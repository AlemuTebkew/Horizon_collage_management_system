<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course;
use App\Models\DegreeDepartment;
use App\Models\Employee;
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
    public function getCourse($department_Head_id){
        $dep_head=Employee::find($department_Head_id);
        $department=$dep_head->manage;
        return $course=Course::where('degree_department_id',$department->id)->get();
    }
}
