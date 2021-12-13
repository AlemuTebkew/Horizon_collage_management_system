<?php

namespace App\Http\Controllers;

use App\Http\Resources\DegreeFee\DegreeStudentFeeResource;
use App\Http\Resources\DegreeFee\DegreeStudentsFeeResource;
use App\Http\Resources\DegreeFee\StudentFeeResource;
use App\Models\AcademicYear;
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

         $academic_year=AcademicYear::where('status',1)->first();
          foreach (DegreeStudent::all() as  $degreeStudent) {
              foreach ($degreeStudent->semesters as $value) {
                $paid_months=[];

                 if ($value->pivot->tution_type == 'cp') {
                    foreach ($degreeStudent->semester_payments as $semester) {
                       $all_pads=[];
                       if ($semester->academic_year->id == $academic_year->id) {
                          foreach ($semester->months as $month) {
                              $pads=[];
                              $pads['month']=$month->name;
                              $pads['pad_no']=$semester->pivot->receipt_no;
                              $all_pads[]=$pads;

                          }
                       }
                   $paid_months=  array_merge($paid_months,$all_pads);

                      }
                 }else {
                    $all_pads=[];
                    foreach ($degreeStudent->month_payments as $month) {

                                 $pads['month']=$month->name;
                                 $pads['pad_no']=$month->pivot->receipt_no;
                                 $all_pads[]=$pads;

                         }
                     $paid_months=  array_merge($paid_months,$all_pads);

                 }
              }

                $students[$degreeStudent->id]= $paid_months;
          }

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

        $monthly_paid= new DegreeStudentsFeeResource($degreeStudent->load('month_payments'));
        $semester_paid= new StudentFeeResource($degreeStudent->load('semester_payments'));

        return response()->json([
            'monthly_paid'=>$monthly_paid,
            'semester_paid'=>$semester_paid
        ]);

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
