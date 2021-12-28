<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
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

            $is_registerd=$student->semesters()->where('semester_id',$request->semester_id)->first();

            if(!$is_registerd){
                 $student->semesters()->attach($request->semester_id,
                 [
                 'year_no'=>$request->year_no,
                 'semester_no'=>$semester->number,
                 'academic_year_id'=>$academic_year_id,
                 //'partial_scholarship'=>$request->partial_scholarship,
                 'status'=>'waiting'
                 ]);

           } else if($is_registerd){
                return response()->json('error Already Registerd ',400);
             }

           DB::commit();
            return $student->load('semesters');
        } catch (\Exception $e) {
            DB::rollBack();
           return $e->getMessage();
            return response()->json(['can t create student'],400);
        }

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
        // DB::beginTransaction();
        // try {

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
            $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');

           $birth_address= $degreeStudent->birth_address()->update($request->birth_address);
           $residential_address= $degreeStudent->contact_address()->update($request->residential_address);
           $emergency_address=  $degreeStudent->residential_address()->update($request->emergency_address);


            $data=$request->all();
            // $data['birth_address_id']=$birth_address->id;
            // $data['residential_address_id']=$residential_address->id;
            // $data['emergency_address_id']=$emergency_address->id;
            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
             $data['current_semester_no']=$semester->number;
             $data['current_year_no']=$request->year_no;
            $data['dob']=date('Y-m-d',strtotime($request->dob));
            $data['student_id']='HR'.$academic_year->year.$degreeStudent->id;
            $degreeStudent->update($data);

            $is_registerd=$degreeStudent->semesters;

            if($is_registerd){
                 $degreeStudent->semesters()->detach();
                 $degreeStudent->semesters()->attach($request->semester_id,
                 [
                 'year_no'=>$request->year_no,
                 'semester_no'=>$semester->number,
                 'academic_year_id'=>$academic_year_id,
                 //'partial_scholarship'=>$request->partial_scholarship,
                 'status'=>'waiting'
                 ]);

           } else if($is_registerd){
                return response()->json('error Already Registerd ',400);
             }

            // DB::commit();
            return $degreeStudent->load('semesters');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //    return $e;
        //     return response()->json(['can t create student'],500);
        // }
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
        $current_academic_year=AcademicYear::find($request->academic_year_id);
        $semester=Semester::find($request->semester_id);
        $semester_no=$semester->number;
        $is_registerd =$student->semesters()->where('semester_id',$request->semester_id)->first();

        if(!$is_registerd ){
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
            return new RegisteredSemesterResource($semester->load('academic_year'),$request->year_no);

        } else if($is_registerd ){
            return response()->json('error',400);
            }

            DB::commit();
            return response()->json(['succesfully registerd'],200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['not succesfully registerd'.$e],500);

               }

    }



    public function getStudentSemesters($degreeStudent_id){
        $degreeStudent= DegreeStudent::find($degreeStudent_id);
        return new StudentSemesterResource($degreeStudent->load('semesters'));
    }

    public function getStudentSemesterCourses($id){

        $dep_id=DegreeStudent::find($id)->degree_department_id;
        $department=DegreeDepartment::find($dep_id);
        $courses=$department->courses()->where('semester_no',request('semester_no'))->get();
        return response()->json( CourseResultResource::collection($courses->load('department','program')),200);
    }

    public function giveCourseResult(){
        $student=DegreeStudent::find(request('student_id'));

        foreach (request()->courses as $course) {
             $student->attach($course['id'],[
                 'semester_id'=>$course['semester_id'],
                 'total_mark'=>$course['total_mark']

             ]);

        }
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

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

               $semesters=[];
               $all=[];
            //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
               $semesters1=Semester::with(['degree_students'=>function($query) {
                 $query->where('degree_department_id',4);
               }])
               ->where('academic_year_id',$academic_year_id)
               ->get();
                 for ($i=1; $i <=3 ; $i++) {
                    // $semester=$semesters1[$i];
                    $no=null;
                    $students=[];
                    foreach ($semesters1 as $semester) {
                            $degree_students=$semester->degree_students()->where('degree_department_id',4)->get();

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


                                if ($dep->has('programs')) {

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
      public function getSemesterArrangedStudents(Request $request){

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

           $semesters=[];
           $all=[];
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $semesters1=Semester::with(['degree_students'=>function($query) {
             $query->where('degree_department_id',4);
           }])
           ->where('academic_year_id',$academic_year_id)
           ->get();
             for ($i=1; $i <=3 ; $i++) {
                // $semester=$semesters1[$i];
                $no=null;
                $students=[];
                foreach ($semesters1 as $semester) {
                        $degree_students=$semester->degree_students()->where('degree_department_id',4)->get();

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


                            if ($dep->has('programs')) {

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
