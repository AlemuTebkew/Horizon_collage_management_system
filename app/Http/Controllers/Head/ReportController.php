<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getCourseTakenBySemester(Request $request){
        
        $employee=Employee::where('email',request()->user()->user_name)->first();
        $department_head=$employee->manage;
        // return Course::where('degree_department_id',$department_head->id)->get();
        return  $course=Course::where('degree_department_id',$department_head->id)
                            ->where('program_id',$request->program_id)
                            ->where('year_no',$request->year_no)
                            ->where('semester_no',$request->semester_no)->get();

    
}
    public function getGradeReport(Request $request){
        $employee=Employee::where('email',request()->user()->user_name)->first();
       $courses=Course::where('degree_department_id',request()->degree_department_id)
       ->where('program_id',request()->program_id)
       ->where('year_no',request()->year_no)
       ->where('semester_no',request()->semester_no)->get();


    }
}
