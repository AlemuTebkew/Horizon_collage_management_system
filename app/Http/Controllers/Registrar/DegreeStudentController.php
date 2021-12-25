<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\DegreeResult\CourseResultResource;
use App\Http\Resources\DegreeStudentResource;
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
        $academic_year=AcademicYear::where('status',1)->first();

        /**
         * =>function($query) use($academic_year){
         *  $query->when(request('academic_year_id'),function($query){
          *       return   $query->where('academic_year_id',request('academic_year_id'));
          * },function($query) use($academic_year){
           *return  $query->where('academic_year_id',$academic_year);
        *});
        *}
         */
        // return DegreeStudent::with(['semesters','degree_department','program'])->get();

        return Semester::with(['degree_students']) ->get();


//         return Destination::addSelect(['last_flight' => Flight::select('name')
//     ->whereColumn('destination_id', 'destinations.id')
//     ->orderByDesc('arrived_at')
//     ->limit(1)
// ])->get();
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
                'maritial_status'=>'required',
                'emergency_contact_name'=>'required',

            ]);
            $academic_year=AcademicYear::where('status',1)->first();
            $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');



           $active_semester=Semester::where('academic_year_id',$academic_year->id)
                            ->where('status',1)
                            ->where('program_id',$request->program_id)->first();

            $birth_address=Address::create($request->birth_address);
            $residential_address=Address::create($request->residential_address);
            $emergency_address=Address::create($request->emergency_address);

            $data=$request->all();
            $data['birth_address_id']=$birth_address->id;
            $data['residential_address_id']=$residential_address->id;
            $data['emergency_address_id']=$emergency_address->id;
            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
             $data['current_semester_no']=$request->semester_no;
             $data['current_year_no']=$request->year_no;
            $data['dob']=date('Y-m-d',strtotime($request->dob));
            $student= DegreeStudent::create($data);

         //   return $student;
            $student->semesters()->attach($active_semester->id,
            [
                'year_no'=>$request->year_no,
                'semester_no'=>$request->semester_no,
                'partial_scholarship'=>$request->partial_scholarship,
                'status'=>'waiting'

            ]);


           foreach ($month_id as $id) {

             $student->month_payments()->attach($id,[
                'academic_year_id'=>$academic_year->id,

            ]);
            }


            DB::commit();
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            return response()->json(['can t create student'],500);
        }


       return $student->load('semesters');

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
        $request->validate([
            'student_id'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'martial_status'=>'required',
            'emergency_contact_name'=>'required',

        ]);
       $degreeStudent->update($request->all());
       return $degreeStudent;
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
            return response()->json(['succesfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }

    public function registerStudentForSemester(Request $request){
      $student=DegreeStudent::find($request->student_id);
    //   $semester=Semester::find($request->semester_id);
      $student->semesters()->attach($request->semester_id,
      [
        'year_no'=>$request->year_no,
        'semester_no'=>$request->semester_no,
        'partial_scholarship'=>$request->partial_scholarship,
        'status'=>'waiting'
      ]);
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


        //  return  $students= DegreeStudent::
        //                               where('degree_department_id',$department->id)

        //                               ->where('current_year_no',$section->year_no)
        //                               ->where('current_semester_no',$section->semester_no)
        //                                ->with('degree_department','program')->get();

 }

        public function getArrangedStudents(){
            $academic_year=AcademicYear::find(request('academic_year_id'));
            // $students=DegreeStudent::where('degree_department_id',$department_id)->get();

            // foreach ($students as $student) {

            //     foreach ($student->semesters as  $semester) {


            //         $semester['semester_no']=$semester->number;
            //         $semester['students']=null;

            //         foreach ($seme as $key => $value) {
            //             # code...
            //         }


               //  }
               $semesters=[];
               $all=[];
               $semesters1=Semester::with('degree_students')->get();
                 for ($i=1; $i <=3 ; $i++) {
                    // $semester=$semesters1[$i];
                    $students=[];
                    foreach ($semesters1 as $semester) {

                        foreach ($semester->degree_students as $s) {

                            if ($i == $s->pivot->semester_no) {

                                $student['id']=$s->id;
                                $student['first_name']=$s->first_name;
                                $student['year_no']=$s->pivot->year_no;

                                // break;

                                 $students[]=$student;
                            }
                        }


                    }

                    $semesters['semester_no']=$semester->number;
                    $semesters['students']=$students;
                    $all[]=$semesters;

            }
        return response()->json(['semesters'=> $all],200);
      }

    }
