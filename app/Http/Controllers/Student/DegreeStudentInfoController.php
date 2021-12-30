<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeStudentInfo\CourseResource;
use App\Http\Resources\DegreeStudentInfo\SectionResource;
use App\Http\Resources\DegreeStudentInfo\SemestersResource;
use App\Http\Resources\DegreeStudentInfo\StudentCocResource;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use Illuminate\Http\Request;

class DegreeStudentInfoController extends Controller
{
    public function myTuitionFee(Request $request){

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
