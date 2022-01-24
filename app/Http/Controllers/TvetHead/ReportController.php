<?php

namespace App\Http\Controllers\TvetHead;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Level;
use App\Models\Module;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getModuleTakenByLevel(Request $request){

        $employee=Employee::where('email',request()->user()->user_name)->first();
        $department=$employee->managet;
        $level=Level::where('tvet_department_id',$department->id)
        ->where('level_no',$request->level_no)->first();

          $modules=Module::where('tvet_department_id',$department->id)
                            // ->where('program_id',$request->program_id)
                            ->where('level_id',$level->id)->get();
          $fee_type_cp=FeeType::where('name','Monthly Fee')->first();
          $fee_type_reg=FeeType::where('name','Registration Fee')->first();

        //   $ac_y=AcademicYear::find($request->academic_year_id);
        //   $ac_y->fee_types->
          $cp_fee= $fee_type_cp->academic_years()->wherePivot('academic_year_id',$request->academic_year_id)->first()->pivot->amount;
          $registration_fee= $fee_type_reg->academic_years()->wherePivot('academic_year_id',$request->academic_year_id)->first()->pivot->amount;
        return response([
            'monthlyFee'=>$cp_fee,
             'registrationFee'=>$registration_fee,
             'module'=>$modules,
        ],200
        );
   }
    public function getGradeReport(Request $request){

       $employee=Employee::where('email',request()->user()->user_name)->first();
       $department=$employee->managet;
       $all_students=[];
       $student1=[];


       foreach ($request->student_ids as $student_id) {
        $modules=[];
          $student=TvetStudent::find($student_id);
          $level_id=Level::where('tvet_department_id',$department->id)
                     ->where('level_no',request('level_no'))->first();
          $studentModules=$student->modules()->wherePivot('level_id',$level_id)
                                        ->get();

                                        // $studentModules=Module::
                                        // get();
          $level=$student->levels()->where('level_id',request()->level_id)->first();

         foreach ($studentModules as $studentModule) {

            $module=[];
            // $module['student_id']=$studentModule->student_id;
            $module['code']=$studentModule->code;
            $module['title']=$studentModule->title;
            $module['level_no']=request('level_no');
            $module['training_hour']=$studentModule->training_hour;
            $module['total_mark']=$studentModule->pivot->total_mark;

           $modules[]=$module;

         }

         $student1['id']=$student->id;
         $student1['student_id']=$student->student_id;
         $student1['first_name']=$student->first_name;
         $student1['last_name']=$student->last_name;
         $student1['sex']=$student->sex;
         $student1['addmission_year']=$student->batch;
         $student1['program']=$student->program->name;
         $student1['department_name']=$student->tvet_department->name;

         $student1['modules']=$modules;

          $all_students[]=$student1;

       }
       return $all_students;


    }

}
