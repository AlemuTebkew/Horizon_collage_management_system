<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentLevelResource;
use App\Http\Resources\StudentSemesterResource;
use App\Models\DegreeStudent;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class StudentCopyController extends Controller
{
    public function getStudentSemesters($degreeStudent_id){
        $degreeStudent= DegreeStudent::find($degreeStudent_id);
        return response()->json( new StudentSemesterResource($degreeStudent->load('semesters')),200);
    }

    public function getStudentLevels( $tvetStudent_id){
        $tvetStudent= TvetStudent::find($tvetStudent_id);
        return new StudentLevelResource($tvetStudent->load('levels'));
    }

}
