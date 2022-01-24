<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function getStudents(){

        // $d_s=DegreeStudent::addSelect(['password'=>UserLogin::select('password') ]);

        $per_page=request()->has('per_page') ? request('per_page') : 10;

        $ds=DB::table('degree_students')
               ->where('degree_students.is_graduated',0)
               ->where('degree_students.deleted_at',null)
               ->when(request('search_query') ,function($query){
                $query->where('first_name', 'LIKE','%'.request('search_query').'%')
                ->orWhere('last_name', 'LIKE','%'.request('search_query').'%')
                ->orWhere('student_id', 'LIKE','%'.request('search_query').'%') ;
            })
               ->join('degree_departments', 'degree_students.degree_department_id' ,'=','degree_departments.id')
               ->join('programs','degree_students.program_id' , '=' ,'programs.id')
               ->select('student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                  'programs.name AS program','degree_departments.name AS department');



            $ts=DB::table('tvet_students')
            ->where('tvet_students.is_graduated',0)
            ->where('tvet_students.deleted_at',null)
            ->when(request('search_query') ,function($query){
                $query->where('first_name', 'LIKE','%'.request('search_query').'%')
                ->orWhere('last_name', 'LIKE','%'.request('search_query').'%')
                ->orWhere('student_id', 'LIKE','%'.request('search_query').'%') ;
            })
            ->join('tvet_departments', 'tvet_students.tvet_department_id' ,'=','tvet_departments.id')
            ->join('programs','tvet_students.program_id' , '=' ,'programs.id')
            ->select('student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
               'programs.name AS program','tvet_departments.name AS department' );
               $all_student=$ds->unionAll($ts);
              // return request('search_query');
            //   where receipt_no = '.request('search_query').'
            // $querySql = $all_student->toSql();
            //   if (request('search_query')) {
            //     $query1 = DB::table(DB::raw('('.$querySql.' ) as a  where student_id = '.request('search_query').''))
            //     ->mergeBindings($all_student);

            //   }else {
            //     $query1 = DB::table(DB::raw('('.$querySql.' ) as a  '))
            //     ->mergeBindings($all_student);

            // }
                return $all_student
               ->paginate($per_page);


         }


    public function getEmployees(){

        $per_page=request()->has('per_page') ? request('per_page') : 10;

        $teacher=Teacher::select('first_name', 'last_name',
        'phone_no','email',DB::raw(" 'teacher' AS role"))
        ->when(request('search_query') ,function($query){
            $query->where('first_name', 'LIKE','%'.request('search_query').'%')
            ->orWhere('last_name', 'LIKE','%'.request('search_query').'%')
            ->orWhere('email', 'LIKE','%'.request('search_query').'%') ;
        })
        ->where('status',1);

       return Employee::select('first_name', 'last_name',
                  'phone_no','email','role')
                  ->where('status',1)
                  ->where('role','!=','admin')
                  ->where('role','!=','dean')
                ->when(request('search_query') ,function($query){
                    $query->where('first_name', 'LIKE','%'.request('search_query').'%')
                    ->orWhere('last_name', 'LIKE','%'.request('search_query').'%')
                    ->orWhere('email', 'LIKE','%'.request('search_query').'%') ;
                })->unionAll($teacher)
                ->paginate($per_page);
    }
}
