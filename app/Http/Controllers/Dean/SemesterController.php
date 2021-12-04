<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Semester::all();
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
            'academic_year_id'=>'required',
            'program_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'status'=>'required',
            'is_current'=>'required',
        ]);
        return Semester::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        return $semester;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'number'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'status'=>'required',
            'is_current'=>'required',
        ]);
         $semester->update($request->all());
         return $semester;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();
    }
}
