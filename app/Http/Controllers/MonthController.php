<?php

namespace App\Http\Controllers;

use App\Http\Resources\AcademicYear\MonthResource;
use App\Models\AcademicYear;
use App\Models\Month;
use App\Models\Semester;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $academic_year=AcademicYear::where('status',1)->first();

        $semesters=Semester::where('academic_year_id',$academic_year->id)
                            ->where('status',1)->get();
        // return  $semesters->load('months');
         return $academic_year->months;
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
            'name'=>'required',

        ]);
      return Month::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Month  $month
     * @return \Illuminate\Http\Response
     */
    public function show(Month $month)
    {
        return $month;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Month  $month
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Month $month)
    {
        $request->validate([
            'name'=>'required',


        ]);
      return $month->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Month  $month
     * @return \Illuminate\Http\Response
     */
    public function destroy(Month $month)
    {
        $month->delete();
    }
}
