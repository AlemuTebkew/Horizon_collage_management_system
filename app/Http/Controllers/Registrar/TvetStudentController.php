<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddedLevelResource;
use App\Http\Resources\Module\ModuleResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentLevelResource;
use App\Http\Resources\TvetResult\ModuleResultResource;
use App\Models\AcademicYear;
use App\Models\Address;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Level;
use App\Models\Module;
use App\Models\Month;
use App\Models\Program;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use App\Models\UserLogin;
use App\Notifications\UnApprovedStudents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class TvetStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TvetStudent::with('tvet_department','program','month_payments')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public $id=0;
    public function store(Request $request)
    {

        // DB::beginTransaction();

        // try {

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'middle_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required|unique:tvet_students',

        ]);

        $st=TvetStudent::where('phone_no',$request->phone_no)->first();


        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }
        $academic_year=AcademicYear::find($academic_year_id);

       // $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

        $level=Level::find($request->level_id);
        $birth_address=Address::create($request->birth_address);
        $residential_address=Address::create($request->residential_address);
        $emergency_address=Address::create($request->emergency_address);

        $data=$request->all();
        $data['last_name']=$request->middle_name;
        $data['middle_name']=$request->last_name;
        $data['birth_address_id']=$birth_address->id;
        $data['residential_address_id']=$residential_address->id;
        $data['emergency_address_id']=$emergency_address->id;
        $data['password']=Hash::make('HR'.$request->last_name);
        $data['batch']=$academic_year->year;
        $data['current_level_no']=$level->level_no;
        $data['dob']=date('Y-m-d',strtotime($request->dob));

       $student= TvetStudent::create($data);

       $id=$this->generateStudentId($student);
       $student->update(['student_id'=>$id]);

        //////////start user login info
        $login=new UserLogin();
        $login->user_name=$student->student_id;
        $login->password=Hash::make($request->last_name.'1234');
        $login->user_type='tvet_student';
        $login->save();
    ///////////////////////end user login info

    $is_registerd=$student->levels()->wherePivot('level_id',$request->level_id)->first();

    if(!$is_registerd || $is_registerd==null || empty($is_registerd)){
        $student->levels()->attach($level->id,
        [

            'academic_year_id'=>$academic_year->id,
            'status'=>'waiting'
            // 'partial_scholarship'=>$request->partial_scholarship

        ]);
    }else {
        return response()->json('error Already Registerd ',400);
    }
        $this->registerStudentForModules($student,$level);
        $this->attachWithMonth($student,$academic_year);

          //sending notification to registrar for approval of student
            //notifcation to registrar to approve

            $users=Employee::where('role','registrar')->get();
            $info['student_id']=$student->id;
            $info['level_id']=$level->id;
            $info['type']='tvet';
            Notification::send($users,new UnApprovedStudents($info));

        DB::commit();


        return response()->json($this->responseData($student,$level->level_no),201);
    // } catch (\Throwable $e) {
    //     DB::rollBack();
    //     //  return $e;
    //     if ($e instanceof ValidationException) {

    //         return response()->json(['error'=>'Already Registerd'],200);

    //     }
    //     return response()->json(['can t create student'.$e],501);
    // }

    }

    public function generateStudentId($student){
        //    //  ---------start student ID generation

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

       $academic_year=AcademicYear::find($academic_year_id);


      $dep=TvetDepartment::find($student->tvet_department_id);
      $program=Program::find($student->program_id)->name;

      $p_short=null;
      if (str_starts_with($program,'R') || str_starts_with($program,'r')) {
          $p_short='R';
      }elseif (str_starts_with($program,'E') || str_starts_with($program,'e')) {
          $p_short='E';
      }

     $year_short= substr($academic_year->year,2);
      $no_of_st= $dep->tvet_students()->withTrashed()
                                     ->where('batch',$academic_year->year)->count()+1;

      if ($no_of_st <= 9) {
          $student_id='HC'.$p_short.$dep->short_name.'00'.$no_of_st.'/'.$year_short;

      }elseif ($no_of_st <= 99) {
          $student_id='HC'.$p_short.$dep->short_name.'0'.$no_of_st.'/'.$year_short;

      }elseif ($no_of_st <= 999) {
          $student_id='HC'.$p_short.$dep->short_name.$no_of_st.'/'.$year_short;

      }
       //---------end student id generation
      return $student_id;
    }


    private function attachWithMonth($student,$ac){


        $reg= $student->month_payments()->wherePivot('academic_year_id',$ac->id)->first();
        if (!$reg || empty($reg || $reg=null)) {

        $months=Month::pluck('id');

        foreach ($months as $month) {


                $student->month_payments()->attach($month,[
                    'academic_year_id'=>$ac->id,

                ]);


        }
    }



    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function show(TvetStudent $tvetStudent)
    {
       return $tvetStudent->load('levels');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TvetStudent $tvetStudent)
    {
        DB::beginTransaction();

        try {

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'middle_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',

        ]);

        $academic_year_id=null;
        if (request()->has('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }
        $academic_year=AcademicYear::find($academic_year_id);


        $level=Level::find($request->level_id);

        $birth_address= $tvetStudent->birth_address()->update($request->birth_address);
        $residential_address= $tvetStudent->contact_address()->update($request->residential_address);
        $emergency_address=  $tvetStudent->residential_address()->update($request->emergency_address);

        $data=$request->all();

        $data['batch']=$academic_year->year;
        $data['current_level_no']=$level->level_no;
        $data['dob']=date('Y-m-d',strtotime($request->dob));
      //  $data['student_id']='HR'.$academic_year->year.$tvetStudent->id;


        $tvetStudent->update($data);

        //////////start user login info
        $login= UserLogin::where('user_name',$tvetStudent->student_id)->first();
        if (!$login) {
            $login=new UserLogin();
        }
        $login->user_name=$tvetStudent->student_id;
        $login->password=Hash::make($request->last_name.'1234');
        $login->user_type='tvet_student';
        $login->save();
        ///////////////////////end user login info

        $is_reg=$tvetStudent->levels()->wherePivot('level_id',$level->id)->first();
        if ($is_reg) {
            $tvetStudent->levels()->wherePivot('level_id',$level->id)->detach();
        }

        $tvetStudent->levels()->attach($level->id,
        [

            'academic_year_id'=>$academic_year->id,
            'status'=>'waiting'
            // 'partial_scholarship'=>$request->partial_scholarship

        ]);

        $modules=$tvetStudent->modules()->wherePivot('level_id',$level->level_id)->get();

      if ($modules) {
        $tvetStudent->modules()->wherePivot('level_id',$level->level_id)->detach();
      }
     $this->registerStudentForModules($tvetStudent,$level);
        DB::commit();
        return response()->json($this->responseData($tvetStudent,$level->level_no),200);

    } catch (\Exception $e) {
        DB::rollBack();
        return $e;
        return response()->json(['can t create student'],501);
    }

    }



    private function responseData($s,$level_no){
        $level=[];
        $student=[];


        $student['id']=$s->id;
        $student['student_id']=$s->student_id;
        $student['first_name']=$s->first_name;
        $student['last_name']=$s->last_name;
        $student['sex']=$s->sex;
        $student['program']=$s->program;
        $student['status']='waiting';
        $student['department']['id']=$s->tvet_department->id;
        $student['department']['name']=$s->tvet_department->name;


        $level['level_no']=$level_no;
        $level['students']=$student;

        return $level;

       }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetStudent $tvetStudent)
    {
        DB::beginTransaction();

        try {
            $level=  $tvetStudent->levels()->wherePivot('level_id',request('level_id'))->first();
            if ( ($tvetStudent->current_level_no == 1)  && ($level->pivot->status == 'waiting' )) {

                $tvetStudent->month_payments()->detach();
                $tvetStudent->levels()->where('levels.id',request('level_id'))->detach();
                $tvetStudent->modules()->detach();
                $tvetStudent->tvet_sections()->detach();
                UserLogin::where('user_name',$tvetStudent->student_id)->first()->delete();
                $tvetStudent->delete();

                DB::commit();
                return response()->json('succesfully deleted',200);
            }else{
                return response()->json('not possible to deleted',500);

            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }

    private function registerStudentForModules($student,$level){

        // $student=DegreeStudent::find($student_id);
        // $semester=Semester::find($semester_id);

        $department=TvetDepartment::find($student->tvet_department_id);
       $modules=$department->modules()->where('level_id',$level->id)->pluck('id');

         $student->modules()->attach($modules,['level_id'=>$level->id]);
    }

    public function registerStudentForLevel(Request $request){

        DB::beginTransaction();
        try {
        $level=Level::find($request->level_id);
        $student= TvetStudent::find($request->student_id);
        $academic_year=AcademicYear::find($request->academic_year_id);

        $is_registerd=$student->levels()->wherePivot('level_id',$level->id)->first();
        if(!$is_registerd || $is_registerd==null || empty($is_registerd )){
            $student->levels()->attach($request->level_id,
            [

                'academic_year_id'=>$request->academic_year_id,
                'status'=>'waiting'

            ]);
            $student->current_level_no=$level->level_no;
            $student->save();
           }else {
            return response()->json(['error' =>'Student Already Registerd '],200);
           }
           $this->registerStudentForModules($student,$level);
           $this->attachWithMonth($student,$academic_year);

           //sending notification to registrar for approval of student

            $users=Employee::where('role','registrar')->get();
            $info['student_id']=$student->id;
            $info['level_id']=$level->id;
            $info['type']='tvet';
            Notification::send($users,new UnApprovedStudents($info));

            DB::commit();
            return response()->json(new AddedLevelResource($student->levels()
                                         ->wherePivot('level_id',$level->id)->first()),201);
            //  return response()->json(new AddedLevelResource($student),201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully registerd'.$e],500);
           }

      }

   public function updateStudentForLevel(Request $request,$id){

    DB::beginTransaction();
    try {

        if (request('old_level_id') == request('level_id')) {
            return response()->json(['error' =>'Student Already Registerd '],200);

         }else {

            $level=Level::find($request->level_id);
            $student= TvetStudent::find($id);
            $academic_year=AcademicYear::find($request->academic_year_id);

            $is_registerd=$student->levels()->wherePivot('level_id',$level->id)->first();
            if(!$is_registerd || $is_registerd==null || empty($is_registerd )){
                $student->levels()->attach($request->level_id,
                [
                    'academic_year_id'=>$request->academic_year_id,
                    'status'=>'waiting'
                ]);
                $student->current_level_no=$level->level_no;
                $student->save();
        }else {
            return response()->json(['error' =>'Student Already Registerd '],200);
        }
        //  $old_level=Level::find($request->old_level_id);

        $succ= DB::table('notifications')->whereJsonContains('data',['student_id'=>$id])
                                ->whereJsonContains('data',['level_id'=>$request->old_level_id])->delete();

        $student->levels()->wherePivot('level_id',$request->old_level_id)->detach();
        $student->modules()->wherePivot('level_id',$request->old_level_id)->detach();

        $this->registerStudentForModules($student,$level);
        $this->attachWithMonth($student,$academic_year);

        //sending notification to registrar for approval of student

        $users=Employee::where('role','registrar')->get();
        $info['student_id']=$student->id;
        $info['level_id']=$level->id;
        $info['type']='tvet';
        Notification::send($users,new UnApprovedStudents($info));

        DB::commit();
        return response()->json(new AddedLevelResource($student->levels()
                                    ->wherePivot('level_id',$level->id)->first()),201);
            //  return response()->json(new AddedLevelResource($student),201);

    }
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['not succesfully registerd'.$e],500);
       }
   }

      public function getStudentLevels($tvetStudent_id){
        $tvetStudent= TvetStudent::find($tvetStudent_id);

        $student['id']=$tvetStudent->id;
        $student['name']=$tvetStudent->fullName;
        $student['student_id']=$tvetStudent->student_id;
        $student['sex']=$tvetStudent->sex;
        $student['department']=$tvetStudent->tvet_department
            ->makeHidden('created_at','updated_at','department_head_id','sector');
        $student['current_level_no']=$tvetStudent->current_level_no;
        foreach ($tvetStudent->levels as $level) {

            $one_level['id']=$level->id;
            $one_level['year']=AcademicYear::find($level->pivot->academic_year_id)->year;
            $one_level['academic_year_id']=$level->pivot->academic_year_id;
            $one_level['status']=$level->pivot->status;
            $one_level['level_no']=$level->level_no;
            $one_level['is_allowed_now']=DB::table('dynamic_system_settings')->first()->tvet_registrar_result_entry_time;
                            //check for student tuition fee
            if ($level->pivot->legible) {
                $one_level['legible']=$level->pivot->legible;
            }else {
                $m_name=(new Carbon())->monthName;
                $un_paid=  $tvetStudent->month_payments()->wherePivot('academic_year_id',$level->pivot->academic_year_id)
                                        ->wherePivot('receipt_no',null)
                                        ->where('months.name',$m_name)->first();

                if (!$un_paid) {
                    $tvetStudent->levels()->updateExistingPivot($level->id,
                    [
                        'legible'=>1,

                    ]);

                    $one_level['legible']=1;

                }else {
                    $tvetStudent->levels()->updateExistingPivot($level->id,
                    [
                        'legible'=>0,

                    ]);
                    $one_level['legible']=0;
                }
            }
            $student['levels'][]=$one_level;
        }
     return $student;
    // return new StudentLevelResource($tvetStudent->load('levels'));
    }

    public function getStudentLevelModules($id){

        $student=TvetStudent::find($id);
        $department=TvetDepartment::find($student->tvet_department_id);
        $modules=$student->modules()
                            ->wherePivot('level_id',request('level_id'))
                            ->get();
        return response()->json( ModuleResultResource::collection($modules->load('department','program')),200);
    }

    public function giveModuleResult($id){
        $student=TvetStudent::find($id);
        $module=Module::find(request('id'));

        $ff=$student->levels()->wherePivot('level_id' ,request('level_id'))->first();

        if ($ff) {
           if (!$ff->pivot->legible) {

            return response()->json('Illigble !!! Fee Isue  ',400);

           }
        }

        $is_allowed_now=  DB::table('dynamic_system_settings')->first('tvet_teacher_result_entry_time');

        if (! $is_allowed_now) {
          return response()->json('Illigble !!! Not Result Entry Time  ',400);

        }

             $student->modules()->updateExistingPivot(request('id'),[
                'from_20'=>request('from_20'),
                'from_30'=>request('from_30'),
                'from_50'=>request('from_50'),
                'total_mark'=>request('total_mark'),

             ]);

             $level_id=request('level_id');
             $sem_couses_count=$student->modules()
             ->wherePivot('level_id',$level_id)
             ->where(function($q){
                 $q->where('from_20','')
                 ->orWhere('from_30','')
                 ->orWhere('from_50','');
             })->count();
                       
            if ($sem_couses_count == 0) {
            $student->levels()->updateExistingPivot($level_id,[
            'status'=>'finished',
            ]);
            }

    }
    public function getArrangedStudents(){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        $level_no=request('level_no') ? :1;
           $levels=[];
           $all=null;
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $levels_=Level::with(['tvet_students'=>function($query) use($academic_year_id) {
                     $query->where('tvet_student_level.academic_year_id',$academic_year_id);
                  }])
                //   ->where('level_no',$level_no)

        //    ->where('academic_year_id',$academic_year_id)
        ->get();
            //  for ($i=1; $i <=5 ; $i++) {
                // $semester=$levels[$i];
                $no=null;
                $students=[];
                foreach ($levels_ as $level) {
                        $tvet_students=$level->tvet_students()
                        ->wherePivot('academic_year_id',$academic_year_id)
                        ->get();

                    foreach ($tvet_students as $s) {

                        if ($level_no == $level->level_no) {

                            $no=$level->level_no;
                            $student['legible']=$s->pivot->legible;
                            $student['level_id']=$level->id;
                            $student['id']=$s->id;
                            $student['student_id']=$s->student_id;
                            $student['first_name']=$s->first_name;
                            $student['last_name']=$s->last_name;
                            $student['sex']=$s->sex;
                            $student['program']=$s->program;
                            $student['status']=$s->pivot->status;
                            $student['department']['id']=$s->tvet_department->id;
                            $student['department']['name']=$s->tvet_department->name;
                            $dep=TvetDepartment::find($s->tvet_department->id);
                            // return $dep->programs->where('pivot.program_id',$s->program_id)
                            // ->first()->pivot->no_of_year;

                            if ($s->fully_scholarship) {
                                $student['scholarship']='fully';
                            }else{
                                $student['scholarship']='none';
                            }

                           if ($dep->programs) {
                               $a= $dep
                                ->programs()
                                ->where('program_id',$s->program->id)->first();
                                   if ($a) {
                                    $student['department']['no_of_level']=$a->pivot->no_of_level;
                                   }else{
                                    $student['department']['no_of_level']=null;
                                   }
                            }
                             $students[]=$student;
                        }
                    }

                }
                  if ($no) {
                    $levels['level_no']=$no;

                    $levels['students']=$students;
                    $all=$levels;
                }
        // }
    return response()->json($all,200);
    }



}
