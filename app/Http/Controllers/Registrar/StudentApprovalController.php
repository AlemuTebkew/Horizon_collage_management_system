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

       $student->semesters()->updateExistingPivot(request('semester_id'),['status'=>'approved']);
       // $new= Employee::find(request('user_id'))->unReadNotifications()->where('notifications.data.student_id',request('student_id'))->delete();
         DB::table('notifications')->whereJsonContains('data',['student_id'=>request('student_id')])
                                  ->whereJsonContains('data',['semester_id'=>request('semester_id')])->delete();
        return response()->json(['status'=>'approved'],200);
    }

    public function approveTvetStudent(){

        $student=TvetStudent::find(request('student_id'));

        $student->levels()->updateExistingPivot(request('level_id'),['status'=>'approved']);
        DB::table('notifications')->whereJsonContains('data',['student_id'=>request('student_id')])
                                   ->whereJsonContains('data',['level_id'=>request('level_id')])->delete();

        return response()->json(['status'=>'approved'],200);

    }
}
