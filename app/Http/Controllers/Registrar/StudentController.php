<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeDepartment;
use App\Models\Level;
use App\Models\Semester;
use App\Models\TvetDepartment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getDegreeGraduated(){

        //$academic_year_id=null;
     //    return request()->academic_year_id;
     if (request('academic_year_id')) {
        $academic_year_id=request('academic_year_id');
    }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
    }

       $semesters=[];
       $all=[];
    //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
       $all_semester=Semester::with(['degree_students'=>function($query) {
       }])
       ->where('academic_year_id',$academic_year_id)
       ->get();
         for ($i=1; $i <=3 ; $i++) {
             /**
              * this loop is in best place .if itplaced after all loop
              * sost gizem tkkl slemihon andun temari sost gize yawotewal
              * arrayw gin yand semester bcha new miz slezih mejemeriya 1 semester lay yalutn mejemeria tchers ena
              * continue
              */

            $no=null;
            $students=[];
            foreach ($all_semester as $semester) {
                    $degree_students=$semester->degree_students()
                   ->where('academic_year_id',$academic_year_id)
                   ->when(request('department_id'),function($q){
                      $q->where('degree_department_id',request('department_id'));
                   })
                   ->when(request('department_id'),function($q){
                      $q->where('program_id',request('program_id'));
                    })
                   ->where('is_graduated',1)
                    ->get();

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

                        if ($s->fully_scholarship) {
                            $student['scholarship']='fully';

                        }else if($s->pivot->partial_scholarship){
                            $student['scholarship']='partial';

                        }else {
                            $student['scholarship']='none';
                        }


                        if ($dep->programs) {

                           $a= $dep
                            ->programs()
                            ->where('program_id',$s->program->id)->first();
                               if ($a) {
                                $student['department']['no_of_year']=$a->pivot->no_of_year;

                               }else{
                                $student['department']['no_of_year']=null;
                               }
                        }else {
                           $student['department']['no_of_year']=null;

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

public function getTvetGraduated(){

    $academic_year_id=null;
    if (request()->filled('academic_year_id')) {
        $academic_year_id=request('academic_year_id');
    }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
    }

       $levels=[];
       $all=[];
    //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
       $levels_=Level::with(['tvet_students'=>function($query) use($academic_year_id) {
                 $query->where('tvet_student_level.academic_year_id',$academic_year_id);
              }])
    //    ->where('academic_year_id',$academic_year_id)
              ->get();
         for ($i=1; $i <=5 ; $i++) {
            // $semester=$levels[$i];
            $no=null;
            $students=[];
            foreach ($levels_ as $level) {
                    $tvet_students=$level->tvet_students()
                    ->wherePivot('academic_year_id',$academic_year_id)
                    ->where('is_graduated',1)
                    ->get();

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

                        if ($s->fully_scholarship) {
                            $student['scholarship']='fully_scholarship';
                        }else if($s->pivot->partial_scholarship){
                            $student['scholarship']='partial_scholarship';
                        }else {
                            $student['scholarship']='none';
                        }

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

