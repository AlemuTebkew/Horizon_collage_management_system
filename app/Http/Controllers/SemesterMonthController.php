<?php

namespace App\Http\Controllers;

use App\Models\SemesterMonth;
use Illuminate\Http\Request;

class SemesterMonthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SemesterMonth::all();
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
            'degree_semester_id'=>'required',
            'month_id'=>'required',

        ]);

        return SemesterMonth::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SemesterMonth  $semesterMonth
     * @return \Illuminate\Http\Response
     */
    public function show(SemesterMonth $semesterMonth)
    {
        return $semesterMonth;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SemesterMonth  $semesterMonth
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SemesterMonth $semesterMonth)
    {
        $request->validate([
            'degree_semester_id'=>'required',
            'month_id'=>'required',

        ]);
         $semesterMonth->update($request->all());
         return $semesterMonth;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SemesterMonth  $semesterMonth
     * @return \Illuminate\Http\Response
     */
    public function destroy(SemesterMonth $semesterMonth)
    {
        $semesterMonth->delete();
    }
}
