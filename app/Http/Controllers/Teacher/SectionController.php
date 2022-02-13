<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\Head\DegreeSectionStudentResource;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Module;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\TvetSection;
use App\Models\TvetStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function getSections($id){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

     $teacher=Teacher::find($id);
     $dsecs=$teacher->degree_sections()->where('academic_year_id',$academic_year_id)->get();
     $tsecs=$teacher->tvet_sections()->where('academic_year_id',$academic_year_id)->get();

    $sections=[];
    foreach ($dsecs as $dsec) {
      $section=[];

      $seme=Semester::find($dsec->semester_id);
     if (! $seme->is_closed) {
        $section['id']=$dsec->id;
        $section['name']=$dsec->name;
        $section['department']=$dsec->degree_department->name;
        $section['program']=$dsec->program->name;
        $section['year']=$dsec->year_no;
        $section['course_title']=Course::find($dsec->pivot->course_id)->title;
        $section['course_id']=Course::find($dsec->pivot->course_id)->id;
        $section['no_of_students']=$dsec->degree_students->count();
        $section['type']='degree';

        $sections[]=$section;
     }

    }

    foreach ($tsecs as $tsec) {
        $section=[];
        $section['id']=$tsec->id;
        $section['name']=$dsec->name;
        $section['department']=$tsec->tvet_department->name;
        $section['program']=$tsec->program->name;
        $section['level']=$tsec->level->level_no;
        $section['course_title']=Module::find($tsec->pivot->module_id)->title??null;
        $section['course_id']=Module::find($tsec->pivot->module_id)->id??null;
        $section['no_of_students']=$tsec->tvet_students->count();
        $section['type']='tvet';

        $sections[]=$section;
      }
    return $sections;
    }


    public function getTeacherStudents($section_id){

        $students=[];
       $teacher= Teacher::find(request('teacher_id'));


        if (request('type') == 'degree') {

            $ds=DegreeSection::find($section_id);

            // $teacher_course=$teacher->courses()->wherePivot('degree_section_id',$section_id)->first();
           foreach ($ds->degree_students as $st) {

            $semester=$st->semesters()->wherePivot('semester_id',$ds->semester_id)->first();

            //check result entry time
            $is_allowed_now=  DB::table('dynamic_system_settings')->first()->degree_teacher_result_entry_time;

            $st_course=$st->courses()->wherePivot('course_id',request('course_id'))->first()->pivot;
            $student['id']=$st->id;
            $student['legible']=$semester->pivot->legible; //check for student payment fee
            $student['is_allowed_now']=$is_allowed_now;
            $student['student_id']=$st->student_id;
            $student['first_name']=$st->first_name;
            $student['last_name']=$st->last_name;
            $student['sex']=$st->sex;
            $student['from_5']=$st_course->from_5;
            $student['from_5s']=$st_course->from_5s;
            $student['from_25']=$st_course->from_25;
            $student['from_40']=$st_course->from_40;
            $student['from_25s']=$st_course->from_25s;
            $student['result']=$st_course->total_mark;
            $student['letter_grade']=$st_course->letter_grade;


            $students[]=$student;
        }
        }else if (request('type') == 'tvet'){

            $ts=TvetSection::find($section_id);

            // $teacher_module=$teacher->modules()->wherePivot('tvet_section_id',$section_id)->first();
            foreach ($ts->tvet_students as $st) {
                $level=$st->levels()->wherePivot('level_id' ,$ts->level_id)->first();

                if ($level->pivot->legible) {
                    $student['legible']=$level->pivot->legible;
                }else {

                //check for student tuition fee
                $m_name=(new Carbon())->monthName;
                $un_paid=  $st->month_payments()->wherePivot('academic_year_id',$ts->academic_year_id)
                                       ->wherePivot('receipt_no',null)
                                        ->where('months.name',$m_name)->first();


                      if (!$un_paid) {
                          $st->levels()->updateExistingPivot($ts->level_id,
                          [
                              'legible'=>1,

                          ]);

                          $student['legible']=1;

                      }else {
                          $st->levels()->updateExistingPivot($ts->level_id,
                          [
                              'legible'=>0,

                          ]);
                          $student['legible']=0;
                      }
                    }
                      //check result entry time
                $is_allowed_now=  DB::table('dynamic_system_settings')->first()->tvet_teacher_result_entry_time;

                $st_module=$st->modules()->where('module_id',request('course_id'))->first()->pivot;
                $student['id']=$st->id;
                $student['is_allowed_now']=$is_allowed_now;
                $student['student_id']=$st->student_id;
                $student['first_name']=$st->first_name;
                $student['last_name']=$st->last_name;
                $student['sex']=$st->sex;
                $student['from_20']=doubleval($st_module->from_20);
                $student['from_30']=doubleval($st_module->from_30);
                $student['from_50']=doubleval($st_module->from_50);
                $student['result']=$st_module->total_mark;

            $students[]=$student;
        }
    } else {
        return response()->json(['not succssfull '],404);
    }

        return $students;
    }

    public function setResult($student_id){

        DB::beginTransaction();

        try {

        if (request('type') == 'degree') {

            $sem_id=DegreeSection::find(request('section_id'))->semester_id;
            $cp=Course::find(request('course_id'))->cp;
            $student=DegreeStudent::find($student_id);
            $semester=Semester::find($sem_id);

            if ($semester->is_closed) {
                return response()->json('Result Entry on closed semester is not allowed!!  ',400);
             }
            $ff=$student->semesters()->wherePivot('semester_id' ,$sem_id)->first();
            if ($ff) {
               if (!$ff->pivot->legible) {
                return response()->json('Illigble !!! Fee Isue  ',400);
               }
            }

          $is_allowed_now=  DB::table('dynamic_system_settings')
                   ->first()->degree_teacher_result_entry_time;
          if (! $is_allowed_now) {
            return response()->json('Illigble !!! Not Result Entry Time  ',400);

          }

            $letter_grade=$this->calculateLetterGrade(request()->total_mark);
            $grade_point=$this->courseGradePoint($cp,$letter_grade);
            $student->courses()->updateExistingPivot(request('course_id'),[

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
                                       ->wherePivot('grade_point','')->count();

                if ($sem_couses_count == 0) {
                    $student->semesters()->updateExistingPivot($sem_id,[
                        'status'=>'finished',
                    ]);
                }
             DB::commit();
             return response()->json($letter_grade,200);

        }else if(request('type') == 'tvet'){
            $student=TvetStudent::find($student_id);
            $level_id=DegreeSection::find(request('section_id'))->level_id;


            $ff=$student->levels()->wherePivot('level_id' ,$level_id)->first();

            if ($ff) {
               if (!$ff->pivot->legible) {

                return response()->json('Illigble !!! Fee Isue  ',400);

               }
            }

            $is_allowed_now=  DB::table('dynamic_system_settings')->first()->tvet_teacher_result_entry_time;

            if (! $is_allowed_now) {
              return response()->json('Illigble !!! Not Result Entry Time  ',400);

            }

            $student->modules()->updateExistingPivot(request('course_id'),[
                'from_20'=>request('from_20'),
                'from_30'=>request('from_30'),
                'from_50'=>request('from_50'),
                'total_mark'=>request('result'),

             ]);

            
             $sem_couses_count=$student->modules()
             ->wherePivot('level_id',$level_id)
             ->where(function($q){
                 $q->where('from_20','')
                 ->orWhere('from_30','')
                 ->orWhere('from_50','');
             })->count();
                        
            if ($sem_couses_count == 0) {
            $student->levels()->updateExistingPivot($level_id,[
            'status'=>'finished',
            ]);
            }
             DB::commit();
             return response()->json('Result Set succssfully  ',200);

            } else {
            return response()->json('not succssfull ',404);
        }


    } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json('not succssfull '.$th,404);

    }

    }


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
            $total_cp += doubleval($course->cp);
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
