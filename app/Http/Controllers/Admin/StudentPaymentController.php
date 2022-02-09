<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeStudent;
use App\Models\TvetStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller{

    public function getPaidStudents(){


        // return request('search_query');

        $per_page=request()->has('per_page') ? request('per_page') : 5;


        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            // $year=2022;
            // $year=1970;
        }

        // return $academic_year_id;
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
                                        DB::raw('DATE(paid_date) AS paid_date'),
                                        DB::raw('"dm" as type')
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
                                  ->select('degree_students.id','student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                //   'degree_other_fees.paid_date',
                                  DB::raw('DATE(degree_other_fees.paid_date) AS paid_date'),
                                  DB::raw('"do" as type'),

                                          'degree_other_fees.receipt_no','degree_other_fees.paid_amount as amount','fee_types.name as payment_type')
                                          // ->unionAll($degree_students_tuition_fee)
                                          // ->get()
                                          ;

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

                                  ->select('tvet_students.id','student_id',
                                      DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                                    //   'paid_date'
                                    DB::raw('"tm" as type'),

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
               ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
               DB::raw('DATE(tvet_other_fees.paid_date) AS paid_date')
               ,
               DB::raw('"to" as type'),

            //    'tvet_other_fees.paid_date',
               'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount','fee_types.name as payment_type');


        $students_all_fee=    $tvet_students_other_fee
                              ->unionAll($tvet_students_tuition_fee)
                              ->unionAll($degree_students_tuition_fee)
                              ->unionAll($degree_students_other_fee);

            // union all doesnit remove duplicates
            $querySql = $students_all_fee->toSql();
            if (request('search_query')) {
                $query1 = DB::table(DB::raw('('.$querySql.' ) as a  where receipt_no = '.request('search_query').''))
                ->mergeBindings($students_all_fee);

            }else {
                $query1 = DB::table(DB::raw('('.$querySql.' ) as a  '))
                ->mergeBindings($students_all_fee);
            }

            // $query2=clone $query1;

        //     $all_fee= $query1->
        //     orderByDesc('paid_amount')
        //   ->when(request('search_query') ,function($query){
        //    // return $query->where('receipt_no',request('search_query'));
        // });
        $all_fee= $query1->paginate($per_page);

                return response()->json(
                     $all_fee
                ,200);


        }



        public function getDailyPaidAmount(){


            // return request('search_query');

            $per_page=request()->has('per_page') ? request('per_page') : 5;


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

                                //   ->crossJoin('tvet_student_month')
                                //   ->crossJoin('degree_other_fees')
                                //   ->crossJoin('tvet_other_fees')
                                  ->select(
                                //   'paid_date'
                                DB::raw('DATE(paid_date) AS paid_date')

                                  ,DB::raw("SUM(paid_amount) AS amount")
                                 )
                                  ->groupBy('paid_date')
                                ;

                                $tvet_payment_query=DB::table('tvet_student_month')
                                ->whereNotNull('receipt_no')
                                ->where('academic_year_id',$academic_year_id)

                              //   ->crossJoin('tvet_student_month')
                              //   ->crossJoin('degree_other_fees')
                              //   ->crossJoin('tvet_other_fees')
                                ->select(
                              //   'paid_date'
                              DB::raw('DATE(paid_date) AS paid_date')

                                ,DB::raw("SUM(paid_amount) AS amount")
                               )
                                ->groupBy('paid_date')
                              ;
                              //  return $q;


                              $degree_other_query=DB::table('degree_other_fees')
                              ->whereNotNull('receipt_no')
                              ->where('academic_year_id',$academic_year_id)

                            //   ->crossJoin('tvet_student_month')
                            //   ->crossJoin('degree_other_fees')
                            //   ->crossJoin('tvet_other_fees')
                              ->select(
                            //   'paid_date'
                            DB::raw('DATE(paid_date) AS paid_date')

                              ,DB::raw("SUM(paid_amount) AS amount")
                             )
                              ->groupBy('paid_date')
                            ;
                            $tvet_other_query=DB::table('tvet_other_fees')
                            ->whereNotNull('receipt_no')
                            ->where('academic_year_id',$academic_year_id)

                          //   ->crossJoin('tvet_student_month')
                          //   ->crossJoin('degree_other_fees')
                          //   ->crossJoin('tvet_other_fees')
                            ->select(
                          //   'paid_date'
                          DB::raw('DATE(paid_date) AS paid_date')

                            ,DB::raw("SUM(paid_amount) AS amount")
                           )
                            ->groupBy('paid_date')
                          ;
                              $students_all_fee=
                                $tvet_payment_query
                              ->unionAll($degree_payment_query)
                              ->unionAll($tvet_other_query)
                              ->unionAll($degree_other_query)
                            ;

            // union all doesnit remove duplicates
            $querySql = $students_all_fee->toSql();

                $query1 = DB::table(DB::raw('('.$querySql.' ) as a '))
                ->select('paid_date',DB::raw("SUM(amount) AS daily_amount"))
                ->groupBy('paid_date')->orderByDesc('paid_date')
                ->mergeBindings($students_all_fee);



            $all_fee= $query1->paginate($per_page);

                    return response()->json(
                         $all_fee
                    ,200);


            }




    public function deletePayment($id){

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }


           if (request('type') == 'dm') {

           return  DB::table('degree_student_month')
             ->where('degree_student_id',$id)
             ->where('academic_year_id',$academic_year_id)
             ->where('receipt_no',request('receipt_no'))->update([
                 'fee_type_id'=>null,
                 'paid_amount'=>0.0,
                 'paid_date'=>null,
                 'receipt_no'=>null,
                 'is_paid'=>0,
             ]);

            }else if (request('type') == 'do') {


          return  DB::table('degree_other_fees')
            ->where('degree_student_id',$id)
            ->where('academic_year_id',$academic_year_id)
            ->where('receipt_no',request('receipt_no'))->update([
                'fee_type_id'=>null,
                'paid_amount'=>0.0,
                'paid_date'=>null,
                'receipt_no'=>null,
                'is_paid'=>0,
            ]);

           }else if (request('type') == 'tm') {
            $student=TvetStudent::find($id);

         return   DB::table('tvet_student_month')
            ->where('tvet_student_id',$id)
            ->where('academic_year_id',$academic_year_id)
            ->where('receipt_no',request('receipt_no'))->update([
                'fee_type_id'=>null,
                'paid_amount'=>0.0,
                'paid_date'=>null,
                'receipt_no'=>null,
                'is_paid'=>0,
            ]);


          }else if (request('type') == 'to') {

         return   DB::table('tvet_other_fees')
            ->where('tvet_student_id',$id)
            ->where('academic_year_id',$academic_year_id)
            ->where('receipt_no',request('receipt_no'))->update([
                'fee_type_id'=>null,
                'paid_amount'=>0.0,
                'paid_date'=>null,
                'receipt_no'=>null,
                'is_paid'=>0,
            ]);
          }

          return response()->json(['not deleted'],501);
        }
    }
