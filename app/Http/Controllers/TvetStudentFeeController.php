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
        $student=[];
        $students=[];
      //  return TvetStudent::with('month_payments')->get();
       foreach(TvetStudent::with('month_payments')->get() as $tvtStudent){
           //return $tvtStudent;
           $all_paid_months=[];

           $all_pads=[];
           foreach ($tvtStudent->month_payments as $monthPayment) {
              // return $monthPayment;
              // $pads=[];
            if($monthPayment->pivot->academic_year_id == $academic_year->id){
                $pads['id']=$monthPayment->id;
                $pads['month_number']=$monthPayment->number;
                $pads['month_name']=$monthPayment->name;
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
    public function show($tvetStudentId)
    {
        $tvetStudent=TvetStudent::find($tvetStudentId);
        // return $tvetStudent->month_payments;
        foreach($tvetStudent->month_payments as $month){
            //check through relationship method
            //return $month;
          $academic_year_id=$month->pivot->academic_year_id;
          $academic_year=AcademicYear::find($academic_year_id);
          $years[$academic_year_id]=$academic_year;
        }
       //return $years;
       $all_pads=[];
       $year_payments=[];
       $all_year_payments=[];
       foreach($years as $year){

            $pads=[];
            foreach($tvetStudent->month_payments as $month){
                if($year->id=$month->pivot->academic_year_id){
                $pads['month']=$month->name;
                $pads['pad_no']=$month->pivot->receipt_no;
              //  return $pads;
                $all_pads[]=$pads;

                }

            }
            $year_payments[$year->year]=$all_pads;
           //return $year_payments;
          //return   $all_payment=array_merge($all_year_payments,$year_payments);
              return $all_year_payments[]=$year_payments;

        }
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
