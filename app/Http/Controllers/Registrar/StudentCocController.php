<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DegreeStudent;
use App\Models\ExternalCocApplicant;
use App\Models\TvetStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentCocController extends Controller
{
    public function checkStudentForCoc($student_id){


        if (request('type') == 'degree') {

            $student=DegreeStudent::where('student_id',$student_id)->first();
            return response()->json(['id'=>$student->id],200);

        }else if (request('type') == 'tvet') {
            $student=TvetStudent::where('student_id',$student_id)->first();
            return response()->json(['id'=>$student->id],200);
        }else {
            return response()->json('Not Found',404);
        }
    }

    public function registerInternalStudentForCoc($student_id){

        if (request('type') == 'degree') {
            $student=DegreeStudent::where('student_id',$student_id)->first();
                if ($student || ! empty($student)) {
                    $check= $student->cocs()->wherePivot('coc_id',request('coc_id'))->first();

                    if (empty($check) || (! $check) ) {

                // return request()->all();
                    $student->cocs()->attach(request('coc_id'),
                    [
                        'occupation_name'=>request('occupation_name'),
                        'level_no'=>request('level_no'),
                        'nature_of_assesment'=>request('nature_of_assesment'),
                        'registration_no'=>request('registration_no'),
                        'application_date'=>now(),

                    ]);
                    return response()->json($student,201);
                }else{
                    return response()->json(['error'=>'Student Already Registerd'],200);

                }
            }else {
                return response()->json(['error'=>'Student Not Found'],404);
            }

        }elseif (request('type') == 'tvet') {

            $student=TvetStudent::where('student_id',$student_id)->first();


           if ($student || ! empty($student)) {
                $check= $student->cocs()->wherePivot('coc_id',request('coc_id'))->first();

                if (empty($check) || (! $check) ) {

                // return request()->all();
                    $student->cocs()->attach(request('coc_id'),
                    [
                        'occupation_name'=>request('occupation_name'),
                        'level_no'=>request('level_no'),
                        'nature_of_assesment'=>request('nature_of_assesment'),
                        'registration_no'=>request('registration_no'),
                        'application_date'=>now(),

                    ]);
                    return response()->json($student,201);
                }else{
                    return response()->json(['error'=>'Student Already Registerd'],200);

                }
        }else {
            return response()->json(['error'=>'Student Not Found'],200);
        }

       }else {
        return response()->json(['error'=>'Student Not Found'],200);
       }
    }

    public function registerExternalStudentForCoc(){

        request()->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'level_no'=>'required',
            'occupation_name'=>'required',


        ]);

        $exs=ExternalCocApplicant::where('phone_no',request('phone_no'))->first();
        if ($exs || (!empty($exs))) {
            return response()->json(['error'=>'Student Already Registerd'],200);

        }
         return response()->json(ExternalCocApplicant::create(request()->all()),201);
    }

    public function getAllStudents(){
        $per_page=request()->has('per_page') ? request('per_page') : 20;

        $external=DB::table('external_coc_applicants')
                     ->where('coc_id',request('coc_id'))
                      ->join('cocs', function($join){
                          $join->on('external_coc_applicants.coc_id','=' ,'cocs.id')
                          ->where('cocs.id',request('coc_id'));
                      })
                      ->select('external_coc_applicants.id as student_id',DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"),
                      'sex','occupation_name','level_no','result','certificate_no','registration_no');
        $external=$external->addSelect(DB::raw("'external' as type"));
        // $data = $data->addSelect();

        $degree=DB::table('degree_students')
                        ->join('coc_degree_student' , 'coc_degree_student.degree_student_id' ,'=' ,'degree_students.id')
                        ->where('coc_id',request('coc_id'))
                        // ->join('levels' , 'coc_degree_student.level_id' ,'=' ,'levels.id')
                        ->join('cocs', function($join){
                            $join->on('coc_degree_student.coc_id','=' ,'cocs.id')
                            ->where('cocs.id',request('coc_id'));
                        })
                        ->select('degree_students.student_id',DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
                        'sex','occupation_name','level_no','result','certificate_no','registration_no');

                        $degree=$degree->addSelect(DB::raw("'degree' as type"));

       $tvet=DB::table('tvet_students')
                        ->join('coc_tvet_student' , 'coc_tvet_student.tvet_student_id' ,'=' ,'tvet_students.id')
                        ->where('coc_id',request('coc_id'))
                        // ->join('levels' , 'coc_tvet_student.level_id' ,'=' ,'levels.id')
                        ->join('cocs', function($join){
                            $join->on('coc_tvet_student.coc_id','=' ,'cocs.id')
                            ->where('cocs.id',request('coc_id'));
                        })
                         ->select('tvet_students.student_id',DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
                        'sex','occupation_name','level_no','result','certificate_no','registration_no');

                        $tvet=$tvet->addSelect(DB::raw("'tvet' as type"));


        $all=$external
             ->unionAll($degree)
             ->unionAll($tvet)
            //  ->groupBy('level_no')->select('level_no')
             ->paginate($per_page);

             return response()->json($all,200);
    }

    public function generateCocRequestForm($coc_id){

        $external=DB::table('external_coc_applicants')
        ->where('coc_id',request('coc_id'))
        //  ->join('levels' , 'external_coc_applicants.level_id' ,'=' ,'levels.id')
         ->join('cocs', function($join){
             $join->on('external_coc_applicants.coc_id','=' ,'cocs.id')
             ->where('cocs.id',request('coc_id'));
         })
         ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"),
         'sex','occupation_name','level_no','result','certificate_no','registration_no');

