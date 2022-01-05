<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddedLevelResource;
use App\Http\Resources\Module\ModuleResource;
use App\Http\Resources\StudentLevelResource;
use App\Http\Resources\TvetResult\ModuleResultResource;
use App\Models\AcademicYear;
use App\Models\Address;
use App\Models\DegreeDepartment;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Level;
use App\Models\Month;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $data['birth_address_id']=$birth_address->id;
        $data['residential_address_id']=$residential_address->id;
        $data['emergency_address_id']=$emergency_address->id;
        $data['password']=Hash::make('HR'.$request->last_name);
        $data['batch']=$academic_year->year;
        $data['current_level_no']=$level->level_no;
        $data['dob']=date('Y-m-d',strtotime($request->dob));



        $student= TvetStudent::create($data);
        $student->update(['student_id'=>'HR'.$academic_year->year.$student->id]);


        //////////start user login info
        $login=new UserLogin();
        $login->user_name=$student->student_id;
        $login->password=Hash::make($request->last_name.'1234');
        $login->user_type='tvet_student';
        $login->save();
    ///////////////////////end user login info

        $student->levels()->attach($level->id,
        [

            'academic_year_id'=>$academic_year->id,
            'status'=>'waiting'
            // 'partial_scholarship'=>$request->partial_scholarship

        ]);
        $this->registerStudentForModules($student,$level);

        DB::commit();
        return response()->json($this->responseData($student,$level->level_no),200);
    } catch (\Exception $e) {
        DB::rollBack();
        // return $e;
        return response()->json(['can t create student'],501);
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
        $data['student_id']='HR'.$academic_year->year.$tvetStudent->id;


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
        $student['department']['id']=$s->degree_department->id;
        $student['department']['name']=$s->degree_department->name;
        $dep=DegreeDepartment::find($s->degree_department->id);


        if ($dep->programs) {
           $a= $dep
            ->programs()
            ->where('program_id',$s->program->id)->first();
               if ($a) {
                $student['department']['no_of_year']=$a->pivot->no_of_year;

               }else{
                $student['department']['no_of_year']=null;
               }
        }

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
            $tvetStudent->birth_address()->delete();
            $tvetStudent->contact_address()->delete();
            $tvetStudent->residential_address()->delete();
            $tvetStudent->delete();

            DB::commit();
            return response()->json(['succesfully deleted']);
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

        $is_reg=$student->levels()->wherePivot('level_id',$level->id)->first();
        if (!$is_reg) {
            $student->levels()->attach($request->level_id,
            [

                'academic_year_id'=>$request->academic_year_id,
                'status'=>'waiting'

                // 'scholarship'=>$request->scholarship

            ]);
           }else {
            return response()->json('error Student Already Registerd ',400);
           }
           $this->registerStudentForModules($student,$level);
        DB::commit();
        return response()->json(new AddedLevelResource($student),200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully registerd'.$e],500);
           }

      }

      public function getStudentLevels( $tvetStudent_id){
        $tvetStudent= TvetStudent::find($tvetStudent_id);
        return new StudentLevelResource($tvetStudent->load('levels'));
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
        $student=TvetStudent::find(request($id));

        foreach (request()->modules as $module) {
             $student->modules()->updateExistingPivot($module['id'],[
                 'total_mark'=>$module['total_mark'],

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

           $levels=[];
           $all=[];
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $levels_=Level::with(['tvet_students'=>function($query) use($academic_year_id) {
                     $query->where('tvet_student_level.academic_year_id',$academic_year_id);
                  }])
        //    ->where('academic_year_id',$academic_year_id)
                  ->get();
             for ($i=1; $i <=5 ; $i++) {
                // $semester=$levels[$i];
                $no=null;
                $students=[];
                foreach ($levels_ as $level) {
                        $tvet_students=$level->tvet_students()
                        ->where('academic_year_id',$academic_year_id)
                        ->get();

                    foreach ($tvet_students as $s) {

                        if ($i == $level->level_no) {

                            $no=$level->level_no;
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
                            }else if($s->pivot->partial_scholarship){
                                $student['scholarship']='partial';
                            }else {
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
                    $all[]=$levels;
                }
        }
    return response()->json($all,200);
    }


    public function getArrangedStudentsByDepartment(){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

           $levels=[];
           $all=[];
           $employee=Employee::where('email',request()->user()->user_name)->first();
           $dep_id= $employee->manage->id;
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $levels_=Level::with(['tvet_students'=>function($query) use($dep_id,$academic_year_id) {
              $query->where('tvet_department_id',$dep_id)
                     ->where('tvet_student_level.academic_year_id',$academic_year_id);
             ;
           }])
        //    ->where('academic_year_id',$academic_year_id)
           ->get();
             for ($i=1; $i <=5 ; $i++) {
                // $semester=$levels[$i];
                $no=null;
                $students=[];
                foreach ($levels_ as $level) {
                        $tvet_students=$level->tvet_students()
                        ->where('academic_year_id',$academic_year_id)
                        ->where('tvet_department_id',$dep_id)->get();

                    foreach ($tvet_students as $s) {

                        if ($i == $level->level_no) {

                            $no=$level->level_no;
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

                        // return $dep->programs;
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
                    $all[]=$levels;
                }
        }
    return response()->json($all,200);
    }
}
