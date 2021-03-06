<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeStudentInfo\CourseResource;
use App\Http\Resources\DegreeStudentInfo\SectionResource;
use App\Http\Resources\DegreeStudentInfo\SemestersResource;
use App\Http\Resources\DegreeStudentInfo\StudentCocResource;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use Illuminate\Http\Request;

class DegreeStudentInfoController extends Controller
{
    public function myTuition(Request $request,$id){
        $degreeStudent=DegreeStudent::find($id);
        // ->with('semesters','month_payments')
        // return $degreeStudent->month_payments;

        $years=[];

          $semesters=$degreeStudent->semesters;
           if ($semesters->isEmpty() == false) {
            foreach($semesters as $semester){
                $academic_year_id=$semester->academic_year_id;
                $academic_year=AcademicYear::find($academic_year_id);
                $years[$academic_year_id]=$academic_year;
                }
            }else {
                return 'not registerd';
            }

            $year=[];
            foreach($years as $y){
                $year['year']=$y->year;
                $pads=[];
                $month_payments= $degreeStudent->month_payments()
                ->orderBy('number')
                ->wherePivot('academic_year_id',$y->id)->get();
                $total=0.0;
                foreach($month_payments as $month_payment){
                    if($y->id == $month_payment->pivot->academic_year_id){
                        // $pads['month']=$month_payment->name;
                        // $pads['pad']=$month_payment->pivot->receipt_no;
                        if ($month_payment->pivot->payable_status == 'unpayable') {
                            $pads[$month_payment->name]='አይከፈልም እሽ';

                        }else {
                            $pads[$month_payment->name]=$month_payment->pivot->receipt_no;
                        }

                    $total+=number_format ($month_payment->pivot->paid_amount);
                    //  $year['t'][]=$pads;
                    }

                }

                $year['total']=$total;
                $year['months']= $pads;
                $student[]=$year;
            }
            return  response()->json($student,200);
}

    public function myCourse(Request $request,$id){
        $student=DegreeStudent::find($id);

        $semester=$student->semesters()->wherePivot('status','!=','waiting')->first();

        if (!$semester) {
            return response()->json([],200);
        }
        $department=DegreeDepartment::find($student->degree_department_id);
        $courses=$department->courses()->where('courses.program_id',$student->program_id)->get();
        // $courses=$department->courses()->where('courses.program_id',$student->program_id)->get();
        return response()->json(CourseResource::collection($courses),200);

    }


    public function mySemesterCourse(Request $request,$id){
        $dep_id=DegreeStudent::find($id)->degree_department_id;
        $department=DegreeDepartment::find($dep_id);
        $courses=$department->courses()->where('semester_no',request('semester_no'))->get();
        return response()->json(CourseResource::collection($courses),200);

    }

    public function myGrade(Request $request,$id){
        $student=DegreeStudent::find($id);


        $studentCourses=$student->courses()->get();
        $semesters=$student->semesters()->wherePivot('status','!=','waiting')->get();


        $courses=[];

        foreach ($semesters as $semester) {

         foreach ($studentCourses as $studentCourse) {

          if ($semester->id == $studentCourse->pivot->semester_id) {

            $course=[];
          $letter_grade=$studentCourse->pivot->grade_point;
          $grade_point=$this->courseGradePoint($studentCourse->cp,$letter_grade);
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
       }
    }
       return response()->json($courses,200);
    }


    public function myStatus(Request $request,$id){

        $student=DegreeStudent::find($id);

        $semesters=[];
        $temp=[];
        foreach ($student->semesters as $semester) {

            if ($semester->pivot->status != 'waiting') {

                $temp[]=$semester->id;
                $seme['id']=$semester->id;
                $seme['year']=$semester->academic_year ? $semester->academic_year->year:null;
                $seme['start_date']=$semester->start_date;
                $seme['end_date']=$semester->end_date;
                $seme['status']=$semester->pivot->status;
                $seme['year_no']=$semester->pivot->year_no;
                $seme['semester_no']=$semester->pivot->semester_no;
                $seme['GPA']=$semester->pivot->semester_GPA;
                if($semester->pivot->semester_GPA == 0 || $semester->pivot->semester_GPA == 0.0){
                    $seme['CGPA']=0;

                }else{
                    $seme['CGPA']=$this->calculateCGPA($student,$temp);

                }
                $semesters[]=$seme;
            }
        }
        // return response()->json(SemestersResource::collection($student->semesters),200);
        return response()->json($semesters,200);
    }

    public function myCoc(Request $request,$id){
        $student=DegreeStudent::find($id);
       return  response()->json(StudentCocResource::collection($student->cocs),200);

    }

    public function mySection(Request $request,$id){
          $student=DegreeStudent::find($id);
          return response()->json(SectionResource::collection($student->degree_sections),200);
    }

    private function getTotalCp($student,$semester){
        $courses=Course::where('degree_department_id',$student->degree_department_id)
        ->where('program_id',$student->program_id)
        ->where('year_no',$semester->pivot->year_no)
        ->where('semester_no',$semester->pivot->semester_no)
        ->get();

    return $courses->sum('cp');
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

    public function calculateCGPA($student,$semesters){

        $semesters=$student->semesters()->whereIn('semesters.id',$semesters)->get();

        $total_cp=0.0;
        $total_point=0.0;
        foreach ($semesters as $semester) {

            $total_cp += $semester->pivot->semester_credit_hour;
            $total_point +=$semester->pivot->semester_grade_point;

            // $total_point +=$semester->pivot->semester_credit_hour * $semester->pivot->semester_GPA;
        }

      return  $cgpa=$total_point/$total_cp;

    }
}
