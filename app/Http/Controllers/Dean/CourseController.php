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

       $c= Course::where('code',$request->code)->first();
        if ($c) {
            return response()->json('The same code number',202);
        }
      $courses=  Course::create($request->all());
      return response()->json(new CourseResource($courses->load('department','program')),201);
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
        $c= Course::where('code',$request->code)
                   ->where('code','!=', $course->code)->first();
        if ($c) {
            return response()->json('The same code number',202);
        }
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

        if ($course->delete()) {
           return response()->json('successfully delete',200);
        }
    }
    /*
    **/


}
