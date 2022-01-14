<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Course\SectionCourseResource;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\Employee;
use App\Models\Semester;
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
     return SectionCourseResource::collection( $courses->load('department','program','teacher'));

    }

}
