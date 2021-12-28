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

        $all=[];
        $student=[];

        //getting all students
        $academic_year_id=null;
        if (request()->has('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        $tvetStudents=TvetStudent::whereHas('month_payments',function( $query) use($academic_year_id){
            $query->where('tvet_student_month.academic_year_id',$academic_year_id);
          })->with('month_payments')->get();

        if ($tvetStudents) {
          foreach($tvetStudents as $tvtStudent){

            $student['id']=$tvtStudent->id;
            $student['full_name']=$tvtStudent->full_name;
            $student['sex']=$tvtStudent->sex;
            $month_pad=[];
            $total=0;
            $month_payments=$tvtStudent->month_payments()
            ->wherePivot('academic_year_id',$academic_year_id)
            ->orderBy('number')
            ->get();
            foreach ($month_payments as $month) {

                if($month->pivot->academic_year_id == $academic_year_id){

                    $month_pad[$month->name] = $month->pivot->receipt_no;
                    $total+=(double)$month->pivot->paid_amount;

                }
            }
            $student['total']=$total;
            $student['pads']=$month_pad;

            $all[]=$student;
            // return $student;
        }
        return response()->json($all,200);
     }else {
        return response()->json('no student',404);
    }

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
        if ($tvetStudent) {
            $years=[];
            if ($tvetStudent->has('month_payments')) {
                foreach($tvetStudent->month_payments as $month){
                    //check through relationship method
                    //return $month;
                $academic_year_id=$month->pivot->academic_year_id;
                $academic_year=AcademicYear::find($academic_year_id);
                $years[$academic_year_id]=$academic_year;
                }
        }

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
                $month_payments= $tvetStudent->month_payments()
                ->orderBy('number')
                ->wherePivot('academic_year_id',$y->id)->get();
                $total=0.0;
                foreach($month_payments as $month_payment){
                    if($y->id=$month_payment->pivot->academic_year_id){
                // $pads['month']=$month->name;
                    $pads[$month_payment->name]=$month_payment->pivot->receipt_no;
                    $total+=number_format ($month_payment->pivot->paid_amount);

                    }

                }

                $year['total']=$total;
                $year['months']=$pads;
                $student['years'][]=$year;
            }
            return  response()->json($student,200);

        }else {
            return response()->json('no student',404);
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
