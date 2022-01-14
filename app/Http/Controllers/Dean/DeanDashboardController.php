<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Coc;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use App\Models\ExternalCocApplicant;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class DeanDashboardController extends Controller
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

        $dashboard_data['new_degree_student']=DegreeStudent::whereHas('semesters',function($query) use($current_academic_year){
            $query->where('degree_student_semester.academic_year_id',$current_academic_year->id)
            -> where('batch',$current_academic_year->year) ;
               })->where('is_graduated',0)
        ->count();



       $dashboard_data['new_tvet_student']=TvetStudent::whereHas('levels',function($query) use($academic_year_id){
        $query->where('tvet_student_level.academic_year_id',$academic_year_id);
       //  ->where('academic_year_id',$academic_year_id);
       })->where('is_graduated',0)->where('batch',$current_academic_year->year)->count();



       $dashboard_data['total_coc']=Coc::where('academic_year_id',$academic_year_id)->count();
      $coc_id=Coc::latest()->first()->id;
       $dcoc=DegreeStudent::whereHas('cocs',function($query) use($coc_id){
        $query->where('coc_id',$coc_id);
    }) ->count();

    $tcoc=TvetStudent::whereHas('cocs',function($query) use($coc_id){
        $query->where('coc_id',$coc_id);
    }) ->count();

    $excoc=ExternalCocApplicant::where('coc_id',$coc_id)->count();

    $dashboard_data['current_coc_students']=$dcoc+$tcoc+$excoc;

        return response()->json($dashboard_data);
}
}
