<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeStudent;
use App\Models\FeeType;
use App\Models\Semester;
use App\Models\TvetStudent;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentFeeController extends Controller
{
    public function studentsPaid(){


        $per_page=request()->has('per_page') ? request('per_page') : 20;

        $year=null;
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
                              ->groupBy('degree_student_id')
                             // ->having('receipt_no',null)

                            //   ->toSql()
                            ;
                          //  return $q;


     // $q->join('degree_students','degree_students.id','=','.degree_student_id');

    $degree_students_tuition_fee = DB::table('degree_students')
                                   ->where('degree_students.is_graduated',0)->where('degree_students.fully_scholarship',0)

                                    ->joinSub($degree_payment_query, 'payment', function ($join) {
                                        $join->on('degree_students.id', '=', 'payment.degree_student_id')
                                        ;

                                    })
                                    ->join('fee_types','fee_types.id','=','fee_type_id')
                                    // ->whereYear('paid_date',$year)
                                    // ->where('academic_year_id',$academic_year_id)

                                    ->select('degree_students.id','student_id',
                                        DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                        // 'paid_date'
                                        DB::raw('DATE(paid_date) AS paid_date')

                                        ,'receipt_no','amount','fee_types.name AS payment_type')
                                        // ->toSql()

                                        // ->get()
                                        ;

      // return $degree_students_tuition_fee;

  ///////////////////////

      $degree_students_other_fee=DB::table('degree_students')
                                 ->where('degree_students.is_graduated',0)->where('degree_students.fully_scholarship',0)
                                  ->join('degree_other_fees','degree_students.id','=','degree_other_fees.degree_student_id')
                                  ->join('fee_types','fee_types.id','=','degree_other_fees.fee_type_id')
                                  ->whereNotNull('receipt_no')
                                //   ->whereYear('degree_other_fees.paid_date',$year)
                                  ->where('academic_year_id',$academic_year_id)

                                  ->select('degree_students.id','student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                                  'degree_other_fees.paid_date',
                                //   DB::raw('DATE(degree_other_fees.paid_date) AS paid_date'),

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

                              ,'fee_type_id',DB::raw("SUM(paid_amount) AS amount")
                              ,DB::raw("count(receipt_no) AS count"))
                              ->groupBy('receipt_no')
                              ->groupBy('paid_date')
                              ->groupBy('fee_type_id')
                              ->groupBy('tvet_student_id')
                              // ->get()
                              ;
  //  return $tvet_payment_query;

      $tvet_students_tuition_fee = DB::table('tvet_students')
                                 ->where('tvet_students.is_graduated',0)->where('tvet_students.fully_scholarship',0)

                                  ->joinSub($tvet_payment_query, 'payment', function ($join) {
                                      $join->on('tvet_students.id', '=', 'payment.tvet_student_id');

                                    })
                                  ->join('fee_types','fee_types.id','=','fee_type_id')
                                //   ->whereYear('paid_date',$year)
                                // ->where('academic_year_id',$academic_year_id)

                                  ->select('tvet_students.id','student_id',
                                      DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                                    //   'paid_date'
                                      DB::raw('DATE(paid_date) AS paid_date')

                                      ,'receipt_no','amount','fee_types.name AS payment_type')
                                    //   ->get()
                                    // ->toSql()
                                    ;

               $tvet_students_other_fee=DB::table('tvet_students')
               ->where('tvet_students.is_graduated',0)->where('tvet_students.fully_scholarship',0)

               ->join('tvet_other_fees','tvet_students.id','=','tvet_other_fees.tvet_student_id')
               ->join('fee_types','fee_types.id','=','tvet_other_fees.fee_type_id')
               ->whereNotNull('receipt_no')
            //    ->whereYear('paid_date',$year)
            ->where('tvet_other_fees.academic_year_id',$academic_year_id)
               ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
               DB::raw('DATE(tvet_other_fees.paid_date) AS paid_date')
               ,
            //    'tvet_other_fees.paid_date',
               'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount as amount ','fee_types.name as payment_type');


               if (request('search_query')) {
                $tvet_students_other_fee->where('student_id', '=',request('search_query'));
                $tvet_students_tuition_fee->where('student_id', '=',request('search_query'));
                $degree_students_tuition_fee->where('student_id', '=',request('search_query'));
                $degree_students_other_fee->where('student_id', '=',request('search_query'));
            }
            if (request('payment_type_query')) {
                $tvet_students_other_fee->where('fee_types.name', '=',request('payment_type_query'));
                $tvet_students_tuition_fee->where('fee_types.name', '=',request('payment_type_query'));
                $degree_students_tuition_fee->where('fee_types.name', '=',request('payment_type_query'));
                $degree_students_other_fee->where('fee_types.name', '=',request('payment_type_query'));
              }

            if (request('date_query')) {
                $tvet_students_other_fee->where('paid_date', '=',request('date_query'));
                $tvet_students_tuition_fee->where('paid_date', '=',request('date_query'));
                $degree_students_tuition_fee->where('paid_date', '=',request('date_query'));
                $degree_students_other_fee->where('paid_date', '=',request('date_query'));

            }
            if (request('date_between_query')) {

                    [$start,$end]=explode(',',request('date_between_query'));
               // return $query->whereBetween('paid_date',[$start,$end]);

                $tvet_students_other_fee->whereBetween('paid_date',[$start,$end]);
                $tvet_students_tuition_fee->whereBetween('paid_date',[$start,$end]);
                $degree_students_tuition_fee->whereBetween('paid_date',[$start,$end]);
                $degree_students_other_fee->whereBetween('paid_date',[$start,$end]);
            }

        $students_all_fee=    $tvet_students_other_fee
                              ->unionAll($tvet_students_tuition_fee)
                              ->unionAll($degree_students_tuition_fee)
                              ->unionAll($degree_students_other_fee)
                            //   ->toSql()
                              ;

                              $querySql = $students_all_fee->toSql();
                              $query1 = DB::table(DB::raw('('.$querySql.' ) as a  '))
                                  ->mergeBindings($students_all_fee);
        $all_fee= $students_all_fee->orderByDesc('paid_date')->paginate($per_page);
        return response()->json(
                                $all_fee
                            ,200);

//             $querySql = $students_all_fee->toSql();
//             if (request('search_query')) {
//                 $query1 = DB::table(DB::raw('('.$querySql.' ) as a  where receipt_no = '.request('search_query').''))
//                 ->mergeBindings($students_all_fee);

//             }else {
//                 $query1 = DB::table(DB::raw('('.$querySql.' ) as a  '))
//                 ->mergeBindings($students_all_fee);
//             }

//             $all_fee=$query1->paginate(10);
//             $query2=clone $query1;



//             // $all_fee= $query1->orderByDesc('paid_date')
//             // ->when(request('search_query') ,function($query){
//             //     return $query->where('student_id', '=',request('search_query'));
//             // })
//             // ->when(request('payment_type_query') ,function($query){
//             //     return $query->where('payment_type', '=',request('payment_type_query'));
//             // })
//             // ->when(request('date_query') ,function($query){
//             //     return $query->where('paid_date', '=',request('date_query'));
//             // })
//             // ->when(request('date_between_query') ,function($query){
//             //     [$start,$end]=explode(',',request('date_between_query'));
//             //     return $query->whereBetween('paid_date',[$start,$end]);
//             // })
//             // ->paginate($per_page);
//             // // ->get();

//             if (request()->has('dashbored_sum')) {


//         //    return     Carbon::createFromFormat('Y-d-m',(new Carbon)->now()->endOfDay()->toDateString());
//             //  return  (new Carbon)->now()->endOfDay()->toDateString();
//             // return (new Carbon)->subDays(31)->startOfDay();
// //   return              $date = Carbon::now()->toDateString();
// //                 return $date->toArray();
// //                 return $mytime = Carbon::now()->format('d-m-Y');
// // //  return $mytime->toDateTimeString();
// // return now(DateTimeZone::AFRICA);


// $m_q=clone $query2;
// $day7_q=clone $query2;
// $day1_q=clone $query2;
// // $hour_sum=  $day1_q->where('paid_date','>',now()->subDays(1))->get()->sum('paid_amount') ;

//                 // $month_sum=  $m_q
//                 //             ->where('paid_date','>=',now()->subDays(31))
//                 //             ->where('paid_date','<',now())
//                 //             ->get()->sum('paid_amount') ;
//                  $month_sum=  $m_q
//                 //  ->where('paid_date','>',date('Y-m-d') )->get();

//                           ->whereDate('paid_date','>=', (new Carbon)->subDays(30)->startOfDay()->toDateString(),'and')
//                                     //  ->whereDate('paid_date','<', strval(2022-01-07))
//                                     // ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
//                                     //  ->get();
//                                      ->sum('paid_amount');

//                  $day7_sum=  $m_q->whereDate('paid_date','>=',(new Carbon)->subDays(7)->startOfDay()->toDateString())
//                                     //    ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
//                                        ->sum('paid_amount');

//                                // $day7_sum=  $day7_q->where(DB::raw('paid_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()'))->sum('paid_amount');
//                 $hour_sum=   $m_q->whereDate('paid_date','>=',(new Carbon)->subDays(1)->startOfDay()->toDateString())
//                             // ->whereDate('paid_date','<=',(new Carbon)->now()->endOfDay()->toDateString())
//                 ->sum('paid_amount');

//               // $lastTenDaysRecord = ModelName::whereDateBetween('created_at',
//               //(new Carbon)->subDays(10)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString() )->get();

//             //   $day1_q->whereDate('paid_date','>=',$fromDate)->whereDate($fieldName,'<=',$todate);

//               // return true;
//                 return response()->json([
//                    '24hour'=>$hour_sum,
//                      '7day'=>$day7_sum,
//                      'month'=>$month_sum,
//                      'all_fee'=>$all_fee
//                  ],200);
//             }else {
//                 return response()->json(
//                     $all_fee
//                 ,200);
//             }



                }

        public function getStudentPaymentDetail($student_id){

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

            $academic_year=AcademicYear::find($academic_year_id);
            $student=[];
            $mf_id=FeeType::where('name','Monthly Fee')->first()->id;
            $cpf_id=FeeType::where('name','CP Fee')->first()->id;
            $month_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$mf_id)->first()->pivot->amount;
            $cp_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$cpf_id)->first()->pivot->amount;



        if (request('type') == 'degree') {
            $degreeStudent=DegreeStudent::where('student_id',$student_id)
                                        ->where('is_graduated',0)->where('fully_scholarship',0)
                                         ->with('semesters','month_payments')
                                         ->first();
            if ($degreeStudent) {


                 $student['id']=$degreeStudent->id;
                 $student['student_id']=$degreeStudent->student_id;
                 $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $student['department']=$degreeStudent->degree_department->name;
                 $student['program']=$degreeStudent->program->name;
                 $student['year_no']=$degreeStudent->current_year_no;
                 $student['month_payment']=$month_fee;
                 $student['cp_payment']=$cp_fee;
                 $student['type']=request('type');

              $semesters=$degreeStudent->semesters
                         ->where('academic_year_id',$academic_year->id);

                        // return $semesters;
              foreach ($semesters as $s) {

                 $total_cp=$degreeStudent->courses()->wherePivot('semester_id',$s->id)->sum('cp');

                 $monthly_cp_fee=($total_cp*$cp_fee)/$s->months->count();

                if ($s->months->isEmpty() == false) {

                //   if ($s->pivot->tuition_type == 'monthly' || $s->pivot->tuition_type == null ) {
                //   $total_cp= $this->getTotalCp($degreeStudent,$s);
                    $semester['id']=$s->id;
                    $semester['semester_no']=$s->number;
                    $semester['tution_type']=$s->pivot->tuition_type;
                    $semester['monthly_cp_fee']=$monthly_cp_fee;

                    $total=0;
                    $total_pad=[];
                    $months= $s->months;

                foreach ($months as $month) {
                     $month_payments=$degreeStudent->month_payments()
                     ->wherePivot('academic_year_id',$academic_year->id)->get();
                    foreach ($month_payments as $month_payment) {
                        $month_pad=[];
                        if ($month->id == $month_payment->id) {
                            $month_pad['id']=$month_payment->id;
                            $month_pad['name']=$month_payment->name;
                            $month_pad['pad']=$month_payment->pivot->receipt_no;
                            $month_pad['paid_date']= $month_payment->pivot->paid_date;
                            $total+= doubleval($month_payment->pivot->paid_amount);
                            $total_pad[]=$month_pad;
                        }

                    }
                }
                $semester['total']=$total;
                $semester['months']=$total_pad;
                $student['semesters'][]=$semester;
                //   }
                //   else if ($s->pivot->tuition_type == 'cp' ) {
                //             $semester=[];
                //         $semester['id']=$s->id;
                //         $semester['semester_no']=$s->number;
                //         $semester['tution_type']=$s->pivot->tuition_type;
                //         $total=0;
                //         $month_pad=null;
                //     //    return $s->months;
                //  //   return $degreeStudent->month_payments ->where('pivot.academic_year_id',$academic_year->id);
                //      foreach ($s->months as $month) {
                //          $month_payments=$degreeStudent->month_payments()
                //          ->wherePivot('academic_year_id',$academic_year->id)->get();
                //         foreach ($month_payments as $month_payment) {
                //             if ($month->id == $month_payment->id) {

                //                 $month_pad=$month_payment->pivot->receipt_no;
                //               //  $month_pad= $month_payment->pivot->paid_date;
                //                 $total+= ($month_payment->pivot->paid_amount);
                //                // $total_pad[]=$month_pad;
                //             }

                //         }
                //     }
                //     $semester['total']=$total;
                //     $semester['pad']=$month_pad;
                //     $student['cp'][]=$semester;
                //   }

                }
              }

          return $student;
           }else {
               return response()->json('student not found');
           }
        }else if (request('type') == 'tvet') {

            $tvetStudent=TvetStudent::where('student_id',$student_id)
            ->where('is_graduated',0)->where('fully_scholarship',0)
            ->first();
            if ($tvetStudent) {
                $student['id']=$tvetStudent->id;
                $student['student_id']=$tvetStudent->student_id;
                $student['full_name']=$tvetStudent->full_name;
                $student['sex']=$tvetStudent->sex;
                $student['department']=$tvetStudent->tvet_department->name;
                $student['program']=$tvetStudent->program->name;
                $student['level_no']=$tvetStudent->current_level_no;
                $student['month_payment']=$month_fee;
                $student['type']=request('type');

                 $total=0;
                 $total_pad=[];
                $month_payments=$tvetStudent->month_payments()
                ->wherePivot('academic_year_id',$academic_year->id)->get();
                foreach($month_payments as $month_payment){
                   $month_pad=[];
               //     $pads[$month_payment->name]=$month_payment->pivot->receipt_no;


                    $month_pad['id']=$month_payment->id;
                    $month_pad['name']=$month_payment->name;
                    $month_pad['pad']=$month_payment->pivot->receipt_no;
                    $total+= doubleval($month_payment->pivot->paid_amount);
                    $total_pad[]=$month_pad;

                }

                $student['total']=$total;
                $student['months']=$total_pad;
            }


            return response()->json($student,200);

        }else {
            return response()->json('Not Found',400);
        }



    }



    private function getTotalCp($student,$semester){
        $courses=Course::where('degree_department_id',$student->degree_department_id)
        ->where('program_id',$student->program_id)
        ->where('year_no',$semester->pivot->year_no)
        ->where('semester_no',$semester->pivot->semester_no)
        ->get();

    return $courses->sum('cp');
    }


    public function addTuitionPayment(Request $request,$student_id){


        DB::beginTransaction();
        try {
            //code...

            $cp_fee_id= FeeType::where('name','CP Fee')->first()->id;
            $monthly_fee_id= FeeType::where('name','Monthly Fee')->first()->id;

            if (request('student_type') == 'degree') {

            $student=DegreeStudent::find($student_id);
            if (! $student) {
                return response()->json(['error'=>'Student Not Found'],201);

            }
              $semester=Semester::find($request->semester_id);
              $month_amount=(double)$request->amount /count($request->months);

              $tvt= DB::table('tvet_student_month')->where('receipt_no',$request->receipt_no)->first();
              $degree=  DB::table('degree_student_month')->where('receipt_no',$request->receipt_no)->first();
              $to=  DB::table('tvet_other_fees')->where('receipt_no',$request->receipt_no)->first();
              $do=  DB::table('degree_other_fees')->where('receipt_no',$request->receipt_no)->first();
              //  $st= $student->month_payments()->wherePivot('receipt_no',$request->receipt_no)->first();

              if ($tvt || $degree || $to || $do) {
                   return response()->json(['error'=>'Pad Number Already Exist'],201);
                }else{
                foreach ($request->months as $id) {
                    $student->month_payments()
                   ->wherePivot('academic_year_id',$request->academic_year_id)
                    ->updateExistingPivot($id,[
                        'fee_type_id'=>$monthly_fee_id,
                        // 'academic_year_id'=>$request->academic_year_id,
                        'receipt_no'=>$request->receipt_no,
                        'paid_amount'=>$month_amount,
                        'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                        'is_paid'=>1

                    ]);
                }


          $m_ids=$semester->months->pluck('id');
          $un_paid=  $student->month_payments()->wherePivot('academic_year_id',$request->academic_year_id)
                                 ->wherePivot('receipt_no',null)
                                  ->whereIn('months.id',$m_ids)
                                 ->count();


                if ($un_paid == 0) {
                    $student->semesters()->updateExistingPivot($semester->id,
                    [
                        'legible'=>1,

                    ]);
                }else {
                    $student->semesters()->updateExistingPivot($semester->id,
                    [
                        'legible'=>0,

                    ]);
                }
                $student->semesters()->updateExistingPivot($semester->id,
                [
                    'tuition_type'=>$request->tuition_type,

                ]);
                DB::commit();
                return response()->json('Successfully Added',200);
         }


        }elseif (request('student_type') == 'tvet') {

            $student=TvetStudent::find($student_id);
            if (! $student) {
                return response()->json(['error'=>'Student Not Found'],201);

            }
            $month_amount=(double)$request->amount /count($request->months);

            $tvt= DB::table('tvet_student_month')->where('receipt_no',$request->receipt_no)->first();
            $degree=  DB::table('degree_student_month')->where('receipt_no',$request->receipt_no)->first();
            $to=  DB::table('tvet_other_fees')->where('receipt_no',$request->receipt_no)->first();
            $do=  DB::table('degree_other_fees')->where('receipt_no',$request->receipt_no)->first();
            //  $st= $student->month_payments()->wherePivot('receipt_no',$request->receipt_no)->first();
              if ($tvt || $degree || $to || $do) {
               return response()->json(['error'=>'Pad Number Already Exist'],201);
            }else{
            foreach ($request->months as $id) {
                $student->month_payments()
                  ->wherePivot('academic_year_id',$request->academic_year_id)
                  ->updateExistingPivot($id,[
                    'fee_type_id'=>$monthly_fee_id,
                    // 'academic_year_id'=>$request->academic_year_id,
                    'receipt_no'=>$request->receipt_no,
                    'paid_amount'=>$month_amount,
                    'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                    'is_paid'=>1

                ]);
            }

            DB::commit();
            return response()->json('Successfully Added',200);
         }
        }


     } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json($th,501);

    }
     }

     public function addOtherPayment(Request $request){


        try {

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

            $academic_year=AcademicYear::find($academic_year_id);

            $reg_fee_id= FeeType::where('name','Registration Fee')->first()->id;
            $tuition_fee_id= FeeType::where('name','CP Fee')->first()->id;
            $monthly_fee_id= FeeType::where('name','Monthly Fee')->first()->id;

            $reg_fee_id= FeeType::where('name','Registration Fee')->first()->id;

            if (request('type') == 'degree') {
                // return $request->paid_date;
                // return date('Y-m-d',strtotime($request->paid_date));
                $student=DegreeStudent::where('student_id',request('student_id'))->first();

                if (! $student) {
                    return response()->json(['error'=>'Student Not Found'],201);

                }
                $tvt= DB::table('tvet_student_month')->where('receipt_no',$request->receipt_no)->first();
                $degree=  DB::table('degree_student_month')->where('receipt_no',$request->receipt_no)->first();
                $to=  DB::table('tvet_other_fees')->where('receipt_no',$request->receipt_no)->first();
                $do=  DB::table('degree_other_fees')->where('receipt_no',$request->receipt_no)->first();
                //  $st= $student->month_payments()->wherePivot('receipt_no',$request->receipt_no)->first();
                  if ($tvt || $degree || $to || $do) {
                   return response()->json(['error'=>'Pad Number Already Exist'],201);
                }else{
                    $student->degree_other_fees()->attach($request->fee_type_id,[
                        'academic_year_id'=>$academic_year->id,
                        'receipt_no'=>$request->receipt_no,
                        'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                        'paid_amount'=>$request->amount,
                        'is_paid'=>1

                    ]);

                    return response()->json('Successfully Added',200);
                }

            } else if (request('type') == 'tvet') {
                $student=TvetStudent::where('student_id',request('student_id'))->first();

                if (! $student) {
                    return response()->json(['error'=>'Student Not Found'],201);

                }
                $tvt= DB::table('tvet_student_month')->where('receipt_no',$request->receipt_no)->first();
                $degree=  DB::table('degree_student_month')->where('receipt_no',$request->receipt_no)->first();
                $to=  DB::table('tvet_other_fees')->where('receipt_no',$request->receipt_no)->first();
                $do=  DB::table('degree_other_fees')->where('receipt_no',$request->receipt_no)->first();
                //  $st= $student->month_payments()->wherePivot('receipt_no',$request->receipt_no)->first();
                  if ($tvt || $degree || $to || $do) {
                   return response()->json(['error'=>'Pad Number Already Exist'],201);
                }else{
                    $student->tvet_other_fees()->attach($request->fee_type_id,[
                        'academic_year_id'=>$academic_year->id,
                        'receipt_no'=>$request->receipt_no,
                        'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                        'paid_amount'=>$request->amount,
                        'is_paid'=>1

                    ]);

                    return response()->json('Successfully Added',200);
                }


            }


        } catch (\Throwable $th) {
            return response()->json('not Addeed'. $th->getMessage(),500);
        }




 }

       public function getDashbordData(){

        // DegreeStudent::withCount()
       }


}
