<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class HeadDashboardController extends Controller
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

        $employee=Employee::where('email',request()->user()->user_name)->first();

        $degree_department=DegreeDepartment::find($employee->manage->id);
        $tvet_department=TvetDepartment::find($employee->managet->id);

        if ($degree_department) {

            $dashboard_data['total_degree_student']=DegreeStudent::whereHas('semesters',function($query) use($academic_year_id){
                $query->where('degree_student_semester.academic_year_id',$academic_year_id);
               //  ->where('academic_year_id',$academic_year_id);
           })->where('is_graduated',0)
             ->where('degree_department_id',$degree_department->id)
           ->count();

           $dashboard_data['new_degree_student']=DegreeStudent::whereHas('semesters',function($query) use($academic_year_id){
            $query->where('degree_student_semester.academic_year_id',$academic_year_id);

           })->where('is_graduated',0)
                  -> where('batch',$current_academic_year->year)
                  ->where('degree_department_id',$degree_department->id)

                  ->count();

            }else if ($tvet_department) {

                $dashboard_data['total_tvet_student']=TvetStudent::whereHas('levels',function($query) use($academic_year_id){
                    $query->where('academic_year_id',$academic_year_id);
                   //  ->where('academic_year_id',$academic_year_id);
               })->where('is_graduated',0)
               ->where('tvet_department_id',$tvet_department->id)

               ->count();

              $dashboard_data['new_tvet_student']=TvetStudent::whereHas('levels',function($query) use($academic_year_id){
               $query->where('tvet_student_level.academic_year_id',$academic_year_id);
              //  ->where('academic_year_id',$academic_year_id);
              })->where('is_graduated',0)
              ->where('tvet_department_id',$tvet_department->id)
              ->where('batch',$current_academic_year->year)->count();

            }

    return response()->json($dashboard_data,200);
    }
}
