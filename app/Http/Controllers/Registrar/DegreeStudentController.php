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
use App\Models\DegreeDepartment;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Month;
use App\Models\Semester;
use App\Models\UserLogin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class DegreeStudentController extends Controller
{
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
                'phone_no'=>'required',

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
           $semester=Semester::find($semester_id);
            //$month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

            $birth_address=Address::create($request->birth_address);
            $residential_address=Address::create($request->residential_address);
            $emergency_address=Address::create($request->emergency_address);

            $data=$request->all();
            $data['birth_address_id']=$birth_address->id;
            $data['residential_address_id']=$residential_address->id;
            $data['emergency_address_id']=$emergency_address->id;
            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
             $data['current_semester_no']=$semester->number;
             $data['current_year_no']=$request->year_no;
            $data['dob']=date('Y-m-d',strtotime($request->dob));
            $student= DegreeStudent::create($data);
            $student->update(['student_id'=>'HR'.$academic_year->year.$student->id]);

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
                DB::rollBack();
                return response()->json('error Already Registerd ',400);
             }

           $this->registerStudentForCourses($student,$semester);
           $this->attachWithMonth($student,$academic_year);
           DB::commit();
            return response()->json($this->responseData($student,$semester->number,$request->year_no),200);
        } catch (\Exception $e) {
            DB::rollBack();
            // return $e;
            return response()->json(['can t create student'],400);
        }

    }

    private function attachWithMonth($student,$ac){


     return   $reg= $student->month_payments()->wherePivot('academic_year_id',$ac->id)->first();
          if (!$reg) {
            $month_ids=$ac->months->pluck('id');
            $student->month_payments()->attach($month_ids,['academic_year_id'=>$ac->id]);
          }




    }

    private function registerStudentForCourses($student,$semester){

        // $student=DegreeStudent::find($student_id);
        // $semester=Semester::find($semester_id);
        $department=DegreeDepartment::find($student->degree_department_id);
        $courses=$department->courses()->where('semester_no',$semester->number)->pluck('id');
       return  $student->courses()->attach($courses,['semester_id'=>$semester->id]);
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
                'first_name'=>'required',
                'last_name'=>'required',
                'sex'=>'required',
                'phone_no'=>'required',
                'dob'=>'required',

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
           $semester=Semester::find($semester_id);
            // $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

           $birth_address= $degreeStudent->birth_address()->update($request->birth_address);
           $residential_address= $degreeStudent->contact_address()->update($request->residential_address);
           $emergency_address=  $degreeStudent->residential_address()->update($request->emergency_address);


            $data=$request->all();

            $data['batch']=$academic_year->year;
             $data['current_semester_no']=$semester->number;
             $data['current_year_no']=$request->year_no;
            $data['dob']=date('Y-m-d',strtotime($request->dob));
            $data['student_id']='HR'.$academic_year->year.$degreeStudent->id;
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

            $is_registerd =$degreeStudent->semesters()->wherePivot('semester_id',$semester->id)->first();

            if($is_registerd){
                $degreeStudent->semesters()->wherePivot('semester_id',$semester->id)->detach();
                 }
                 $degreeStudent->semesters()->attach($semester->id,
                 [
                 'year_no'=>$request->year_no,
                 'semester_no'=>$semester->number,
                 'academic_year_id'=>$academic_year_id,
                 //'partial_scholarship'=>$request->partial_scholarship,
                 'status'=>'waiting'
                 ]);

             $courses=$degreeStudent->courses()->wherePivot('semester_id',$semester->id)->get();
             if ($courses) {
                $degreeStudent->courses()->wherePivot('semester_id',$semester->id)->detach();
             }
                $this->registerStudentForCourses($degreeStudent,$semester);
                $this->attachWithMonth($degreeStudent,$academic_year);


            DB::commit();
            return response()->json($this->responseData($degreeStudent,$semester->number,$request->year_no),200);
        } catch (\Exception $e) {
            DB::rollBack();
         //  return $e;
            return response()->json(['can t create student'],400);
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
            $degreeStudent->birth_address()->delete();
            $degreeStudent->contact_address()->delete();
            $degreeStudent->residential_address()->delete();
            $degreeStudent->delete();

            DB::commit();
            return response()->json(['succesfully deleted'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }

    public function registerStudentForSemester(Request $request){

        DB::beginTransaction();
        try {
        $student=DegreeStudent::find($request->student_id);
        $academic_year=AcademicYear::find($request->academic_year_id);
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
            return response()->json('error Student Already Registerd ',400);
            }

            $this->registerStudentForCourses($student,$semester);
            $this->attachWithMonth($student,$academic_year);

            DB::commit();
            return response()->json(new AddedSemesterResource($student->semesters()
            ->wherePivot('semester_id',$semester->id)->first() ),200);
            } catch (\Exception $e) {
                DB::rollBack();
               // return $e;
                return response()->json(['not succesfully registerd'],500);
               }

    }

    public function addedSemesterStudent(){

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
            // return request()->courses;
        foreach (request()->courses as $course) {
               // return $course['total_mark'];
             $student->courses()->updateExistingPivot($course['id'],[
                 'total_mark'=>$course['total_mark'],
                //  'grade_point'=>$this->courseGradePoint($course->cp,$course->total_mark),

             ]);

        }
        return response()->json(['successfull set'],200);
    }
    public function getDegreeStudentsByDepartment(){


            $dep_head=Employee::where('email',request()->user()->user_name)->first();
            $department=$dep_head->manage;
            return  $students= DegreeStudent::where('degree_department_id',$department->id)
                                          ->with('degree_department','program')->get();
            //  return  $students= DegreeStudent::
            //                               where('degree_department_id',$department->id)
            //                               ->with('degree_department','program')->get();

    }
    public function sectionSuggestedStudents(){

        $dep_head=Employee::where('email',request()->user()->user_name)->first();
        $department=$dep_head->manage;
        $id=request()->section_id;
        $section=DegreeSection::find($id);
     //   return $section;
          $students= DegreeStudent::where('degree_department_id',$department->id)
                                         ->where('current_year_no',$section->year_no)
                                         ->where('current_semester_no',$section->semester_no)
                                         ->whereDoesntHave('degree_sections', function (Builder $query) use($department,$section) {
                                            $query->where('degree_department_id',$department->id)
                                                  ->where('year_no',$section->year_no)
                                                  ->where('semester_no',$section->semester_no)
                                                  ->where('academic_year_id',$section->academic_year_id);

                                        }) ->with('degree_department','program')->get();

 }

        public function getArrangedStudents(){

           //$academic_year_id=null;
        //    return request()->academic_year_id;
            if (request('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

               $semesters=[];
               $all=[];
            //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
               $semesters1=Semester::with(['degree_students'=>function($query) {
               }])
              ->where('academic_year_id',$academic_year_id)
               ->get();
                 for ($i=1; $i <=3 ; $i++) {
                    // $semester=$semesters1[$i];
                    $no=null;
                    $students=[];
                    foreach ($semesters1 as $semester) {
                            $degree_students=$semester->degree_students()
                        //    ->where('academic_year_id',$academic_year_id)
                            ->get();

                        foreach ($degree_students as $s) {

                            if ($i == $s->pivot->semester_no) {

                                $no=$s->pivot->semester_no;
                                $student['id']=$s->id;
                                $student['student_id']=$s->student_id;
                                $student['first_name']=$s->first_name;
                                $student['last_name']=$s->last_name;
                                $student['sex']=$s->sex;
                                $student['year_no']=$s->pivot->year_no;
                                $student['program']=$s->program;
                                $student['status']=$s->pivot->status;
                                $student['department']['id']=$s->degree_department->id;
                                $student['department']['name']=$s->degree_department->name;
                                $dep=DegreeDepartment::find($s->degree_department->id);
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

      public function getArrangedStudentsByDepartment(Request $request){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

           $semesters=[];
           $all=[];
           $employee=Employee::where('email',request()->user()->user_name)->first();
           $dep_id= $employee->manage->id;
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $semesters1=Semester::with(['degree_students'=>function($query) use($dep_id) {
             $query->where('degree_department_id',$dep_id);
           }])
           ->where('academic_year_id',$academic_year_id)
           ->get();
             for ($i=1; $i <=3 ; $i++) {
                // $semester=$semesters1[$i];
                $no=null;
                $students=[];
                foreach ($semesters1 as $semester) {
                        $degree_students=$semester->degree_students()
                       ->where('academic_year_id',$academic_year_id)
                        ->where('degree_department_id',$dep_id)->get();

                    foreach ($degree_students as $s) {

                        if ($i == $s->pivot->semester_no) {

                            $no=$s->pivot->semester_no;
                            $student['id']=$s->id;
                            $student['student_id']=$s->student_id;
                            $student['first_name']=$s->first_name;
                            $student['last_name']=$s->last_name;
                            $student['sex']=$s->sex;
                            $student['year_no']=$s->pivot->year_no;
                            $student['program']=$s->program;
                            $student['status']=$s->pivot->status;
                            $student['department']['id']=$s->degree_department->id;
                            $student['department']['name']=$s->degree_department->name;
                            $dep=DegreeDepartment::find($s->degree_department->id);
                            // return $dep->programs->where('pivot.program_id',$s->program_id)
                            // ->first()->pivot->no_of_year;


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


  public function courseGradePoint($credit_hour,$letter_grade){
    if($letter_grade=='A'|| $letter_grade=='A+'){
      return  $grade_point=$credit_hour*4;
    }
    else if($letter_grade=='A-'){
        return  $grade_point=$credit_hour*3.75;
    }
    else if($letter_grade=='B+'){
        return  $grade_point=$credit_hour*3.5;
    }
    else if($letter_grade=='B'){
        return  $grade_point=$credit_hour*3;
    }
    else if($letter_grade=='B-'){
        return  $grade_point=$credit_hour*2.75;
    }
    else if($letter_grade=='C+'){
        return $grade_point=$credit_hour*2.5;
    }
    else if($letter_grade=='C'){
        return $grade_point=$credit_hour*2;
    }
    else if($letter_grade=='C-'){
        return $grade_point=$credit_hour*1.75;
    }
    else if($letter_grade=='D'){
        return $grade_point=$credit_hour*1;
    }
    else if($letter_grade=='F'){
        return $grade_point=$credit_hour*0;
    }
}
    }
