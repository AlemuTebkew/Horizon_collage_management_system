<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Head\DegreeSectionController;
use App\Http\Controllers\Registrar\DegreeStudentController;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Course\SectionCourseResource;
use App\Models\Course;
use App\Models\DegreeDepartment;
use App\Models\DegreeSection;
use App\Models\Employee;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CourseResource::collection(Course::with('department','program')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'=>'required',
            'title'=>'required',
            'type'=>'required',
             'year_no'=>'required',
            'semester_no'=>'required',
            'degree_department_id'=>'required',
            'program_id'=>'required',

        ]);
      $courses=  Course::create($request->all());
      return response()->json(new CourseResource($courses->load('department','program')),200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return response()->json(new CourseResource($course->load('department','program')),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'code'=>'required',
            'title'=>'required',
            'type'=>'required',
             'year_no'=>'required',
            'semester_no'=>'required',
            'degree_department_id'=>'required',
            'program_id'=>'required',

        ]);
       $course->update($request->all());
       return response()->json(new CourseResource($course->load('department','program')),200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();
    }
    /*
    **/
    public function getDepartmentCourses(){

        $employee=Employee::where('email',request()->user()->user_name)->first();
        // return $employee;
           $department_head=$employee->manage;


        return  CourseResource::collection ( Course::where('degree_department_id',$department_head->id)
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

    public function assignTeacherForCourse(){
        $ds=DegreeSection::find(request('section_id'));
        $course=Course::find(request('course_id'));
        $teacher=Teacher::find(request('teacher_id'));
        $assign=$course->teachers()->where('teacher_id',request()->teacher_id)
                           ->where('degree_section_id',request()->section_id)->get();
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
