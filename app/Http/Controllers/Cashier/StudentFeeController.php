<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeStudent;
use App\Models\FeeType;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentFeeController extends Controller
{
    public function studentsPaid(){


      $degree_payment_query=DB::table('degree_student_month')
                              ->whereNotNull('receipt_no')
                              // ->distinct('receipt_no')
                              ->select('receipt_no','degree_student_id','paid_date','fee_type_id',DB::raw("SUM(paid_amount) AS amount")
                              ,DB::raw("count(receipt_no) AS count"))
                              ->groupBy('receipt_no')
                              ->groupBy('paid_date')
                              ->groupBy('fee_type_id')
                              ->groupBy('degree_student_id');
                          //  return $q;


     // $q->join('degree_students','degree_students.id','=','.degree_student_id');

    $degree_students_tuition_fee = DB::table('degree_students')
                                    ->joinSub($degree_payment_query, 'payment', function ($join) {
                                        $join->on('degree_students.id', '=', 'payment.degree_student_id');

                                    })
                                    ->join('fee_types','fee_types.id','=','fee_type_id')
                                    ->select('degree_students.id','student_id',
                                        DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                        'paid_date','receipt_no','amount','fee_types.name AS payment_type')
                                        // ->get()
                                        ;

      // return $degree_students_tuition_fee;

  ///////////////////////

      $degree_students_other_fee=DB::table('degree_students')
                                  ->join('degree_other_fees','degree_students.id','=','degree_other_fees.degree_student_id')
                                  ->join('fee_types','fee_types.id','=','degree_other_fees.fee_type_id')
                                  ->whereNotNull('receipt_no')
                                  ->select('degree_students.id','student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),'degree_other_fees.paid_date',
                                          'degree_other_fees.receipt_no','degree_other_fees.paid_amount as amount','fee_types.name as payment_type')
                                          // ->unionAll($degree_students_tuition_fee)
                                          // ->get()
                                          ;

  //  return $degree_students_other_fee;

      $tvet_payment_query=DB::table('tvet_student_month')
                              ->whereNotNull('receipt_no')
                              // ->distinct('receipt_no')
                              ->select('receipt_no','tvet_student_id','paid_date','fee_type_id',DB::raw("SUM(paid_amount) AS amount") ,DB::raw("count(receipt_no) AS count"))
                              ->groupBy('receipt_no')
                              ->groupBy('paid_date')
                              ->groupBy('fee_type_id')
                              ->groupBy('tvet_student_id')
                              // ->get()
                              ;
  //  return $tvet_payment_query;

      $tvet_students_tuition_fee = DB::table('tvet_students')
                                  ->joinSub($tvet_payment_query, 'payment', function ($join) {
                                      $join->on('tvet_students.id', '=', 'payment.tvet_student_id');
                                  })
                                  ->join('fee_types','fee_types.id','=','fee_type_id')
                                  ->select('tvet_students.id','student_id',
                                      DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                                      'paid_date','receipt_no','amount','fee_types.name AS payment_type')
                                      // ->get()
                                      ;

              // return $tvet_students_tuition_fee;


        $students_all_fee=DB::table('tvet_students')
                              ->join('tvet_other_fees','tvet_students.id','=','tvet_other_fees.tvet_student_id')
                              ->join('fee_types','fee_types.id','=','tvet_other_fees.fee_type_id')
                              ->whereNotNull('receipt_no')
                              ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),'tvet_other_fees.paid_date',
                              'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount','fee_types.name as payment_type')
                              ->unionAll($tvet_students_tuition_fee)
                              ->unionAll($degree_students_tuition_fee)
                              ->unionAll($degree_students_other_fee)
                              ->get();
            return $students_all_fee;

    }

    public function getStudentPaymentDetail(){
        $academic_year=AcademicYear::where('status',1)->first();
        $student=[];
        $mf_id=FeeType::where('name','Monthly Fee')->first()->id;
        $cpf_id=FeeType::where('name','CP Fee')->first()->id;
        $month_fee=$academic_year->academic_fees()->where('fee_type_id',$mf_id)->first()->amount;
        $cp_fee=$academic_year->academic_fees()->where('fee_type_id',$cpf_id)->first()->amount;

        // return $cp_fee;

        if (request('type') == 'degree') {
            $degreeStudent=DegreeStudent::where('student_id',request('student_id'))->first();
            if ($degreeStudent) {


                 $student['id']=$degreeStudent->id;
                 $student['student_id']=$degreeStudent->student_id;
                 $student['full_name']=$degreeStudent->full_name;
                 $student['department']=$degreeStudent->degree_department->name;
                 $student['program']=$degreeStudent->program->name;
                 $student['year_no']=$degreeStudent->current_year_no;
                 $student['month_payment']=$month_fee;
                 $student['cp_payment']=$cp_fee;

              $semesters=$degreeStudent->semesters
                         ->where('academic_year_id',$academic_year->id);

                        //return $semesters;
              foreach ($semesters as $s) {


                  if ($s->pivot->tuition_type == 'monthly' || $s->pivot->tuition_type == null ) {
                  $total_cp= $this->getTotalCp($degreeStudent,$s);
                    $semester['id']=$s->id;
                    $semester['semester_no']=$s->number;
                    $semester['tution_type']=$s->pivot->tuition_type;
                    $semester['semester_payment']=$cp_fee * $total_cp;

                    $total=0;
                    $total_pad=[];
                //    return $s->months;
             //   return $degreeStudent->month_payments ->where('pivot.academic_year_id',$academic_year->id);
                 foreach ($s->months as $month) {
                     $month_payments=$degreeStudent->month_payments
                     ->where('pivot.academic_year_id',$academic_year->id);
                    foreach ($month_payments as $month_payment) {
                        if ($month->id == $month_payment->id) {
                            $month_pad['id']=$month_payment->id;
                            $month_pad['name']=$month_payment->name;
                            $month_pad['pad']=$month_payment->pivot->receipt_no;
                            $month_pad['paid_date']= $month_payment->pivot->paid_date;
                            $total+= ($month_payment->pivot->paid_amount);
                            $total_pad[]=$month_pad;
                        }

                    }
                }
                $semester['total']=$total;
                $semester['months']=$total_pad;
                $student['semesters'][]=$semester;
                  }else if ($s->pivot->tuition_type == 'cp' ) {
                            $semester=[];
                        $semester['id']=$s->id;
                        $semester['semester_no']=$s->number;
                        $semester['tution_type']=$s->pivot->tuition_type;
                        $total=0;
                        $month_pad=null;
                    //    return $s->months;
                 //   return $degreeStudent->month_payments ->where('pivot.academic_year_id',$academic_year->id);
                     foreach ($s->months as $month) {
                         $month_payments=$degreeStudent->month_payments
                         ->where('pivot.academic_year_id',$academic_year->id);
                        foreach ($month_payments as $month_payment) {
                            if ($month->id == $month_payment->id) {

                                $month_pad=$month_payment->pivot->receipt_no;
                              //  $month_pad= $month_payment->pivot->paid_date;
                                $total+= ($month_payment->pivot->paid_amount);
                               // $total_pad[]=$month_pad;
                            }

                        }
                    }
                    $semester['total']=$total;
                    $semester['pad']=$month_pad;
                    $student['cp'][]=$semester;
                  }


              }


           }else {
               return response()->json('student not found');
           }
        }else if (request('type') == 'tvet') {

        }

        return $student;

    }





    private function getTotalCp($student,$semester){
        $courses=Course::where('degree_department_id',$student->degree_department_id)
        ->where('program_id',$student->program_id)
        ->where('year_no',$semester->pivot->year_no)
        ->where('semester_no',$semester->pivot->semester_no)
        ->get();

    return $courses->sum('cp');
    }



}
