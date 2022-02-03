<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{

    public function getSettingData(){
        $academic_year=null;
        if (request('academic_year_id')) {
            $academic_year=AcademicYear::find(request('academic_year_id'));

        }else {
            $academic_year=AcademicYear::where('is_current',1)->first();
        }

        $reg_id=Program::where('name','Regular')->where('type','degree')->first()->id;
        $ext_id=Program::where('name','Extension')->where('type','degree')->first()->id;

        foreach($academic_year->semesters as $semester){

            if($semester->program_id == $reg_id){
                $regular[]=$semester;

            }else if($semester->program_id == $ext_id){

                $ext[]=$semester;


            }

        }
        // DB::table('dynamic_system_settings')
        // ->update(['degree_teacher_result_entry_time'=>request('entry_status')]);

        $result_related= DB::table('dynamic_system_settings')->first();
        $act['year'] =  $academic_year->year;
        $act['id'] =  $academic_year->id;
        $act['is_closed'] =  $academic_year->is_closed;
        $act['regular']=$regular;
        $act['extension']=$ext;
        $act['result_related']=$result_related;

        return response()->json($act,200);

    }
    public function changeAcademicYearStatus($acc_id){

       $acc_year= AcademicYear::find($acc_id);
       $acc_year->is_closed=request('status');
       $acc_year->save();

       return response()->json($acc_year->is_closed,200);
    }

    public function changeSemesterStatus($seme_id){

        $seme= Semester::find($seme_id);
        $seme->is_closed=request('status');
        $seme->save();
        return response()->json($seme->is_closed,200);

     }

     public function changeDegreeTeacherResultEntryTime(){

         DB::table('dynamic_system_settings')
        ->update(['degree_teacher_result_entry_time'=>request('entry_status')]);

        return response()->json(request('entry_status'),200);

    }

    public function changeTvetTeacherResultEntryTime(){

        DB::table('dynamic_system_settings')
       ->update(['tvet_teacher_result_entry_time'=>request('entry_status')]);

       return response()->json(request('entry_status'),200);

   }

   public function changeDegreeRegistrarResultEntryTime(){

    DB::table('dynamic_system_settings')
   ->update(['degree_registrar_result_entry_time'=>request('entry_status')]);

   return response()->json(request('entry_status'),200);

}

public function changeTvetRegistrarResultEntryTime(){

    DB::table('dynamic_system_settings')
   ->update(['tvet_registrar_result_entry_time'=>request('entry_status')]);

   return response()->json(request('entry_status'),200);

}


}
