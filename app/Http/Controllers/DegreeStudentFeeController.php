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
        $all=[];
        $student=[];


        //getting all students
        $academic_year_id=null;
        if (request()->has('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }


        $degreeStudents=DegreeStudent::whereHas('month_payments')
                                     ->with('month_payments')->get();
          foreach ($degreeStudents as  $degreeStudent) {

                 $student['id']=$degreeStudent->id;
                 $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $month_pad=[];
                 $total=0;
                  $month_payments=$degreeStudent->month_payments()
                  ->wherePivot('academic_year_id',$academic_year_id)->get();
                    foreach ($month_payments as $month) {
                  //    return  $month;
                        if($month->pivot->academic_year_id == $academic_year_id){

                            $month_pad[$month->name] = $month->pivot->receipt_no;
                            $total+=(double)$month->pivot->paid_amount;

                         }
                        }

           $student['total']=$total;
           $student['pads']=$month_pad;

           $all[]=$student;


          }
        //   $chunks = collect($all)->chunk(2);
        //  return $chunks->toArray();
         return response()->json( collect($all)->paginate());
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

       // return $degreeStudent;
        $all=[];
        $student=[];
        $years=[];
       // $academic_year=AcademicYear::where('status',1)->first();
        // $degreeStudents=DegreeStudent::whereHas('month_payments')->with('month_payments')->get();
       //return $degreeStudent->semesters;
        if ($degreeStudent->semesters) {
            foreach($degreeStudent->semesters as $semester){
            $academic_year_id=$semester->academic_year_id;
            $academic_year=AcademicYear::find($academic_year_id);
            $years[$academic_year_id]=$academic_year;
            }
        }else {
            return 'not paid';
        }


        $student['id']=$degreeStudent->id;
        $student['student_id']=$degreeStudent->student_id;
        $student['full_name']=$degreeStudent->full_name;
        $student['department']=$degreeStudent->degree_department->name;
        $student['program']=$degreeStudent->program->name;
        $student['year_no']=$degreeStudent->current_year_no;

        $year=[];
        foreach ($years as $y) {
            // return $years;
            $year['year']=$y->year;
            $year['semesters']=null;
            $semester=[];
            $j=0;
        //   $semesters=$y->semesters->where('program_id',$degreeStudent->program->id );
            $semesters=$degreeStudent->semesters
            ->where('academic_year_id',$y->id);
            foreach ($semesters as  $s) {
            $semester['id']=$s->id;
            $semester['semester_no']=$s->number;
            $semester['tution_type']=$s->pivot->tuition_type;
            $total_pads=[];
            $total=0.0;
            foreach ($s->months as $month) {
                $month_pad=[];

                $month_payments= $degreeStudent->month_payments
                ->where('pivot.academic_year_id',$y->id);
              // $m=$paid[$j];
              foreach ($month_payments as $month_payment) {

                if($month_payment->pivot->academic_year_id == $s->academic_year_id){

                    if ($month->id == $month_payment->id) {

                    $month_pad['id']=$month_payment->id;
                    $month_pad['name']=$month_payment->name;
                    $month_pad['pad']=$month_payment->pivot->receipt_no;
                    $month_pad['paid_date']= $month_payment->pivot->paid_date;
                    $total+=number_format ($month_payment->pivot->paid_amount);


                    $total_pads[]= $month_pad;
                    }
                 }


        }
            }
            $semester['total']= $total;
            $semester['months']= $total_pads;

            $year['semesters'][]=$semester;
        }
       $student['years'][]=$year;
    }
   return response()->json( $student);

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
