<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Http\Resources\Head\DegreeSectionStudentResource;
use App\Models\AcademicYear;
use App\Models\DegreeDepartment;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function getArrangedStudentsByDepartment(Request $request){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

           $semesters=[];
           $all=[];
           $employee=Employee::where('email',request()->user()->user_name)->first();
           $dep_id= $employee->manage->id;
        //    $dep_id=Employee::where('email',request()->user()->user_name)->first()->manage->id;
           $semesters1=Semester::with(['degree_students'=>function($query) use($dep_id) {
             $query->where('degree_department_id',$dep_id);
           }])
           ->where('academic_year_id',$academic_year_id)
           ->get();
             for ($i=1; $i <=3 ; $i++) {
                // $semester=$semesters1[$i];
                $no=null;
                $students=[];
                foreach ($semesters1 as $semester) {
                        $degree_students=$semester->degree_students()
                       ->where('academic_year_id',$academic_year_id)
                        ->where('degree_department_id',$dep_id)->get();

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


                            if ($dep->programs) {

                               $a= $dep
                                ->programs()
                                ->where('program_id',$s->program->id)->first();
                                   if ($a) {
                                    $student['department']['no_of_year']=$a->pivot->no_of_year;

                                   }else{
                                    $student['department']['no_of_year']=null;
                                   }

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
    public function getSectionStudents($section_id){
        $ds=DegreeSection::find($section_id);
        return DegreeSectionStudentResource::collection($ds->degree_students);
    }

    public function addStudentsToSection(){
        $sec= DegreeSection::find(request('section_id'));
        foreach (request('student_ids')  as  $student_id) {
           $sec->degree_students()->attach($student_id);
        }

        return DegreeSectionStudentResource::collection($sec->degree_students);

    }

    public function removeStudentsFromSection($student_id){
        $sec= DegreeSection::find(request('section_id'));
        $sec->degree_students()->detach($student_id);


        return response()->json('Succssfully Removed',200);

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
                                         ->whereHas('semesters',function(Builder $q) use($section){
                                             $q->where('semester_id',$section->semester_id)
                                               ->where('status','approved')
                                               ;
                                         })

                                         ->whereDoesntHave('degree_sections', function (Builder $query) use($department,$section) {
                                            $query->where('degree_department_id',$department->id)
                                                  ->where('year_no',$section->year_no)
                                                  ->where('semester_no',$section->semester_no)
                                                  ->where('academic_year_id',$section->academic_year_id);

                                        }) ->with('degree_department','program')->get();

        return $students;
 }

}
