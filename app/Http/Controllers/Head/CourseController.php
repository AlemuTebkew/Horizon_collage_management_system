<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Course\SectionCourseResource;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\Employee;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getDepartmentCourses(){

        $employee=Employee::where('email',request()->user()->user_name)->first();
        // return $employee;
           $department=$employee->manage;
        return  CourseResource::collection ( Course::where('degree_department_id',$department->id)
        ->with('department','program')->get());
    }

    public function getSectionCourses(){
        $ds=DegreeSection::find(request('section_id'));
        $semester=Semester::find($ds->semester_id);
        $courses=Course::where('degree_department_id',$ds->degree_department_id)
                        ->where('program_id',$ds->program_id)
                        ->where('year_no',$ds->year_no)
                        ->where('semester_no',$semester->number)->get();

                        $all=[];
                        foreach ($courses as $course) {

                            $teacher=$course->teachers()->where('degree_section_id',$ds->id)->first();
                            $course1['id']=$course->id;
                            $course1['title']=$course->title;
                            $course1['code']=$course->code;
                            $course1['type']=$course->type;
                            $course1['cp']=$course->cp;
                            $course1['instructor']= $teacher ?$teacher->full_name:null;
                            $all[]=$course1;
                        }

                        return response()->json($all,200);
    //  return SectionCourseResource::collection( $courses->load('department','program','teachers'));

    }

    public function assignTeacherForCourse(){
        $ds=DegreeSection::find(request('section_id'));
        $course=Course::find(request('course_id'));
        $teacher=Teacher::find(request('teacher_id'));
        $assign=$course->teachers()->where('teacher_id',request()->teacher_id)
                           ->where('degree_section_id',request()->section_id)->first();
        if(!$assign) {
            $teacher->courses()->attach($course->id,[
                'degree_section_id'=>$ds->id,
            ]);
        }
        else{
            $teacher->courses()->updateExistingPivot($course->id,[
                'degree_section_id'=>$ds->id,
            ]);
        }


        return $teacher->full_name;
    }

}
