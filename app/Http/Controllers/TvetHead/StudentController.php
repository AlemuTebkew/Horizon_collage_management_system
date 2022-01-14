<?php

namespace App\Http\Controllers\TvetHead;

use App\Http\Controllers\Controller;
use App\Http\Resources\Head\TvetSectionStudentResource;
use App\Models\AcademicYear;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Level;
use App\Models\TvetDepartment;
use App\Models\TvetSection;
use App\Models\TvetStudent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getSectionStudents($section_id){
        $ts=TvetSection::find($section_id);
        return TvetSectionStudentResource::collection($ts->tvet_students);
    }

    public function addStudentsToSection(){
        $sec= TvetSection::find(request('section_id'));
        foreach (request('student_ids')  as  $student_id) {
           $sec->tvet_students()->attach($student_id);
        }

        return TvetSectionStudentResource::collection($sec->tvet_students);

    }

    public function sectionSuggestedStudents(){

        $dep_head=Employee::where('email',request()->user()->user_name)->first();
        $department=$dep_head->managet;
        $id=request()->section_id;
        $section=TvetSection::find($id);
     //   return $section;
          $students= TvetStudent::where('tvet_department_id',$department->id)
                                         ->where('current_level_no', Level::find($section->level_id)->first()->level_no)
                                         ->whereDoesntHave('tvet_sections', function (Builder $query) use($department,$section) {
                                            $query->where('tvet_department_id',$department->id)
                                            ->where('level_id', $section->level_id)
                                            ->where('academic_year_id',$section->academic_year_id);

                                        }) ->with('tvet_department','program')->get();

 }

 public function getArrangedStudentsByDepartment(){

    $academic_year_id=null;
    if (request()->filled('academic_year_id')) {
        $academic_year_id=request('academic_year_id');
    }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
    }

       $levels=[];
       $all=[];
       $employee=Employee::where('email',request()->user()->user_name)->first();
       $dep_id= $employee->manage->id;
    //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
       $levels_=Level::with(['tvet_students'=>function($query) use($dep_id,$academic_year_id) {
          $query->where('tvet_department_id',$dep_id)
                 ->where('tvet_student_level.academic_year_id',$academic_year_id);
         ;
       }])
    //    ->where('academic_year_id',$academic_year_id)
       ->get();
         for ($i=1; $i <=5 ; $i++) {
            // $semester=$levels[$i];
            $no=null;
            $students=[];
            foreach ($levels_ as $level) {
                    $tvet_students=$level->tvet_students()
                    ->where('academic_year_id',$academic_year_id)
                    ->where('tvet_department_id',$dep_id)->get();

                foreach ($tvet_students as $s) {

                    if ($i == $level->level_no) {

                        $no=$level->level_no;
                        $student['id']=$s->id;
                        $student['student_id']=$s->student_id;
                        $student['first_name']=$s->first_name;
                        $student['last_name']=$s->last_name;
                        $student['sex']=$s->sex;
                        $student['program']=$s->program;
                        $student['status']=$s->pivot->status;
                        $student['department']['id']=$s->tvet_department->id;
                        $student['department']['name']=$s->tvet_department->name;
                        $dep=TvetDepartment::find($s->tvet_department->id);
                        // return $dep->programs->where('pivot.program_id',$s->program_id)
                        // ->first()->pivot->no_of_year;

                    // return $dep->programs;
                        if ($dep->programs) {

                           $a= $dep
                            ->programs()
                            ->where('program_id',$s->program->id)->first();
                               if ($a) {
                                $student['department']['no_of_level']=$a->pivot->no_of_level;

                               }else{
                                $student['department']['no_of_level']=null;
                               }
                        }
                         $students[]=$student;
                    }
                }
            }
              if ($no) {
                $levels['level_no']=$no;

                $levels['students']=$students;
                $all[]=$levels;
            }
    }
return response()->json($all,200);
}
}
