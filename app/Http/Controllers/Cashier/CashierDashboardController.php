<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierDashboardController extends Controller
{
    public function getDashboardData(){


        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            // $year=2022;
            // $year=1970;
        }

      $degree_payment_query=DB::table('degree_student_month')
                              ->whereNotNull('receipt_no')
                              ->where('academic_year_id',$academic_year_id)
                            //   ->whereYear('paid_date',$year)
                              ->select('receipt_no','degree_student_id',
                            //   'paid_date'
                            DB::raw('DATE(paid_date) AS paid_date')

                              ,'fee_type_id',DB::raw("SUM(paid_amount) AS amount")
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
                                    // ->whereYear('paid_date',$year)
                                    // ->where('academic_year_id',$academic_year_id)

                                    ->select('degree_students.id','student_id',
                                        DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                        // 'paid_date'
                                        DB::raw('DATE(paid_date) AS paid_date')

                                        ,'receipt_no','amount','fee_types.name AS payment_type')
                                        // ->get()
                                        ;

      // return $degree_students_tuition_fee;

  ///////////////////////

      $degree_students_other_fee=DB::table('degree_students')
                                  ->join('degree_other_fees','degree_students.id','=','degree_other_fees.degree_student_id')
                                  ->join('fee_types','fee_types.id','=','degree_other_fees.fee_type_id')
                                  ->whereNotNull('receipt_no')
                                //   ->whereYear('degree_other_fees.paid_date',$year)
                                  ->where('academic_year_id',$academic_year_id)
                                  ->where('tvet_students.is_graduated',0)->where('tvet_students.fully_scholarship',0)

                                  ->select('degree_students.id','student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                //   'degree_other_fees.paid_date',
                                  DB::raw('DATE(degree_other_fees.paid_date) AS paid_date'),

                                          'degree_other_fees.receipt_no','degree_other_fees.paid_amount as amount','fee_types.name as payment_type')
                                          // ->unionAll($degree_students_tuition_fee)
                                          // ->get()
                                          ;

        //    return $degree_students_other_fee

        //    ->whereDate('paid_date','>=', (new Carbon)->subDays(10)->startOfDay()->toDateString())
           //  ->whereDate('paid_date','<', strval(2022-01-07))
        //    ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
           //  ->get();
        //    ->where('paid_date','>=',request('search_query') )
        //    ->where('paid_date','<=',now()->day('Y-m-d') )
        //    ->get();
  //  return $degree_students_other_fee;

      $tvet_payment_query=DB::table('tvet_student_month')
                              ->whereNotNull('receipt_no')
                            //   ->whereYear('paid_date',$year)
                              ->where('academic_year_id',$academic_year_id)

                              ->select('receipt_no','tvet_student_id',
                            //   'paid_date'
                            DB::raw('DATE(paid_date) AS paid_date')

                              ,'fee_type_id',DB::raw("SUM(paid_amount) AS amount") ,DB::raw("count(receipt_no) AS count"))
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
                                //   ->whereYear('paid_date',$year)
                                // ->where('academic_year_id',$academic_year_id)
                                ->where('tvet_students.is_graduated',0)->where('tvet_students.fully_scholarship',0)

                                  ->select('tvet_students.id','student_id',
                                      DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                                    //   'paid_date'
                                      DB::raw('DATE(paid_date) AS paid_date')

                                      ,'receipt_no','amount','fee_types.name AS payment_type')
                                    //   ->get()
                                      ;

               $tvet_students_other_fee=DB::table('tvet_students')
               ->join('tvet_other_fees','tvet_students.id','=','tvet_other_fees.tvet_student_id')
               ->join('fee_types','fee_types.id','=','tvet_other_fees.fee_type_id')
               ->whereNotNull('receipt_no')
            //    ->whereYear('paid_date',$year)
                ->where('tvet_other_fees.academic_year_id',$academic_year_id)
                ->where('tvet_students.is_graduated',0)->where('tvet_students.fully_scholarship',0)

               ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
               DB::raw('DATE(tvet_other_fees.paid_date) AS paid_date')
               ,
            //    'tvet_other_fees.paid_date',
               'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount as amount','fee_types.name as payment_type');

               $allItems = new \Illuminate\Database\Eloquent\Collection;
                //Create empty collection which we know has the merge() method
                /**
                 * also simply as $allItems=collect();// creating empty collection
                 * instead of merge use concat since similar database id
                 */
                $allItems = $allItems->concat($tvet_students_other_fee->get());
                $allItems = $allItems->concat($tvet_students_tuition_fee->get());
                $allItems = $allItems->concat($degree_students_tuition_fee->get());
                $allItems = $allItems->concat($degree_students_other_fee->get());
                   $allItems= $allItems->sortByDesc('paid_date')->take(10)->values();


             $m1=   $tvet_students_other_fee ->whereDate('paid_date','>=', (new Carbon())->subDays(30)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
              $m2=  $tvet_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(30)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $m3= $degree_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(30)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $m4= $degree_students_other_fee->whereDate('paid_date','>=', (new Carbon())->subDays(30)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');

                                         $month_sum=$m1+$m2+$m3+$m4;


               $w1=   $tvet_students_other_fee ->whereDate('paid_date','>=', (new Carbon())->subDays(7)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $w2=  $tvet_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(7)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $w3= $degree_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(7)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $w4= $degree_students_other_fee->whereDate('paid_date','>=', (new Carbon())->subDays(7)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');

                                         $weak_sum=$w1+$w2+$w3+$w4;




               $d1=   $tvet_students_other_fee ->whereDate('paid_date','>=', (new Carbon())->subDays(1)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $d2=  $tvet_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(1)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $d3= $degree_students_tuition_fee->whereDate('paid_date','>=', (new Carbon())->subDays(1)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');
               $d4= $degree_students_other_fee->whereDate('paid_date','>=', (new Carbon())->subDays(1)->startOfDay()->toDateString())
                                         ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
                                         ->get()->sum('amount');

                                         $day_sum=$d1+$d2+$d3+$d4;





            return response()->json([
                 '24hour'=>$day_sum,
                 '7day'=>$weak_sum,
                 'month'=>$month_sum,
                 'all_fee'=>$allItems
             ],200);

    }
}
