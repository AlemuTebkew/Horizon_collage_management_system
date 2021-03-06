<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearDetail extends Controller
{

    public function editAcademicYearDetail(){

        $academic_year=null;
        if (request('academic_year_id')) {
            $academic_year=AcademicYear::find(request('academic_year_id'));

        }else {
            $academic_year=AcademicYear::where('is_current',1)->first();
        }

        $reg_id=Program::where('name','Regular')->where('type','degree')->first()->id;
        $ext_id=Program::where('name','Extension')->where('type','degree')->first()->id;

        $regular=[];
        $ext=[];
        foreach($academic_year->semesters as $semester){

            if($semester->program_id == $reg_id){
                $regular[]=$semester->load(['months:id','degree_calender_activities']);

            }else if($semester->program_id == $ext_id){

                $ext[]=$semester->load('months:id','degree_calender_activities');


            }

        }

        $tvet_calender= DB::table('tvet_calenders')->where('academic_year_id',$academic_year->id)->first();
        $tvet_activities= DB::table('tvet_calender_activities')->where('academic_year_id',$academic_year->id)->get();
        $act['year'] =  $academic_year->year;
        $act['regular']=$regular;
        $act['extension']=$ext;
        $act['tvet_calender']=$tvet_calender;
        $act['tvet_activities']=$tvet_activities;

        return response()->json($act,200);

    }


    public function updateSemester(Request $request){

        $academic_year= AcademicYear::find($request->academic_year_id);
        $academic_year->year=$request->year;
        $academic_year->save();


        //updating all semesters
        foreach ($request->semesters as  $semester ) {

           $new_s= Semester::find($semester['semester_id']);
           $new_s->start_date=date('Y-m-d',strtotime($semester['start_date']));
           $new_s->end_date=date('Y-m-d',strtotime($semester['end_date']));
           $new_s->number=$semester['number'];
           $new_s->program_id=$semester['program_id'];
           $new_s->academic_year_id=$academic_year->id;


           $new_s->save();
        }

        DB::table('tvet_calenders')
        ->where('academic_year_id',$academic_year->id)
        ->update([
            'start_date'=>date('Y-m-d',strtotime($request->tvet_start_date)),
            'end_date'=> date('Y-m-d',strtotime($request->tvet_end_date)) ,
            'academic_year_id'=> $academic_year->id ,
        ]);


        return response()->json('successfully updated',200);
    }
    public function updateSemesterMonth(Request $request){

        foreach ($request->semesters as  $semester ) {

           $new_s= Semester::find($semester['semester_id']);
             //attaching semester months
             $month_ids=$semester['months'];
             $new_s->months()->sync($month_ids);
             //end
        }

        return response()->json('successfully updated',200);
    }

    public function updateSemesterActivities(Request $request){


        DB::beginTransaction();
        try {
            foreach ($request->semesters as  $semester ) {

                // $new_s= Semester::find($semester['semester_id']);
                  DB::table('degree_calender_activities')
                         ->where('semester_id', $semester['semester_id'])->delete();
                  $activities=$semester['activities'];
                  foreach ($activities as $activity) {
                     DB::table('degree_calender_activities')
                         ->insert($activity);
                  }

             }

                 //creating tvet calender

              $st= DB::table('tvet_calender_activities')
                        ->where('academic_year_id',$request->academic_year_id)
                        ->delete();

                 foreach ($request->tvet_activities as $activity) {
                    
                    // unset($activity->id);
                 
                    DB::table('tvet_calender_activities')
                    ->insert([
                        'activity'=>$activity['activity'],
                        'date'=>$activity['date'],
                        'academic_year_id'=>$activity['academic_year_id'],
                    ]);
                  }

             return response()->json('successfully updated',200);
        } catch (\Throwable $th) {
            return response()->json($th.'not updated',500);

        }

    }
}
