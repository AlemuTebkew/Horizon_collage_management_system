<?php

namespace App\Http\Controllers;

use App\Http\Resources\DegreeFee\DegreeStudentFeeResource;
use App\Http\Resources\DegreeFee\DegreeStudentsFeeResource;
use App\Http\Resources\DegreeFee\StudentFeeResource;
use App\Models\AcademicYear;
use App\Models\DegreeStudent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class DegreeStudentFeeController extends Controller
{

    public function index()
    {
        $all=[];
        $student=[];


        //getting all students
        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        $academic_year=AcademicYear::find($academic_year_id);
        $degreeStudents=DegreeStudent::whereHas('month_payments',function( $query) use($academic_year_id){
            $query->where('degree_student_month.academic_year_id',$academic_year_id);
        })->with('month_payments')->get();

          foreach ($degreeStudents as  $degreeStudent) {

                 $student['id']=$degreeStudent->id;
                 $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $month_pad=[];
                 $total=0.0;
                  $month_payments=$degreeStudent->month_payments()
                  ->wherePivot('academic_year_id',$academic_year_id)
                  ->orderBy('number')
                  ->get();
                  foreach ($academic_year->months as $month) {

                      $month_pad=[];
                    foreach ($month_payments as $month_payment) {
                  //    return  $month;
                        if($month_payment->pivot->academic_year_id == $academic_year_id){

                            if ($month->id == $month_payment->id)  {
                                $month_pad = $month_payment->pivot->receipt_no;
                                $total+=(double)$month_payment->pivot->paid_amount;
                               break;
                            }else {
                                $month_pad =null;
                                break;
                            }


                         }
                        }



           $pads[$month->name]=$month_pad;



            }
            $student['total']=$total;
            $student['pads']=$pads;
            $all[]=$student;
          }
        //   $chunks = collect($all)->chunk(2);
        //  return $chunks->toArray();
         return response()->json( $all);
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

        $student=[];
        $years=[];
        if ($degreeStudent) {
            if ($degreeStudent->has('semesters')) {
                foreach($degreeStudent->semesters as $semester){
                $academic_year_id=$semester->academic_year_id;
                $academic_year=AcademicYear::find($academic_year_id);
                $years[$academic_year_id]=$academic_year;
                }
            }else {
                return 'not registerd';
            }


            $student['id']=$degreeStudent->id;
            $student['student_id']=$degreeStudent->student_id;
            $student['full_name']=$degreeStudent->full_name;
            $student['department']=$degreeStudent->degree_department->name;
            $student['program']=$degreeStudent->program->name;
            $student['year_no']=$degreeStudent->current_year_no;

            $year=[];
            foreach ($years as $y) {

                $year['year']=$y->year;
                $year['semesters']=[];
                $semester=[];
                $semesters=$degreeStudent->semesters()
                ->where('semesters.academic_year_id',$y->id)->get();
                foreach ($semesters as  $s) {
                    $semester['id']=$s->id;
                    $semester['semester_no']=$s->number;
                    $semester['tution_type']=$s->pivot->tuition_type;
                    $total_pads=[];
                    $total=0.0;
                    if ($s->has('months')) {

                    foreach ($s->months as $month) {
                        $month_pad=[];

                        $month_payments= $degreeStudent->month_payments()
                        ->orderBy('number')
                        ->wherePivot('academic_year_id',$y->id)->get();

                        foreach ($month_payments as $month_payment) {

                            if($month_payment->pivot->academic_year_id == $s->academic_year_id){

                                if ($month->id == $month_payment->id) {

                                $month_pad['id']=$month_payment->id;
                                $month_pad['name']=$month_payment->name;
                                $month_pad['pad']=$month_payment->pivot->receipt_no;
                                $month_pad['paid_date']= $month_payment->pivot->paid_date;
                                $total+=number_format ($month_payment->pivot->paid_amount);

                                $total_pads[]= $month_pad;
                                break;
                                }else {
                                    $month_pad['id']=$month->id;
                                    $month_pad['name']=$month->name;
                                    $month_pad['pad']=null;
                                     $month_pad['paid_date']=null;
                                     $total_pads[]= $month_pad;
                                   break;
                                }

                            }

                    }
                    }

                    }
                    if ($total_pads) {
                        $semester['total']= $total;
                        $semester['months']= $total_pads;
                        $year['semesters'][]=$semester;
                    }

            }
            if ($year) {
                $student['years'][]=$year;
            }

           }
          return response()->json( $student,200);
        }else {
            return response()->json('no student',404);
     }

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
