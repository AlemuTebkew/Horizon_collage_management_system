<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddedSemesterResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\DegreeResult\CourseResultResource;
use App\Http\Resources\DegreeStudentResource;
use App\Http\Resources\Semester\RegisteredSemesterResource;
use App\Http\Resources\StudentSemesterResource;
use App\Models\AcademicYear;
use App\Models\Address;
use App\Models\Course;
use App\Models\DegreeDepartment;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Month;
use App\Models\Program;
use App\Models\Semester;
use App\Models\UserLogin;
use App\Notifications\UnApprovedStudents;
use App\Traits\Reusable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class DegreeStudentController extends Controller
{
    use Reusable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //getting all students
        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        return DegreeStudent::with(['semesters'=>function($query) use($academic_year_id){
                $query->where('semesters.academic_year_id',$academic_year_id);
        },'degree_department','program'])

                             ->get();


     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'sex'=>'required',
                'dob'=>'required',
                'phone_no'=>'required|unique:degree_students',

            ]);

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

            if ($request->filled('semester_id')) {
                 $semester_id=$request->semester_id;

            }else {
                $semester_id=Semester::where('academic_year_id',$academic_year_id)
                ->where('is_current',1)
                ->where('program_id',$request->program_id)->first()->id;
            }

           $academic_year=AcademicYear::find($academic_year_id);
           $cpf_id=FeeType::where('name','CP Fee')->first()->id;
           $cp_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$cpf_id)->first()->pivot->amount;

           $semester=Semester::find($semester_id);
            //$month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

            $birth_address=Address::create($request->birth_address);
            $residential_address=Address::create($request->residential_address);
            $emergency_address=Address::create($request->emergency_address);

            $data=$request->all();
     //name reversing problems here
            $data['last_name']=$request->middle_name;
            $data['middle_name']=$request->last_name;

            $data['birth_address_id']=$birth_address->id;
            $data['residential_address_id']=$residential_address->id;
            $data['emergency_address_id']=$emergency_address->id;
            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
             $data['current_semester_no']=$semester->number;
             $data['current_year_no']=$request->year_no;
            $data['dob']=date('Y-m-d',strtotime($request->dob));


            $student= DegreeStudent::create($data);
            $generated_id=$this->generateStudentId($student);
            $student->update(['student_id'=>$generated_id]);

             //////////start user login info
             $login=new UserLogin();
             $login->user_name=$student->student_id;
             $login->password=Hash::make($request->last_name.'1234');
             $login->user_type='degree_student';
             $login->save();
            ///////////////////////end user login info

            $is_registerd=$student->semesters()->wherePivot('semester_id',$request->semester_id)->first();

            if(!$is_registerd || $is_registerd==null || empty($is_registerd)){
                 $student->semesters()->attach($request->semester_id,
                 [
                 'year_no'=>$request->year_no,
                 'semester_no'=>$semester->number,
                 'academic_year_id'=>$academic_year_id,
                 //'partial_scholarship'=>$request->partial_scholarship,
                 'status'=>'waiting'
                 ]);
            //  return $student->semesters;
           } else if($is_registerd){
              //  DB::rollBack();
                return response()->json('error Already Registerd ',400);
             }

          $total_cp= $this->registerStudentForCourses($student,$semester,$request->year_no);
          $monthly_cp_fee=$total_cp*$cp_fee/$semester->months->count();
          $student->semesters()->updateExistingPivot($request->semester_id,[
            'semester_credit_hour'=>$total_cp,
            'monthly_cp_fee'=>$monthly_cp_fee

          ]);

          $this->attachWithMonth($student,$academic_year);

          //sending approval notification to registrar
          $users=Employee::where('role','registrar')->get();

          $info['student_id']=$student->id;
          $info['semester_id']=$semester->id;
          $info['type']='degree';
          Notification::send($users,new UnApprovedStudents($info));

           DB::commit();
            return response()->json($this->responseData($student,$semester->number,$request->year_no),201);
        } catch (\Exception $e) {
            DB::rollBack();
            //   return $e;
            if ($e instanceof ValidationException) {

                return response()->json(['error'=>'Already Registerd'],200);

            }
            return response()->json(['can t create student'.$e],400);
        }

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


      $dep=DegreeDepartment::find($student->degree_department_id);
      $program=Program::find($student->program_id)->name;

      $p_short=null;
      if (str_starts_with($program,'R') || str_starts_with($program,'r')) {
          $p_short='R';
      }elseif (str_starts_with($program,'E') || str_starts_with($program,'e')) {
          $p_short='E';
      }

     $year_short= substr($academic_year->year,2);
      $no_of_st= $dep->degree_students()->withTrashed()
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

    private function registerStudentForCourses($student,$semester,$year_no){


        // return $semester->id;
        // $student=DegreeStudent::find($student_id);
        // $semester=Semester::find($semester_id);
         $department=DegreeDepartment::find($student->degree_department_id);
         $courses=$department->courses()
                    ->where('semester_no',$semester->number)
                    ->where('year_no',$year_no)
                    ->where('program_id',$student->program->id)
                    ->get();
         $course_ids=$courses->pluck('id');
         $cp=$courses->sum('cp');
         //$department->courses()->where('semester_no',$semester->number)->sum('cp')
         $student->courses()->attach($course_ids,['semester_id'=>$semester->id]);
         return $cp;
    }
    private function responseData($s,$s_no,$y_no){
     $semester=[];
     $student=[];


     $student['id']=$s->id;
     $student['student_id']=$s->student_id;
     $student['first_name']=$s->first_name;
     $student['last_name']=$s->last_name;
     $student['sex']=$s->sex;
     $student['year_no']=$s_no;
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

     $semester['semester_no']=$s_no;
     $semester['students']=$student;

     return $semester;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeStudent $degreeStudent)
    {
       return $degreeStudent->load('semesters.months');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeStudent $degreeStudent)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                // 'first_name'=>'required',
                // 'last_name'=>'required',
                // 'sex'=>'required',
                // 'phone_no'=>'required',
                // 'dob'=>'required',

            ]);
            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

            // if ($request->filled('semester_id')) {
            //      $semester_id=$request->semester_id;
            // }else {
            //     $semester_id=Semester::where('academic_year_id',$academic_year_id)
            //     ->where('is_current',1)
            //     ->where('program_id',$request->program_id)->first()->id;
            // }

           $academic_year=AcademicYear::find($academic_year_id);
