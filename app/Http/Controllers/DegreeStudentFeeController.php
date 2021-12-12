<?php

namespace App\Http\Controllers;

use App\Http\Resources\DegreeFee\DegreeStudentFeeResource;
use App\Http\Resources\DegreeFee\DegreeStudentsFeeResource;
use App\Http\Resources\DegreeFee\StudentFeeResource;
use App\Models\DegreeStudent;
use Illuminate\Http\Request;

class DegreeStudentFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DegreeStudentsFeeResource::collection(DegreeStudent::with('month_payments')->get());
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
        $degreeStudent=DegreeStudent::find($id);

             // return $degreeStudent->load('semesters.months');
             return new StudentFeeResource($degreeStudent->load('semesters.months'));
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
}
