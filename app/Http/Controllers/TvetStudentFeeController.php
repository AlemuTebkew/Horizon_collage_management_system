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
        $all=[];
        $student=[];
        $tvetStudents=TvetStudent::whereHas('month_payments')
                                 ->with('month_payments')->get();
       foreach($tvetStudents as $tvtStudent){

        $student['id']=$tvtStudent->id;
        $student['full_name']=$tvtStudent->full_name;
        $student['sex']=$tvtStudent->sex;
        $month_pad=[];
        $total=0;
           foreach ($tvtStudent->month_payments as $month) {


            if($month->pivot->academic_year_id == $academic_year->id){

                $month_pad[$month->name] = $month->pivot->receipt_no;
                $total+=(double)$month->pivot->paid_amount;

            }
       }
       $student['total']=$total;
       $student['pads']=$month_pad;

       $all[]=$student;
        // return $student;
    }
     return response()->json($all);
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
        $years=[];
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
       $student['id']=$tvetStudent->id;
       $student['student_id']=$tvetStudent->student_id;
       $student['full_name']=$tvetStudent->full_name;
       $student['department']=$tvetStudent->tvet_department->name;
       $student['program']=$tvetStudent->program->name;
       $student['level_no']=$tvetStudent->current_level_no;

       $year=[];
       foreach($years as $y){
            $year['year']=$y->year;
            $pads=[];
            foreach($tvetStudent->month_payments as $month){
                if($y->id=$month->pivot->academic_year_id){
               // $pads['month']=$month->name;
                $pads[$month->name]=$month->pivot->receipt_no;
               // $all_pads[]=$pads;

                }

            }

            $year['total']=0;
            $year['months']=$pads;
            $student['years'][]=$year;
        }

        return $student;

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
