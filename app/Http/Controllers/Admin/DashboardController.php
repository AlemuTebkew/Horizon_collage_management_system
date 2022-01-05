<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeStudent;
use App\Models\TvetStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData(){



        $per_page=request()->has('per_page') ? request('per_page') : 5;

        $year=null;
        if (request()->filled('year')) {
            $year=request('year');
        }else{
            // $year=AcademicYear::where('is_current',1)->first()->year;
            $year=2021;
        }

      $degree_payment_query=DB::table('degree_student_month')
                              ->whereNotNull('receipt_no')
                              ->whereYear('paid_date',$year)
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
                                    ->whereYear('paid_date',$year)

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
                                  ->whereYear('degree_other_fees.paid_date',$year)
                                  ->select('degree_students.id','student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),'degree_other_fees.paid_date',
                                          'degree_other_fees.receipt_no','degree_other_fees.paid_amount as amount','fee_types.name as payment_type')
                                          // ->unionAll($degree_students_tuition_fee)
                                          // ->get()
                                          ;

  //  return $degree_students_other_fee;

      $tvet_payment_query=DB::table('tvet_student_month')
                              ->whereNotNull('receipt_no')
                              ->whereYear('paid_date',$year)
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
                                  ->whereYear('paid_date',$year)
                                  ->select('tvet_students.id','student_id',
                                      DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                                      'paid_date','receipt_no','amount','fee_types.name AS payment_type')
                                      // ->get()
                                      ;

               $tvet_students_other_fee=DB::table('tvet_students')
               ->join('tvet_other_fees','tvet_students.id','=','tvet_other_fees.tvet_student_id')
               ->join('fee_types','fee_types.id','=','tvet_other_fees.fee_type_id')
               ->whereNotNull('receipt_no')
               ->whereYear('paid_date',$year)
               ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),'tvet_other_fees.paid_date',
               'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount','fee_types.name as payment_type');


        $students_all_fee=    $tvet_students_other_fee
                              ->unionAll($tvet_students_tuition_fee)
                              ->unionAll($degree_students_tuition_fee)
                              ->unionAll($degree_students_other_fee);



            $querySql = $students_all_fee->toSql();
            $query = DB::table(DB::raw("($querySql ) as a"))
            ->mergeBindings($students_all_fee);
             $sum=0;
             $sum=$query->sum('paid_amount');

                $month_sum=  $query->whereDate('paid_date','>',now()->subMonth())->sum('paid_amount') ;
                 $day7_sum=  $query->whereDate('paid_date','>',now()->subDays(7))->sum('paid_amount') ;
                $hour_sum=  $query->whereDate('paid_date','>',now()->subHours(24))->sum('paid_amount') ;

                // return true;
                return response()->json([
                    'total'=>$sum,
                    '24hour'=>$hour_sum,
                     '7day'=>$day7_sum,
                    '$month'=>$month_sum,
                 ],200);

            }


public function otherWay(){

    $year=null;
    if (request()->filled('year')) {
        $year=request('year');
    }else{
        // $year=AcademicYear::where('is_current',1)->first()->year;
        $year=2021;
    }
    $total=0;
  $ds=  DegreeStudent::whereHas('month_payments',function($q) use($year){
        // $q->where('academic_year_id',1);
        $q->whereYear('paid_date',$year);
    })
     ->with('month_payments')
     ->get();

    //  return $ds->pluck('id');
    foreach ($ds as $s) {

         foreach ($s->month_payments as $m) {
             $total+=$m->pivot->paid_amount;
         }
    }

    $dos=  DegreeStudent::whereHas('degree_other_fees',function($q) use($year){
        // $q->where('academic_year_id',1);
        $q->whereYear('paid_date',$year);

    })
     ->with('degree_other_fees')
     ->get();

    //  return $ds->pluck('id');
    foreach ($dos as $s) {

         foreach ($s->degree_other_fees as $m) {
             $total+=$m->pivot->paid_amount;
         }
    }

    $ts=  TvetStudent::whereHas('month_payments',function($q) use($year){
        // $q->where('academic_year_id',1);
        $q->whereYear('paid_date',$year);

    })
     ->with('month_payments')
     ->get();
    //  return $ts->pluck('id');


    foreach ($ts as $s) {

         foreach ($s->month_payments as $m) {
             $total+=$m->pivot->paid_amount;
         }
    }

    $tos=  TvetStudent::whereHas('tvet_other_fees',function($q) use($year){
        // $q->where('academic_year_id',1);
        $q->whereYear('paid_date',$year);
    })
     ->with('tvet_other_fees')
     ->get();
    //  return $ts->pluck('id');


    foreach ($tos as $s) {

         foreach ($s->tvet_other_fees as $m) {
             $total+=$m->pivot->paid_amount;
         }
    }

    return $total;
  }

 }
