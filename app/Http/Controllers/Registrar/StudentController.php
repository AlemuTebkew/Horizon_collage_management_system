<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use App\Models\Level;
use App\Models\Semester;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getDegreeGraduated(){

        //$academic_year_id=null;
     //    return request()->academic_year_id;
     if (request('academic_year_id')) {
        $academic_year_id=request('academic_year_id');
    }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
    }

    $academic_year=AcademicYear::find($academic_year_id);

    $stud=DegreeStudent::with(['program','degree_department'])
                        ->select('student_id','first_name','last_name','program_id','degree_department_id')
                        ->where('is_graduated',1)
                        ->whereYear('graduated_date',$academic_year->year)
                        ->paginate();



return response()->json($stud,200);
}

public function getTvetGraduated(){

    if (request('academic_year_id')) {
        $academic_year_id=request('academic_year_id');
    }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
    }

    $academic_year=AcademicYear::find($academic_year_id);

    $stud=TvetStudent::with(['program','tvet_department'])
                        ->select('id','student_id','first_name','last_name','program_id','tvet_department_id')
                        ->where('is_graduated',1)
                        ->whereYear('graduated_date',$academic_year->year)
                        ->paginate();



return response()->json($stud,200);
 }
 }

