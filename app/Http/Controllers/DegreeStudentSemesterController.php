<?php

namespace App\Http\Controllers;

use App\Models\DegreeStudentSemester;
use Illuminate\Http\Request;

class DegreeStudentSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DegreeStudentSemester::all();
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
            'number'=>'required',
            'degree_student_id'=>'required',
            'semester_id'=>'required',
            'semester_GPA'=>'required',
            'tution_type'=>'required',


        ]);
      return DegreeStudentSemester::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeStudentSemester  $degreeStudentSemester
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeStudentSemester $degreeStudentSemester)
    {
        return $degreeStudentSemester;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeStudentSemester  $degreeStudentSemester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeStudentSemester $degreeStudentSemester)
    {
        $request->validate([
            'number'=>'required',
            'degree_student_id'=>'required',
            'semester_id'=>'required',
            'semester_GPA'=>'required',
            'tution_type'=>'required',


        ]);
      return $degreeStudentSemester->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeStudentSemester  $degreeStudentSemester
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeStudentSemester $degreeStudentSemester)
    {
        //
    }
}
