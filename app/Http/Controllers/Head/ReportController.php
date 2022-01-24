<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeResult\CourseResultResource;
use App\Http\Resources\DegreeResult\StudentSemesterResource;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\FeeType;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getCourseTakenBySemester(Request $request){

        $employee=Employee::where('email',request()->user()->user_name)->first();
        $department=$employee->manage;
       //  Course::where('degree_department_id',$department->id)->get();
          $course=Course::where('degree_department_id',$department->id)
                            ->where('program_id',$request->program_id)
                            ->where('year_no',$request->year_no)
                            ->where('semester_no',$request->semester_no)->get();
          $fee_type_cp=FeeType::where('name','CP Fee')->first();
          $fee_type_reg=FeeType::where('name','Registration Fee')->first();

        //   $ac_y=AcademicYear::find($request->academic_year_id);
        //   $ac_y->fee_types->
          $cp_fee= $fee_type_cp->academic_years()->wherePivot('academic_year_id',$request->academic_year_id)->first()->pivot->amount;
          $registration_fee= $fee_type_reg->academic_years()->wherePivot('academic_year_id',$request->academic_year_id)->first()->pivot->amount;
        return response([
             'cpFee'=>$cp_fee,
             'registrationFee'=>$registration_fee,
             'course'=>$course,
        ],200
        );
}
    public function getGradeReport(Request $request){

       $employee=Employee::where('email',request()->user()->user_name)->first();

       $all_students=[];
       $student1=[];
       $all_data=[];


       foreach ($request->student_ids as $student_id) {
        $courses=[];
        $course=[];
          $student=DegreeStudent::find($student_id);
          $studentCourses=$student->courses()
                                  ->where('semester_no',request()->semester_no)
                                  ->where('year_no',request()->year_no)
                                  ->get();
          $semester=$student->semesters() ->wherePivot('year_no',request()->year_no)
                                          ->wherePivot('semester_no',request()->semester_no)->first();
         foreach ($studentCourses as $studentCourse) {
            $letter_grade=$studentCourse->pivot->grade_point;
            $grade_point=$this->courseGradePoint($studentCourse->cp,$letter_grade);
            // $course['student_id']=$studentCourse->student_id;
            $course['code']=$studentCourse->code;
            $course['title']=$studentCourse->title;
            $course['semester_no']=$studentCourse->semester_no;
            $course['cp']=$studentCourse->cp;
            $course['letter_grade']=$studentCourse->pivot->letter_grade;
            $course['year_no']=$studentCourse->year_no;
            $course['total_mark']=$studentCourse->pivot->total_mark;
            $course['grade_point']=$studentCourse->pivot->grade_point;



           $courses[]=$course;

         }
        // return $courses;
        $total_credit_hour=$this->totalCreditHour($student);
        $cgpa=$this->calculateCGPA($student);

        $student1['id']=$student->id;
        $student1['student_id']=$student->student_id;
        $student1['first_name']=$student->first_name;
         $student1['last_name']=$student->last_name;
         $student1['sex']=$student->sex;
         $student1['addmission_year']=$student->batch;
         $student1['program']=$student->program->name;
         $student1['department_name']=$student->degree_department->name;

        $student1['semester_grade_point']=$semester->pivot->semester_grade_point;
        $student1['GPA']=$semester->pivot->semester_GPA;
        $student1['CGPA']=$cgpa;
        $student1['total_credit_hour']=$total_credit_hour;
       // $student1['semesterAverage']=$semester->pivot->semester_avarege;
         $student1['previous_total']=100;
         $student1['courses']=$courses;

          $all_students[]=$student1;

       }
       return $all_students;


    }
    /*
    a method which return total grade point
    */
    public function totalCreditHour($student)
    {
        $semesters=$student->semesters;
        $total_credit_hour=0;
        foreach ($semesters as $semester) {
            $total_credit_hour+=$semester->pivot->semester_credit_hour;
        }
        return $total_credit_hour;

    }
    /*
    calculate a grade point of each course
    formula=grade_point_of_course=credit_hour*value_which_letter_grade_have
    */
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

    public function calculateCGPA($student){

        $semesters=$student->semesters;

        $total_cp=0.0;
        $total_point=0.0;
        foreach ($semesters as $semester) {

            $total_cp += $semester->pivot->semester_credit_hour;

            $total_point +=$semester->pivot->semester_credit_hour * $semester->pivot->semester_GPA;
        }

        if ($total_cp !=0) {
            $cgpa=$total_point/$total_cp;
        }else {
            $cgpa=0.0;
        }
    return $cgpa;

    }
}
