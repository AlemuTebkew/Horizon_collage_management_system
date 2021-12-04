<?php

namespace App\Http\Controllers;

use App\Models\SemesterPayment;
use Illuminate\Http\Request;

class SemesterPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SemesterPayment::all();
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
            'degree_student_id'=>'required',
            'academic_fee_id'=>'required',
            'receipt_no'=>'required',
            'amount'=>'required',



        ]);

        return SemesterPayment::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SemesterPayment  $semesterPayment
     * @return \Illuminate\Http\Response
     */
    public function show(SemesterPayment $semesterPayment)
    {
        return $semesterPayment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SemesterPayment  $semesterPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SemesterPayment $semesterPayment)
    {
        $request->validate([
            'degree_semester_id'=>'required',
            'degree_student_id'=>'required',
            'academic_fee_id'=>'required',
            'receipt_no'=>'required',
            'amount'=>'required',



        ]);

        return $semesterPayment->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SemesterPayment  $semesterPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SemesterPayment $semesterPayment)
    {
        $semesterPayment->delete();
    }
}
