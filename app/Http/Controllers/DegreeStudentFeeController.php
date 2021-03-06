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

        $paginated_data=[];
        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }
        $per_page=request()->has('per_page') ? request('per_page') : 10;

        $academic_year=AcademicYear::find($academic_year_id);

       $degreeStudents=DegreeStudent::whereHas('month_payments',function( $query) use($academic_year_id){
            $query->where('academic_year_id',$academic_year_id)

            ->when(request('paid'),function($query){
                $query->whereNotNull('receipt_no')
                     ->where('month_id',request('paid'));
              }) 
              ->when(request('unpaid'),function($query){
                $query->where('receipt_no',null)
                      ->where('month_id',request('unpaid'));
              });

       })->where('is_graduated',0)->where('fully_scholarship',0)
    ->when(request('search_id'),function($query){

        $query->where('student_id','LIKE','%'.request('search_id').'%')
            ->orWhere('first_name','LIKE','%'.request('search_id').'%')
            ->orWhere('middle_name','LIKE','%'.request('search_id').'%')
            ->orWhere('last_name','LIKE','%'.request('search_id').'%');
     
        })

       ->with(['month_payments'=>function( $query) use($academic_year_id){
        $query->where('degree_student_month.academic_year_id',$academic_year_id);
 
      }])
    //   ->orderBy('degree_student_month.paid_date')

      ->paginate($per_page);

        $a= $degreeStudents->toArray();

        $paginated_data['current_page']= $a['current_page'];
        $paginated_data['to']= $a['to'];
        $paginated_data['from']= $a['from'];
        $paginated_data['total']= $a['total'];

          foreach ($degreeStudents as  $degreeStudent) {

                $student['id']=$degreeStudent->id;
                $student['student_id']=$degreeStudent->student_id;
                $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $month_pad=[];
                 $total=0.0;
                  $month_payments=$degreeStudent->month_payments()
                  ->wherePivot('academic_year_id',$academic_year_id)
                 // ->wherePivot('receipt_no',null)
                  ->orderBy('number')
                  ->get();
                //   foreach ($academic_year->months as $month) {

                      $month_pad=[];
                    foreach ($month_payments as $month_payment) {
                  //    return  $month;
                        if($month_payment->pivot->academic_year_id == $academic_year_id){

                            // if ($month->id == $month_payment->id)  {
                                $pads[$month_payment->name]= $month_payment->pivot->receipt_no;
                                $total+=(double)$month_payment->pivot->paid_amount;
                            //    break;
                            //  }
                             //else {
                            //     $month_pad =null;
                            //     break;
                            // }
                         }
                        //  $pads[$month_payment->name]=$month_pad;
                        }
            // }
            $student['total']=$total;
            $student['pads']=$pads;
            $all[]=$student;
          }
        //   $chunks = collect($all)->chunk(2);
        //  return $chunks->toArray();
        $paginated_data['data']=$all;
         return response()->json($paginated_data ,200);
    }

    public function filterPaidStudentsByMonth()
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
        $per_page=request()->has('per_page') ? request('per_page') : 10;

        $academic_year=AcademicYear::find($academic_year_id);


            $degreeStudents=DegreeStudent:: whereHas('month_payments',function($query) use($academic_year_id){
                 $query->where('degree_student_month.academic_year_id',$academic_year_id)
                ->whereNotNull('degree_student_month.receipt_no')
                ->where('degree_student_month.month_id',request('month_query'));
      })->with(['month_payments'=>function( $query) use($academic_year_id){
        $query->where('degree_student_month.academic_year_id',$academic_year_id);
    }])->where('is_graduated',0)->where('fully_scholarship',0)
      ->paginate($per_page);

      $a= $degreeStudents->toArray();

      $paginated_data['current_page']= $a['current_page'];
      $paginated_data['to']= $a['to'];
      $paginated_data['from']= $a['from'];
      $paginated_data['total']= $a['total'];


          foreach ($degreeStudents as  $degreeStudent) {

                $student['id']=$degreeStudent->id;
                $student['student_id']=$degreeStudent->student_id;
                $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $month_pad=[];
                 $total=0.0;
                  $month_payments=$degreeStudent->month_payments()
                  ->wherePivot('academic_year_id',$academic_year_id)
                 // ->wherePivot('receipt_no',null)
                  ->orderBy('number')
                  ->get();
                //   foreach ($academic_year->months as $month) {

                      $month_pad=[];
                    foreach ($month_payments as $month_payment) {
                  //    return  $month;
                        if($month_payment->pivot->academic_year_id == $academic_year_id){

                            // if ($month->id == $month_payment->id)  {
                                $pads[$month_payment->name]= $month_payment->pivot->receipt_no;
                                $total+=(double)$month_payment->pivot->paid_amount;
                            //    break;
                            //  }
                             //else {
                            //     $month_pad =null;
                            //     break;
                            // }


                         }

                        //  $pads[$month_payment->name]=$month_pad;

                        }



            // }
            $student['total']=$total;
            $student['pads']=$pads;
            $all[]=$student;
          }
          $paginated_data['data']=$all;
          return response()->json($paginated_data ,200);
    }

    public function filterUnPaidStudentsByMonth()
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
        $per_page=request()->has('per_page') ? request('per_page') : 4;

        $academic_year=AcademicYear::find($academic_year_id);


            $degreeStudents=DegreeStudent:: whereHas('month_payments',function($query) use($academic_year_id){
                 $query->where('degree_student_month.academic_year_id',$academic_year_id)
                ->where('degree_student_month.receipt_no',null)
                ->where('degree_student_month.month_id',request('month_query'));

      })->with(['month_payments'=>function( $query) use($academic_year_id){
        $query->where('degree_student_month.academic_year_id',$academic_year_id);
    }])->where('is_graduated',0)->where('fully_scholarship',0)
      ->paginate($per_page);


      $a=  $degreeStudents->toArray();

      $paginated_data['current_page']= $a['current_page'];
      $paginated_data['to']= $a['to'];
      $paginated_data['from']= $a['from'];
      $paginated_data['total']= $a['total'];



          foreach ($degreeStudents as  $degreeStudent) {

                $student['id']=$degreeStudent->id;
                $student['student_id']=$degreeStudent->student_id;
                $student['full_name']=$degreeStudent->full_name;
                 $student['sex']=$degreeStudent->sex;
                 $month_pad=[];
                 $total=0.0;
                  $month_payments=$degreeStudent->month_payments()
                  ->wherePivot('academic_year_id',$academic_year_id)
                 // ->wherePivot('receipt_no',null)
                  ->orderBy('number')
                  ->get();
                //   foreach ($academic_year->months as $month) {

                      $month_pad=[];
                    foreach ($month_payments as $month_payment) {
                  //    return  $month;
                        if($month_payment->pivot->academic_year_id == $academic_year_id){

                            // if ($month->id == $month_payment->id)  {
                                $pads[$month_payment->name]= $month_payment->pivot->receipt_no;
                                $total+=(double)$month_payment->pivot->paid_amount;
                            //    break;
                            //  }
                             //else {
                            //     $month_pad =null;
                            //     break;
                            // }


                         }

                        //  $pads[$month_payment->name]=$month_pad;

                        }



            // }
            $student['total']=$total;
            $student['pads']=$pads;
            $all[]=$student;
          }
          $paginated_data['data']=$all;
          return response()->json($paginated_data ,200);
    }
    public function show($id)
    {
        $degreeStudent=DegreeStudent::find($id);

        $student=[];
        $years=[];
        if ($degreeStudent) {
            $semesters=$degreeStudent->semesters;
            if ($semesters->isEmpty() == false) {
                foreach($semesters as $semester){
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
            $student['sex']=$degreeStudent->sex;
            $student['program']=$degreeStudent->program->name;
            $student['year_no']=$degreeStudent->current_year_no;

            $year=[];
            foreach ($years as $y) {

                $year['year']=$y->year;
                $year['semesters']=[];
                $semester=[];
                $semesters=$degreeStudent->semesters()
                ->where('semesters.academic_year_id',$y->id)->get();
                // return $semesters->load('months');
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
                                }
                                // else {
                                //     $month_pad['id']=$month->id;
                                //     $month_pad['name']=$month->name;
                                //     $month_pad['pad']=null;
                                //      $month_pad['paid_date']=null;
                                //      $total_pads[]= $month_pad;
                                //    break;
                                // }

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