//           $semester=Semester::find($semester_id);
            // $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

            $degreeStudent->birth_address()->update($request->birth_address);
            $degreeStudent->residential_address()->update($request->residential_address);
            $degreeStudent->emergency_address()->update($request->emergency_address);


            $data=$request->all();

            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
            //  $data['current_semester_no']=$semester->number;
             $data['current_year_no']=1;
            $data['dob']=date('Y-m-d',strtotime($request->dob));

        //    //  ---------start student ID generation
        //     $dep=DegreeDepartment::find($request->degree_department_id);
        //     $program=Program::find($request->program_id)->name;

        //     $p_short=null;
        //     if (str_starts_with($program,'R') || str_starts_with($program,'r')) {
        //         $p_short='R';
        //     }elseif (str_starts_with($program,'E') || str_starts_with($program,'e')) {
        //         $p_short='E';
        //     }

        //    $year_short= substr($academic_year->year,2);
        //     $no_of_st= $dep->degree_students()->withTrashed()
        //                                    ->where('batch',$academic_year->year)->count()+1;

        //     if ($no_of_st <= 9) {
        //         $student_id='HC'.$p_short.$dep->short_name.'00'.$no_of_st.'/'.$year_short;

        //     }elseif ($no_of_st <= 99) {
        //         $student_id='HC'.$p_short.$dep->short_name.'0'.$no_of_st.'/'.$year_short;

        //     }elseif ($no_of_st <= 999) {
        //         $student_id='HC'.$p_short.$dep->short_name.$no_of_st.'/'.$year_short;

        //     }
        // //---------end student id generation
        //     $data['student_id']=$student_id;
            $degreeStudent->update($data);

                       //////////start user login info
                      $login= UserLogin::where('user_name',$degreeStudent->student_id)->first();
                       if (!$login) {
                         $login=new UserLogin();
                       }
                       $login->user_name=$degreeStudent->student_id;
                       $login->password=Hash::make($request->last_name.'1234');
                       $login->user_type='degree_student';
                       $login->save();
                      ///////////////////////end user login info

            // $is_registerd =$degreeStudent->semesters()->wherePivot('semester_id',$semester->id)->first();

            // if($is_registerd){
            //     $degreeStudent->semesters()->wherePivot('semester_id',$semester->id)->detach();
            //      }
            //      $degreeStudent->semesters()->attach($semester->id,
            //      [
            //      'year_no'=>$request->year_no,
            //      'semester_no'=>$semester->number,
            //      'academic_year_id'=>$academic_year_id,
            //      //'partial_scholarship'=>$request->partial_scholarship,
            //      'status'=>'waiting'
            //      ]);

            //  $courses=$degreeStudent->courses()->wherePivot('semester_id',$semester->id)->get();
            //  if ($courses) {
            //     $degreeStudent->courses()->wherePivot('semester_id',$semester->id)->detach();
            //  }
            //  $total_cp= $this->registerStudentForCourses($degreeStudent,$semester,$request->year_no);
            //  $this->attachWithMonth($degreeStudent,$academic_year);


            DB::commit();
            return response()->json('saved',200);
            // return response()->json($this->responseData($degreeStudent,$semester->number,$request->year_no),200);


        } catch (\Exception $e) {
            DB::rollBack();
         //  return $e;
            return response()->json(['can t create student'.$e],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeStudent $degreeStudent)
    {

        DB::beginTransaction();

        try {
            // $degreeStudent->birth_address()->delete();
            // $degreeStudent->contact_address()->delete();
            // $degreeStudent->emergency_address()->delete();
            $seme=  $degreeStudent->semesters()->wherePivot('semester_id',request('semester_id'))->first();
            if ( ($degreeStudent->current_year_no == 1) && ($degreeStudent->current_semester_no == 1) && ($seme->pivot->status == 'waiting' )) {

                $degreeStudent->month_payments()->detach();
                $degreeStudent->semesters()->where('semesters.id',request('semester_id'))->detach();
                $degreeStudent->courses()->detach();
                $degreeStudent->degree_sections()->detach();
                UserLogin::where('user_name',$degreeStudent->student_id)->first()->delete();
                $degreeStudent->delete();

                DB::commit();
                return response()->json('succesfully deleted',200);
            }else{

            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('not succesfully deleted'.$e,500);

        }
    }

    public function registerStudentForSemester(Request $request){


        DB::beginTransaction();
        try {
        $student=DegreeStudent::find($request->student_id);
        $academic_year=AcademicYear::find($request->academic_year_id);

        $cpf_id=FeeType::where('name','CP Fee')->first()->id;
        $cp_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$cpf_id)->first()->pivot->amount;

        $semester=Semester::find($request->semester_id);
        $semester_no=$semester->number;
        $is_registerd =$student->semesters()->wherePivot('semester_id',$request->semester_id)->first();

        if(!$is_registerd || $is_registerd==null || empty($is_registerd )){
            $student->semesters()->attach($request->semester_id,
            [
            'year_no'=>$request->year_no,
            'semester_no'=>$semester_no,
            'academic_year_id'=>$request->academic_year_id,
            //'partial_scholarship'=>$request->partial_scholarship,
            'status'=>'waiting'
            ]);
            $student->current_year_no=$request->year_no;
            $student->current_semester_no=$semester_no;
            $student->save();
         //   return new RegisteredSemesterResource($semester->load('academic_year'));

        } else if($is_registerd ){
            return response()->json(['error'=> 'Student Already Registerd '],200);
            }

            $total_cp= $this->registerStudentForCourses($student,$semester,$request->year_no);
            $monthly_cp_fee=$total_cp*$cp_fee/$semester->months->count();
            $student->semesters()->updateExistingPivot($request->semester_id,[
              'semester_credit_hour'=>$total_cp,
              'monthly_cp_fee'=>$monthly_cp_fee

            ]);
             $this->attachWithMonth($student,$academic_year);

            //notifcation to registrar to approve

            $users=Employee::where('role','registrar')->get();
            $info['student_id']=$student->id;
            $info['semester_id']=$semester->id;
            $info['type']='degree';
            Notification::send($users,new UnApprovedStudents($info));

             DB::commit();
            return response()->json(new AddedSemesterResource($student->semesters()
            ->wherePivot('semester_id',$semester->id)->first() ),201);
            } catch (\Exception $e) {
                DB::rollBack();
              // return $e;
                return response()->json(['not succesfully registerd'],500);
               }

    }


    public function updateStudentForSemester(Request $request,$id){


        DB::beginTransaction();
        try {
            $student=DegreeStudent::find($id);
          $seme_check=$student->semesters()->wherePivot('semester_id',$request->old_semester_id)->first();
           if ($seme_check) {
               if ($seme_check->pivot->status == 'waiting') {
                return response()->json(['error' =>'This Semester can\'t be updated'],200);

               }
           }

            if (request('old_year_no') == request('year_no')) {
                if (request('old_semester_id') == request('semester_id')) {
                    return response()->json(['error' =>'Student Already Registerd '],200);

                 }
            }

            $academic_year=AcademicYear::find($request->academic_year_id);

            $cpf_id=FeeType::where('name','CP Fee')->first()->id;
            $cp_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$cpf_id)->first()->pivot->amount;

            $semester=Semester::find($request->semester_id);
            $semester_no=$semester->number;

            //detach student from old semester

            $student->semesters()->wherePivot('semester_id',$request->old_semester_id)->detach();
            $student->courses()->wherePivot('semester_id',$request->old_semester_id)->detach();
            $succ= DB::table('notifications')->whereJsonContains('data',['student_id'=>$id])
                                ->whereJsonContains('data',['semester_id'=>request('old_semester_id')])->delete();
               ///  ---------end detach
            $is_registerd =$student->semesters()->wherePivot('semester_id',$request->semester_id)->first();
            if(!$is_registerd || $is_registerd==null || empty($is_registerd )){
                $student->semesters()->attach($request->semester_id,
                [
                'year_no'=>$request->year_no,
                'semester_no'=>$semester_no,
                'academic_year_id'=>$request->academic_year_id,
                //'partial_scholarship'=>$request->partial_scholarship,
                'status'=>'waiting'
                ]);
                $student->current_year_no=$request->year_no;
                $student->current_semester_no=$semester_no;
                $student->save();
            //   return new RegisteredSemesterResource($semester->load('academic_year'));

            } else if($is_registerd ){
                return response()->json(['error'=> 'Student Already Registerd '],200);
                }


                $total_cp= $this->registerStudentForCourses($student,$semester,$request->year_no);
                $monthly_cp_fee=$total_cp*$cp_fee/$semester->months->count();
                $student->semesters()->updateExistingPivot($request->semester_id,[
                'semester_credit_hour'=>$total_cp,
                'monthly_cp_fee'=>$monthly_cp_fee

                ]);
                $this->attachWithMonth($student,$academic_year);

                //notifcation to registrar to approve

                $users=Employee::where('role','registrar')->get();
                $info['student_id']=$student->id;
                $info['semester_id']=$semester->id;
                $info['type']='degree';
                Notification::send($users,new UnApprovedStudents($info));

                DB::commit();
                return response()->json(new AddedSemesterResource($student->semesters()
                ->wherePivot('semester_id',$semester->id)->first() ),201);


            } catch (\Exception $e) {
                DB::rollBack();
              // return $e;
                return response()->json(['not succesfully registerd'],500);
               }

    }


    public function getStudentSemesters($degreeStudent_id){
        $degreeStudent= DegreeStudent::find($degreeStudent_id);
        return response()->json( new StudentSemesterResource($degreeStudent->load('semesters')),200);
    }

    public function getStudentSemesterCourses($id){

        $student=DegreeStudent::find($id);
        $department=DegreeDepartment::find($student->degree_department_id);
        $courses=$student->courses()
                            ->wherePivot('semester_id',request('semester_id'))
                           ->get();
        return response()->json( CourseResultResource::collection($courses->load('department','program')),200);
    }

    public function giveCourseResult($id){
        $student=DegreeStudent::find($id);
         $sem_id=request('semester_id');
         $semester=Semester::find($sem_id);
         $course=Course::find(request('id'));

          if ($semester->is_closed) {
            return response()->json('Result Entry on closed semester is not allowed!!  ',400);
         }
         $ff=$student->semesters()->wherePivot('semester_id' ,$sem_id)->first();
               if ($ff) {
                  if (!$ff->pivot->legible) {
                   return response()->json('Illigble !!! Fee Isue  ',400);
                  }
               }
             $is_allowed_now=  DB::table('dynamic_system_settings')->first('degree_teacher_result_entry_time');
             if (! $is_allowed_now) {
               return response()->json('Illigble !!! Not Result Entry Time  ',400);
             }



               $letter_grade=$this->calculateLetterGrade(request()->total_mark);
               $grade_point=$this->courseGradePoint($course->cp,$letter_grade);
               $student->courses()->updateExistingPivot(request('id'),[

                   'from_5'=>request('from_5'),
                   'from_5s'=>request('from_5s'),
                   'from_25'=>request('from_25'),
                   'from_25s'=>request('from_25s'),
                   'from_40'=>request('from_40'),
                   'total_mark'=>request('total_mark'),
                   'letter_grade'=> $letter_grade,
                   'grade_point'=> $grade_point

                ]);
                $all=$this->calculateGPA($student,$sem_id);
                $new_gpa=$all[0];
                $semester_grade_point=$all[1];

                 if ($new_gpa == 0.0 || $new_gpa == 0) {
                       $student->semesters()->updateExistingPivot($sem_id,[
                           'semester_GPA'=>0.0,
                       ]);
                   }else {
                       $student->semesters()->updateExistingPivot($sem_id,[
                           'semester_GPA'=>$new_gpa,
                           'semester_grade_point'=> $semester_grade_point

                       ]);
                   }

                   $sem_couses_count=$student->courses()->wherePivot('semester_id',$sem_id)
                                          ->wherePivot('letter_grade','')->count();

                   if ($sem_couses_count == 0) {
                       $student->semesters()->updateExistingPivot($sem_id,[
                           'status'=>'finished',
                       ]);
                   }

        return response()->json(['successfull set'],200);
    }
    public function getDegreeStudentsByDepartment(){

            $dep_head=Employee::where('email',request()->user()->user_name)->first();
            $department=$dep_head->manage;
            return  $students= DegreeStudent::where('degree_department_id',$department->id)
                                          ->with('degree_department','program')->get();

    }


        public function getArrangedStudents(){

            if (request('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

            $year_no=request('year_no') ?:1;
               $semesters=[];
               $all=[];
            //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
               $semesters1=Semester::with(['degree_students'=>function($query) {
               }])
               ->where('academic_year_id',$academic_year_id)

                // ->when(request('semester_no'),function($q){
                //     return $q->where('number',request('semester_no'));
                // },function($q){
                //    return $q->where('status',1);
                // })
               ->get();
                 for ($i=1; $i <=3 ; $i++) {
                    // $semester=$semesters1[$i];
                    $no=null;
                    $students=[];
                    foreach ($semesters1 as $semester) {
                            $degree_students=$semester->degree_students()->orderByDesc('created_at')
                            // ->when(request('search_query'),function($query){
                            //     $query->where('student_id',request('search_query'));

                            // })
                            ->wherePivot('year_no',$year_no)
                            ->get();

                        foreach ($degree_students as $s) {

                            // if ($semester_no == $s->pivot->semester_no) {
                                if ($i == $s->pivot->semester_no) {

                                $no=$s->pivot->semester_no;
                                $student['semester_id']=$semester->id;
                                $student['id']=$s->id;
                                $student['student_id']=$s->student_id;
                                $student['first_name']=$s->first_name;
                                $student['last_name']=$s->last_name;
                                $student['sex']=$s->sex;
                                $student['year_no']=$s->pivot->year_no;
                                $student['program']=$s->program;
                                $student['status']=$s->pivot->status;
                                $student['legible']=$s->pivot->legible;
                                $student['department']['id']=$s->degree_department->id;
                                $student['department']['name']=$s->degree_department->name;
                                $dep=DegreeDepartment::find($s->degree_department->id);
                                // return $dep->programs->where('pivot.program_id',$s->program_id)
                                // ->first()->pivot->no_of_year;

                                if ($s->fully_scholarship) {
                                    $student['scholarship']='fully';

                                }else {
                                    $student['scholarship']='none';
                                }


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
                                 $students[]=$student;
                            }
                        }

                    }
                      if ($no) {
                        $semesters['semester_no']=$no;

                        $semesters['students']=$students;
                        $all[]=$semesters;
                    }

            }
        return response()->json($all,200);
      }





    }
