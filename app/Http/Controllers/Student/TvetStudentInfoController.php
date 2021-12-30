<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TvetStudentInfo\LevelResource;
use App\Http\Resources\TvetStudentInfo\ModuleResource;
use App\Http\Resources\TvetStudentInfo\SectionResource;
use App\Http\Resources\TvetStudentInfo\StudentCocResource;
use App\Models\Level;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class TvetStudentInfoController extends Controller
{
    public function myTuitionFee(Request $request){

    }

    public function myCourse(Request $request,$id){
        $student=TvetStudent::find($id);
        $department=TvetDepartment::find($student->tvet_department_id);
        $modules=$department->modules()->get();
        return response()->json(ModuleResource::collection($modules),200);

    }

    public function myGrade(Request $request,$id){
        $student=TvetStudent::find($id);
        $studentModules=$student->modules()
                                        //    ->where('program_id',request()->program_id)
                                        //     ->where('year_no',request()->year_no)
                                        //     ->where('semester_no',request()->semester_no)
                                            ->get();

          $modules=[];

         foreach ($studentModules as $studentModule) {
             $course=[];

          $course['code']=$studentModule->code;
          $course['title']=$studentModule->title;
          $course['level_no']=Level::find($studentModule->pivot->level_id)->level_no ?? null;
          $course['training_hour']=$studentModule->training_hour;
          $course['total_mark']=$studentModule->pivot->total_mark;


         $modules[]=$course;

       }
       return response()->json($modules,200);
    }


    public function myStatus(Request $request,$id){

        $student=TvetStudent::find($id);
        return response()->json(LevelResource::collection($student->levels),200);
    }

    public function myCoc(Request $request,$id){
        $student=TvetStudent::find($id);
        return  response()->json(StudentCocResource::collection($student->cocs),200);

    }

    public function mySection(Request $request,$id){
        $student=TvetStudent::find($id);
        return response()->json(SectionResource::collection($student->tvet_sections),200);
    }
}
