<?php
namespace App\Traits;

trait Reusable{


    public function calculateLetterGrade($r){

        if ($r >= 90) {
            return 'A+';
        } else if ($r >= 85) {
            return 'A';
        } else if ($r >= 80) {
            return 'A-';
        } else if ($r >= 75) {
            return 'B+';
        } else if ($r >=70) {
            return 'B';
        } else if ($r >=65) {
            return 'B-';
        } else if ($r >=60) {
            return 'C+';
        } else if ($r >=50) {
            return 'C';
        } else if ($r >=45) {
            return 'C-';
        } else if ($r >=40) {
            return 'D';
        } else if ($r < 40) {
            return 'F';
        }
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
        else if($letter_grade == 'D'){
            return $grade_point=$credit_hour*1;
        }
        else if($letter_grade == 'F'){
            return $grade_point=$credit_hour*0;
        }
    }

    public function calculateGPA($student,$semester_id){

        $sem_couses=$student->courses()->wherePivot('semester_id',$semester_id)->get();

        $total_gp=0.0;
        $total_cp=0.0;
        foreach ($sem_couses as $course) {

            if (($course->pivot->grade_point == 0.0  || $course->pivot->grade_point == 0) && $course->pivot->letter_grade != 'F' ) {
                $total_gp += $course->cp * 4.0;

            }else{
                $total_gp += $course->pivot->grade_point;
                // $total_gp += $course->cp * $this->courseGradePoint($course->cp, $course->pivot->letter_grade);
            }
            $total_cp += $course->cp;
        }
        $gpa=$total_gp/$total_cp;


        return [$gpa,$total_gp];

    }

    public function calculateSemesterGP(){

    }

    public function calculateCGPA($student){

        $semesters=$student->semesters;
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
