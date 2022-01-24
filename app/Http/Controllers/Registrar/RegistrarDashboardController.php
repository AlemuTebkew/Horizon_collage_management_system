<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Coc;
use App\Models\DegreeStudent;
use App\Models\ExternalCocApplicant;
use App\Models\Teacher;
use App\Models\TvetStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RegistrarDashboardController extends Controller
{
    public function getDashboardData(){

        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }
        $current_academic_year=AcademicYear::find($academic_year_id);

        $dashboard_data=[];
        $dashboard_data['total_tvet_student']=TvetStudent::whereHas('levels',function($query) use($academic_year_id){
             $query->where('academic_year_id',$academic_year_id);
            //  ->where('academic_year_id',$academic_year_id);
        })->where('is_graduated',0)
        ->count();

        $dashboard_data['total_degree_student']=DegreeStudent::whereHas('semesters',function($query) use($academic_year_id){
            $query->where('degree_student_semester.academic_year_id',$academic_year_id);
           //  ->where('academic_year_id',$academic_year_id);
       })->where('is_graduated',0)
       ->count();

        $dashboard_data['new_degree_student']=DegreeStudent::whereHas('semesters',function($query) use($academic_year_id){
            $query->where('degree_student_semester.academic_year_id',$academic_year_id);

           })->where('is_graduated',0)
                  -> where('batch',$current_academic_year->year)
                  ->count();


       $dashboard_data['new_tvet_student']=TvetStudent::whereHas('levels',function($query) use($academic_year_id){
        $query->where('tvet_student_level.academic_year_id',$academic_year_id);
       //  ->where('academic_year_id',$academic_year_id);
       })->where('is_graduated',0)->where('batch',$current_academic_year->year)->count();


        $dashboard_data['tvet_scholarship_students']=TvetStudent::select('student_id','first_name','last_name','tvet_department_id')->
                                 whereHas('levels',function($query) use($academic_year_id){
            $query->where('tvet_student_level.academic_year_id',$academic_year_id);
            //  ->where('academic_year_id',$academic_year_id);
            })->with('tvet_department')
            ->where('is_graduated',0)
              ->where('fully_scholarship',1)->take(7)->get();

        $dashboard_data['degree_scholarship_students']=DegreeStudent::select('student_id','first_name','last_name','degree_department_id')->
             whereHas('semesters',function($query) use($current_academic_year){
                   $query->where('degree_student_semester.academic_year_id',$current_academic_year->id);
            })->with('degree_department')
            ->where('is_graduated',0)
             ->where('fully_scholarship',1)->take(7)->get();




       $degreeStudents=DegreeStudent::select('student_id','first_name','last_name')->whereHas('month_payments',function($query) use($academic_year_id){
        $query->where('degree_student_month.academic_year_id',$academic_year_id)
        ->where('degree_student_month.receipt_no',null)
        ->where('months.name',Carbon::now()->monthName);

            })->take(7)->get();

            $tvetStudents=TvetStudent::select('student_id','first_name','last_name')->whereHas('month_payments',function( $query) use($academic_year_id){
                $query->where('tvet_student_month.academic_year_id',$academic_year_id)
                ->whereNull('tvet_student_month.receipt_no')
                ->where('months.name',Carbon::now()->monthName);

            })->take(7)->get();

            $dashboard_data['unpaid_degree_students']= $degreeStudents;
            $dashboard_data['unpaid_tvet_students']= $tvetStudents;
            $dashboard_data['active_month']= Carbon::now()->monthName;


            $tcoc=$dcoc=$excoc=0;
       $dashboard_data['total_coc']=Coc::where('academic_year_id',$academic_year_id)->count();
      $coc=Coc::latest()->first();
      if ($coc) {
        $dcoc=DegreeStudent::whereHas('cocs',function($query) use($coc){
            $query->where('coc_id',$coc->id);
        }) ->count();

        $tcoc=TvetStudent::whereHas('cocs',function($query) use($coc){
            $query->where('coc_id',$coc->id);
        }) ->count();

        $excoc=ExternalCocApplicant::where('coc_id',$coc->id)->count();

           $dashboard_data['current_coc_students']=$dcoc+$tcoc+$excoc;

          }



        return response()->json($dashboard_data);
}
}
