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

            if ($degreeStudent->has('semesters')) {
                foreach($degreeStudent->semesters as $semester){
                $academic_year_id=$semester->academic_year_id;
                $academic_year=AcademicYear::find($academic_year_id);
                $years[$academic_year_id]=$academic_year;
                }
            }else {
                return 'not registerd';
            }

            $year=[];
            foreach ($years as $y) {
                // return  $month_payments=$degreeStudent->month_payments()
                // ->wherePivot('academic_year_id',$y->id)->get();
                $year['year'][]=$y->year;
                $semester=[];
                $semesters=$degreeStudent->semesters()
                ->where('semesters.academic_year_id',$y->id)->get();



                        // return $semesters;
              foreach ($semesters as $s) {

                //    return $s->months;
                if ($s->has('months')) {


                  if ($s->pivot->tuition_type == 'monthly' || $s->pivot->tuition_type == null ) {
                  $total_cp= $this->getTotalCp($degreeStudent,$s);
                    $semester['id']=$s->id;
                    $semester['semester_no']=$s->number;
                    $semester['tution_type']=$s->pivot->tuition_type;
                    // $semester['semester_payment']=$cp_fee * $total_cp;

                    $total=0;
                    $total_pad=[];
                    $months= $s->months;
                 foreach ($months as $month) {
                    $month_pad=[];

                     $month_payments=$degreeStudent->month_payments()
                     ->wherePivot('academic_year_id',$y->id)->get();
                     if ( count( $month_payments) == 0 ) {

                        foreach ($month_payments as $month_payment) {


                        if ($month_payment->pivot->academic_year_id == $y->id ) {

                        if ($month->id == $month_payment->id) {
                            $month_pad['id']=$month_payment->id;
                            $month_pad['name']=$month_payment->name;
                            $month_pad['pad']=$month_payment->pivot->receipt_no;
                            $month_pad['paid_date']= $month_payment->pivot->paid_date;
                            $total+= doubleval($month_payment->pivot->paid_amount);
                            $total_pad[]=$month_pad;
                           break;
                        }else {

                            $month_pad['id']=$month->id;
                            $month_pad['name']=$month->name;
                            $month_pad['pad']=null;
                            $month_pad['paid_date']=null;
                            $total_pad[]=$month_pad;
                           break;
                        }
                    }

                    }
                }else {
                    $month_pad['id']=$month->id;
                    $month_pad['name']=$month->name;
                    $month_pad['pad']=null;
                    $month_pad['paid_date']=null;
                    $total_pad[]=$month_pad;
                    // break;
                }

            }

                $semester['total']=$total;
                $semester['months']=$total_pad;
                $student['monthly'][]=$semester;
                  }else if ($s->pivot->tuition_type == 'cp' ) {
                            $semester=[];
                        $semester['id']=$s->id;
                        $semester['semester_no']=$s->number;
                        $semester['tution_type']=$s->pivot->tuition_type;
                        $total=0;
                        $month_pad=null;
                    //    return $s->months;
                 //   return $degreeStudent->month_payments ->where('pivot.academic_year_id',$academic_year->id);
                     foreach ($s->months as $month) {
                         $month_payments=$degreeStudent->month_payments()
                         ->wherePivot('academic_year_id',$y->id)->get();
                        foreach ($month_payments as $month_payment) {

                            if ($month_payment->pivot->academic_year_id == $y->id) {
                                if ($month->id == $month_payment->id) {

                                $month_pad=$month_payment->pivot->receipt_no;
                              //  $month_pad= $month_payment->pivot->paid_date;
                                $total+= ($month_payment->pivot->paid_amount);
                               // $total_pad[]=$month_pad;
                               break;
                            }

                        }
                        }
                    }
                    $semester['total']=$total;
                    $semester['pad']=$month_pad;
                    $student['cp'][]=$semester;

                }


                }

              }
              $year['year'][]=$student;

            }


    return $year;

}

    public function myCourse(Request $request,$id){
        $student=DegreeStudent::find($id);
        $department=DegreeDepartment::find($student->degree_department_id);
        $courses=$department->courses()->where('courses.program_id',$student->program_id)->get();
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
        $studentCourses=$student->courses()
                                        //    ->where('program_id',request()->program_id)
                                        //     ->where('year_no',request()->year_no)
                                        //     ->where('semester_no',request()->semester_no)
                                            ->get();
        $semester=$student->semesters()->where('program_id',request()->program_id)
                                                  ->where('year_no',request()->year_no)
                                                  ->where('semester_no',request()->semester_no)->first();
          $courses=[];

         foreach ($studentCourses as $studentCourse) {
             $course=[];
          $letter_grade=$studentCourse->pivot->grade_point;
          $grade_point=$this->courseGradePoint($studentCourse->cp,$letter_grade);
          $course['code']=$studentCourse->code;
          $course['title']=$studentCourse->title;
          $course['semester_no']=$studentCourse->semester_no;
          $course['cp']=$studentCourse->cp;
          $course['letter_grade']=$studentCourse->pivot->grade_point;
          $course['year_no']=$studentCourse->year_no;
          $course['total_mark']=$studentCourse->pivot->total_mark;
          $course['grade_point']=$grade_point;

         $courses[]=$course;

       }
       return response()->json($courses,200);
    }


    public function myStatus(Request $request,$id){

        $student=DegreeStudent::find($id);
        return response()->json(SemestersResource::collection($student->semesters),200);
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
}
