<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TvetStudentInfo\LevelResource;
use App\Http\Resources\TvetStudentInfo\ModuleResource;
use App\Http\Resources\TvetStudentInfo\SectionResource;
use App\Http\Resources\TvetStudentInfo\StudentCocResource;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\TvetDepartment;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class TvetStudentInfoController extends Controller
{
    public function myTuition(Request $request,$id){

        $tvetStudent=TvetStudent::find($id);
        // ->with('semesters','month_payments')
        // return $tvetStudent->levels;

        $years=[];

                $levels=$tvetStudent->levels;
                if ($levels->isEmpty() == false) {
                foreach($levels as $level){
                $academic_year_id=$level->pivot->academic_year_id;
                $academic_year=AcademicYear::find($academic_year_id);
                $years[$academic_year_id]=$academic_year;
                }
            }else {
                return 'not registerd';
            }

            $year=[];
            foreach($years as $y){
                $year['year']=$y->year;
                $pads=[];
                $month_payments= $tvetStudent->month_payments()
                ->orderBy('number')
                ->wherePivot('academic_year_id',$y->id)->get();
                 $total=0.0;
                 foreach($month_payments as $month_payment){
                    if($y->id == $month_payment->pivot->academic_year_id){
                        // $pads['month']=$month_payment->name;
                        // $pads['pad']=$month_payment->pivot->receipt_no;
                        if ($month_payment->pivot->payable_status == 'unpayable') {
                            $pads[$month_payment->name]='አይከፈልም እሽ';

                        }else {
                            $pads[$month_payment->name]=$month_payment->pivot->receipt_no;
                        }

                    $total+=number_format ($month_payment->pivot->paid_amount);
                    //  $year['t'][]=$pads;
                    }

                }

                $year['total']=$total;
                $year['months']= $pads;
                $student[]=$year;
            }
            return  response()->json($student,200);
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