$degree=DB::table('degree_students')
           ->join('coc_degree_student' , 'coc_degree_student.degree_student_id' ,'=' ,'degree_students.id')
           ->where('coc_id',request('coc_id'))
        //    ->join('levels' , 'coc_degree_student.level_id' ,'=' ,'levels.id')
           ->join('cocs', function($join){
               $join->on('coc_degree_student.coc_id','=' ,'cocs.id')
               ->where('cocs.id',request('coc_id'));
           })
           ->select(DB::raw("CONCAT(degree_students.first_name, ' ', degree_students.last_name) AS full_name"),
           'sex','occupation_name','level_no','result','certificate_no','registration_no');

$tvet=DB::table('tvet_students')
           ->join('coc_tvet_student' , 'coc_tvet_student.tvet_student_id' ,'=' ,'tvet_students.id')
           ->where('coc_id',request('coc_id'))
        //    ->join('levels' , 'coc_tvet_student.level_id' ,'=' ,'levels.id')
           ->join('cocs', function($join){
               $join->on('coc_tvet_student.coc_id','=' ,'cocs.id')
               ->where('cocs.id',request('coc_id'));
           })
            ->select(DB::raw("CONCAT(tvet_students.first_name, ' ', tvet_students.last_name) AS full_name"),
           'sex','occupation_name','level_no','result','certificate_no','registration_no');

            // ->select(DB::raw("count(*)  AS F"),
            // // DB::raw("count(sex) where sex = male AS M"),
            // 'levels.occupation_name','levels.level_no')
            //     ->groupBy('sex')
            //     // ->having('sex','=','male')
            //     ->groupBy('occupation_name')
            //     ->groupBy('levels.level_no')->get();
            // ;
    $all=$external
    ->unionAll($degree)
    ->unionAll($tvet);
    // ->select('level_no');
    // ->get();
    //  ->groupBy('level_no')->select('level_no')
    // ->paginate($per_page);

    $querySql = $all->toSql();
    $query = DB::table(DB::raw("($querySql ) as a"))
    ->mergeBindings($all);
    // ->get();
   return $query
           ->select(
            // DB::raw(" SUM(CASE WHEN sex = 'male' THEN 1 ELSE 0 END) as M"),
            // DB::raw(" SUM(CASE WHEN sex = 'female' THEN 1 ELSE 0 END) as F"),
            DB::raw(" COUNT(CASE WHEN sex = 'male' THEN 1  END) as M"),
            DB::raw(" COUNT(CASE WHEN sex = 'female' THEN 1  END) as F"),
            DB::raw(" COUNT(*) as Total"),
             'occupation_name','level_no')
                // ->groupBy('sex')
                // ->having('sex','=','male')
                ->groupBy('occupation_name')
                ->groupBy('level_no')->get();
    return response()->json($all,200);
    }
}
