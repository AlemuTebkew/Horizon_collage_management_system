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
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentFeeController extends Controller
{
    public function studentsPaid(){


        $per_page=request()->has('per_page') ? request('per_page') : 5;
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

               $tvet_students_other_fee=DB::table('tvet_students')
               ->join('tvet_other_fees','tvet_students.id','=','tvet_other_fees.tvet_student_id')
               ->join('fee_types','fee_types.id','=','tvet_other_fees.fee_type_id')
               ->whereNotNull('receipt_no')
               ->select('tvet_students.id','student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),'tvet_other_fees.paid_date',
               'tvet_other_fees.receipt_no','tvet_other_fees.paid_amount','fee_types.name as payment_type');


        $students_all_fee=    $tvet_students_other_fee
                              ->unionAll($tvet_students_tuition_fee)
                              ->unionAll($degree_students_tuition_fee)
                              ->unionAll($degree_students_other_fee);

                            $s= request('search_query');

            $querySql = $students_all_fee->toSql();
            $query = DB::table(DB::raw("($querySql ) as a"))
            ->mergeBindings($students_all_fee);

            return $query->orderByDesc('paid_date')
                        ->when(request('search_query') ,function($query){
                            return $query->where('student_id', '=',request('search_query'));
                        })
                        ->when(request('payment_type_query') ,function($query){
                            return $query->where('payment_type', '=',request('payment_type_query'));
                        })
                        ->when(request('date_query') ,function($query){
                            return $query->where('paid_date', '=',request('date_query'));
                        })
                        ->when(request('date_between_query') ,function($query){
                            [$start,$end]=explode(',',request('date_between_query'));
                            return $query->whereBetween('paid_date',[$start,$end]);
                        })
                        ->paginate($per_page);
                        // ->get();

                }

        public function getStudentPaymentDetail($student_id){
            $academic_year=AcademicYear::find(request('academic_year_id'));
            $student=[];
            $mf_id=FeeType::where('name','Monthly Fee')->first()->id;
            $cpf_id=FeeType::where('name','CP Fee')->first()->id;
            $month_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$mf_id)->first()->pivot->amount;
            $cp_fee=$academic_year->fee_types()->wherePivot('fee_type_id',$cpf_id)->first()->pivot->amount;



        if (request('type') == 'degree') {
            $degreeStudent=DegreeStudent::where('student_id',$student_id)
                                         ->with('semesters','month_payments')
                                         ->first();
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

                        // return $semesters;
              foreach ($semesters as $s) {


                if ($s->has('months')) {


                  if ($s->pivot->tuition_type == 'monthly' || $s->pivot->tuition_type == null ) {
                  $total_cp= $this->getTotalCp($degreeStudent,$s);
                    $semester['id']=$s->id;
                    $semester['semester_no']=$s->number;
                    $semester['tution_type']=$s->pivot->tuition_type;
                    $semester['semester_payment']=$cp_fee * $total_cp;

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
                         $month_payments=$degreeStudent->month_payments()
                         ->wherePivot('academic_year_id',$academic_year->id)->get();
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
              }

          return $student;
           }else {
               return response()->json('student not found');
           }
        }else if (request('type') == 'tvet') {

            $tvetStudent=TvetStudent::where('student_id',$student_id)->first();
            if ($tvetStudent) {
                $student['id']=$tvetStudent->id;
                $student['student_id']=$tvetStudent->student_id;
                $student['full_name']=$tvetStudent->full_name;
                $student['department']=$tvetStudent->tvet_department->name;
                $student['program']=$tvetStudent->program->name;
                $student['level_no']=$tvetStudent->current_level_no;
                $student['month_payment']=$month_fee;
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


            return $student;
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


            $cp_fee_id= FeeType::where('name','CP Fee')->first()->id;
            $monthly_fee_id= FeeType::where('name','Monthly Fee')->first()->id;

            if (request('student_type') == 'degree') {

            $student=DegreeStudent::find($student_id);
            $semester=Semester::find($request->semester_id);
            $student->semesters()->updateExistingPivot($semester->id,
            [
                'tuition_type'=>$request->tuition_type,

            ]);
            if ($request->tuition_type == 'cp') {

                 $month_amount=doubleval($request->amount)/count($semester->months);
                foreach ($semester->months as $month) {

                    $student->month_payments()->updateExistingPivot($month->id,[
                        'fee_type_id'=>$cp_fee_id,
                        'academic_year_id'=>$request->academic_year_id,
                        'receipt_no'=>$request->receipt_no,
                        'paid_amount'=>$month_amount,
                        'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                        'is_paid'=>1

                    ]);
                }

            }elseif ($request->tuition_type == 'monthly') {
                $month_amount=(double)$request->amount /count($request->months);

                foreach ($request->months as $id) {
                    $student->month_payments()->updateExistingPivot($id,[
                        'fee_type_id'=>$monthly_fee_id,
                        'academic_year_id'=>$request->academic_year_id,
                        'receipt_no'=>$request->receipt_no,
                        'paid_amount'=>$month_amount,
                        'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                        'is_paid'=>1

                    ]);
                }
            }

            return $student->load('month_payments');
        }elseif (request('student_type') == 'tvet') {

            $student=TvetStudent::find($student_id);

            $month_amount=(double)$request->amount /count($request->months);

            foreach ($request->months as $id) {
                $student->month_payments()->updateExistingPivot($id,[
                    'fee_type_id'=>$monthly_fee_id,
                    'academic_year_id'=>$request->academic_year_id,
                    'receipt_no'=>$request->receipt_no,
                    'paid_amount'=>$month_amount,
                    'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                    'is_paid'=>1

                ]);
            }
            return $student->load('month_payments');

        }
     }

     public function addOtherPayment(Request $request){

        $academic_year=AcademicYear::where('status',1)->first();

        $reg_fee_id= FeeType::where('name','Registration Fee')->first()->id;
        $tuition_fee_id= FeeType::where('name','CP Fee')->first()->id;
        $monthly_fee_id= FeeType::where('name','Monthly Fee')->first()->id;

        $reg_fee_id= FeeType::where('name','Registration Fee')->first()->id;

        if (request('type') == 'degree') {
            $student=DegreeStudent::where('student_id',request('student_id'))->first();
            $student->degree_other_fees()->attach($request->fee_type_id,[
                'academic_year_id'=>$academic_year->id,
                'receipt_no'=>$request->receipt_no,
                'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                'paid_amount'=>$request->amount,
                'is_paid'=>1

            ]);
        } else if (request('type') == 'tvet') {
            $student=TvetStudent::where('student_id',request('student_id'))->first();
            $student->tvet_other_fees()->attach($request->fee_type_id,[
                'academic_year_id'=>$academic_year->id,
                'receipt_no'=>$request->receipt_no,
                'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                'paid_amount'=>$request->amount,
                'is_paid'=>1

            ]);
        }





 }

        public function getAcademicFee(){
            $academic_year=null;
            if (request('academic_year_id')) {
                $academic_year=AcademicYear::find(request('academic_year_id'));

            }else {
                $academic_year=AcademicYear::where('is_current',1)->first();
            }

                $all=[];

               foreach ($academic_year->fee_types as $fee_type) {
                $fee=[];
                $fee['id']=$fee_type->id;
                $fee['name']=$fee_type->name;
                $fee['amount']=$fee_type->pivot->amount;
                $all[]=$fee;
            }
         return response()->json($all,200);
        }
}
