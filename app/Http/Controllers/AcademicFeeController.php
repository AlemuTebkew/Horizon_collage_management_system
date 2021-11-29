<?php

namespace App\Http\Controllers;

use App\Models\AcademicFee;
use Illuminate\Http\Request;

class AcademicFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AcademicFee::all();
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
            'amount'=>'required',
            'academic_type_id'=>'required',
            'academic_year_id'=>'required',


        ]);
      return AcademicFee::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\academicFee  $academicFee
     * @return \Illuminate\Http\Response
     */
    public function show(academicFee $academicFee)
    {
        return $academicFee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\academicFee  $academicFee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicFee $academicFee)
    {
        $request->validate([
            'amount'=>'required',
            'academic_type_id'=>'required',
            'academic_year_id'=>'required',


        ]);
      return $academicFee->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\academicFee  $academicFee
     * @return \Illuminate\Http\Response
     */
    public function destroy(academicFee $academicFee)
    {
        $academicFee->delete();
    }
}
