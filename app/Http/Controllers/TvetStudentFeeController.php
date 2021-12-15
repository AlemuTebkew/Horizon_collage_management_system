<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\TvetStudent;
use App\Models\TvetStudentFee;
use Illuminate\Http\Request;

class TvetStudentFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $academic_year=AcademicYear::where('status',1)->first();
        $all_paid_months=[];
        $student=[];
        $students=[];
       foreach(TvetStudent::all() as $tvtStudent){
           //return $tvtStudent;
           
           $all_pads=[];
           foreach ($tvtStudent->month_payments as $monthPayment) {
              // return $monthPayment;
              // $pads=[];
            if($monthPayment->academic_year=$academic_year){
                $pads['month']=$monthPayment->name;
                $pads['pad_no']=$monthPayment->pivot->receipt_no;
              //  return $pads;
                $all_pads[]=$pads;
               
            }
      
            
       }
        $all_paid_months=array_merge($all_paid_months,$all_pads);
        $student[][$tvtStudent->id]=$all_paid_months;
        // return $student;
    }
    $students=array_merge($students, $student);   
     return response()->json(['students'=> $students]);
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
            'tvet_student_id'=>'required',
            'academic_fee_id'=>'required',
            'academic_year_id'=>'required',
             'month_id'=>'required',
             'paid_amount'=>'required',
             'paid_date'=>'required',
             'receipt_no'=>'required',
             'is_paid'=>'required',

        ]);
      return TvetStudentFee::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TvetStudentFee  $tvetStudentFee
     * @return \Illuminate\Http\Response
     */
    public function show(TvetStudentFee $tvetStudentFee)
    {
        return $tvetStudentFee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TvetStudentFee  $tvetStudentFee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TvetStudentFee $tvetStudentFee)
    {
        $request->validate([
            'tvet_student_id'=>'required',
            'academic_fee_id'=>'required',
            'academic_year_id'=>'required',
             'month_id'=>'required',
             'paid_amount'=>'required',
             'paid_date'=>'required',
             'receipt_no'=>'required',
             'is_paid'=>'required',

        ]);
       $tvetStudentFee->update($request->all());
       return $tvetStudentFee;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetStudentFee  $tvetStudentFee
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetStudentFee $tvetStudentFee)
    {
        $tvetStudentFee->delete();
    }
}
