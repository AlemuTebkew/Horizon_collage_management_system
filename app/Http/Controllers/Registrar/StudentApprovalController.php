<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\TvetStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentApprovalController extends Controller
{
    public function approveDegreeStudent(){

        $student=DegreeStudent::find(request('student_id'));

        if ($student) {
            $student->semesters()->updateExistingPivot(request('semester_id'),['status'=>'approved']);
            // $new= Employee::find(request('user_id'))->unReadNotifications()->where('notifications.data.student_id',request('student_id'))->delete();
            $succ=  DB::table('notifications')->whereJsonContains('data',['student_id'=>request('student_id')],'and')
                                    ->whereJsonContains('data',['semester_id'=>request('semester_id')])->delete();
             if ($succ == 0) {
                return response()->json(['status'=>'error'],404);

             }
             return response()->json(['status'=>'approved'],200);

               }else {
            return response()->json(['status'=>'error'],404);
        }

    }

    public function approveTvetStudent(){

        $student=TvetStudent::find(request('student_id'));
        if ($student) {

        $student->levels()->updateExistingPivot(request('level_id'),['status'=>'approved']);
      $succ= DB::table('notifications')->whereJsonContains('data',['student_id'=>request('student_id')])
                                   ->whereJsonContains('data',['level_id'=>request('level_id')])->delete();
            if ($succ == 0) {
            return response()->json(['status'=>'error'],404);

            }
        return response()->json(['status'=>'approved'],200);
    }else {
        return response()->json(['status'=>'error'],404);
    }
    }

    public function makeDegreeStudentLegibleForResult($id){

        $student=DegreeStudent::find($id);
        $student->semesters()->updateExistingPivot(request('semester_id'),
        [
            'legible'=>1,

        ]);
    }

    public function makeTvetStudentLegibleForResult($id){

        $student=TvetStudent::find($id);
        $student->levels()->updateExistingPivot(request('level_id'),
        [
            'legible'=>1,

        ]);
    }
}
