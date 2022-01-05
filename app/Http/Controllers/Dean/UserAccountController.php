<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function getStudents(){

        // $d_s=DegreeStudent::addSelect(['password'=>UserLogin::select('password') ]);

        $per_page=request()->has('per_page') ? request('per_page') : 5;

        $ds=DB::table('degree_students')
               ->join('degree_departments', 'degree_students.degree_department_id' ,'=','degree_departments.id')
               ->join('programs','degree_students.program_id' , '=' ,'programs.id')
               ->select('student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                  'programs.name AS program','degree_departments.name AS department');



            $ts=DB::table('tvet_students')
            ->join('tvet_departments', 'tvet_students.tvet_department_id' ,'=','tvet_departments.id')
            ->join('programs','tvet_students.program_id' , '=' ,'programs.id')
            ->select('student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
               'programs.name AS program','tvet_departments.name AS department' );
               $all_student=$ds->unionAll($ts);
               return   $all_student
                        ->when(request('search_query') ,function($query){
                   return $query->where('student_id', '=',request('search_query'));
               })->paginate($per_page);


         }


    public function getEmployees(){

        $per_page=request()->has('per_page') ? request('per_page') : 5;

       return Employee::select('first_name', 'last_name',
                  'phone_no','email','role')
                ->when(request('search_query') ,function($query){
                    return $query->where('email', '=',request('search_query'));
                }) ->paginate($per_page);
    }
}
