<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\Head\DegreeSectionStudentResource;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\TvetSection;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

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

            $st_course=$st->courses()->where('course_id',request('course_id'))->first()->pivot;
            $student['id']=$st->id;
            $student['student_id']=$st->student_id;
            $student['first_name']=$st->first_name;
            $student['last_name']=$st->last_name;
            $student['sex']=$st->sex;
            $student['from_11']=$st_course->from_11;
            $student['from_12']=$st_course->from_12;
            $student['from_12s']=$st_course->from_12s;
            $student['from_40']=$st_course->from_40;
            $student['from_25']=$st_course->from_25;
            $student['result']=$st_course->total_mark;

            $students[]=$student;
        }
        }else if (request('type') == 'tvet'){

            $ts=TvetSection::find($section_id);

            // $teacher_module=$teacher->modules()->wherePivot('tvet_section_id',$section_id)->first();
            foreach ($ts->tvet_students as $st) {
               $st_module=$st->modules()->where('module_id',request('course_id'))->first()->pivot;
                $student['id']=$st->id;
                $student['student_id']=$st->student_id;
                $student['first_name']=$st->first_name;
            $student['last_name']=$st->last_name;
            $student['sex']=$st->sex;
            $student['from_11']=$st_module->from_11;
            $student['from_12']=doubleval($st_module->from_12);
            $student['from_12s']=doubleval($st_module->from_12s);
            $student['from_40']=$st_module->from_40;
            $student['from_25']=$st_module->from_25;
            $student['result']=$st_module->total_mark;

            $students[]=$student;
        }
    } else {
        return response()->json(['not succssfull '],404);
    }

        return $students;
    }

    public function setResult($student_id){


        if (request('type') == 'degree') {
            $student=DegreeStudent::find($student_id);

            $student->courses()->updateExistingPivot(request('course_id'),[
                'from_11'=>request('from_11'),
                'from_12'=>request('from_12'),
                'from_12s'=>request('from_12s'),
                'from_25'=>request('from_25'),
                'from_40'=>request('from_40'),
                'total_mark'=>request('total_mark'),
                'grade_point'=>$this->calculateGrade(request()->total_mark),

             ]);

             return response()->json(['Result Set succssfully  '],200);

        }else if(request('type') == 'tvet'){
            $student=TvetStudent::find($student_id);

            $student->modules()->updateExistingPivot(request('course_id'),[
                'from_11'=>request('from_11'),
                'from_12'=>request('from_12'),
                'from_12s'=>request('from_12s'),
                'from_25'=>request('from_25'),
                'from_40'=>request('from_40'),
                'total_mark'=>request('total_mark'),
                // 'grade_point'=>$this->calculateGrade(request()->total_mark),

             ]);
             return response()->json(['Result Set succssfully  '],200);

            } else {
            return response()->json(['not succssfull '],404);
        }

    }


    public function calculateGrade($r){

        if ($r >= 90) {
            return 'A+';
        } else if ($r >= 85) {
            return 'A';
        } else if ($r >= 80) {
            return 'A-';
        } else if ($r >= 75) {
            return 'B+';
        } else if ($r >=50) {
            return 'C+';
        } else if ($r >=30) {
            return 'D';
        }
    }
}
