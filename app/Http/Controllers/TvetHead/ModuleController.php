<?php

namespace App\Http\Controllers\TvetHead;

use App\Http\Controllers\Controller;
use App\Http\Resources\Module\ModuleResource;
use App\Http\Resources\Module\SectionModuleResource;
use App\Models\DegreeSection;
use App\Models\Employee;
use App\Models\Level;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\TvetSection;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function getDepartmentModules(){

        $employee=Employee::where('email',request()->user()->user_name)->first();
        // return $employee;
           $tvet_department=$employee->managet;


        return  ModuleResource::collection ( Module::where('tvet_department_id',$tvet_department->id)
        ->with('department','program')->get());
    }

    public function getSectionModules(){
        $ts=TvetSection::find(request('section_id'));
        $level=Level::find($ts->level_id);
        $modules=Module::where('tvet_department_id',$ts->tvet_department_id)
                        // ->where('program_id',$ts->program_id)
                        ->where('level_id',$ts->level_id)
                        ->get();

                        $all=[];
                        foreach ($modules as $module) {
                            $teacher=$module->teachers()->where('tvet_section_id',$ts->id)->first();

                            $module1['id']=$module->id;
                            $module1['title']=$module->title;
                            $module1['code']=$module->code;
                            $module1['training_hour']=$module->training_hour;
                            $module1['instructor']= $teacher ? $teacher->full_name: null;
                            $all[]=$module1;
                        }

                        return response()->json($all,200);
    //  return SectionModuleResource::collection( $modules->load('department','program','teachers'));

    }

    public function assignTeacherForModule(){
        $ts=TvetSection::find(request('section_id'));
        $module=Module::find(request('module_id'));
        $teacher=Teacher::find(request('teacher_id'));
        $assign=$module->teachers()->wherePivot('teacher_id',request()->teacher_id)
                           ->wherePivot('tvet_section_id',request()->section_id)->first();
        if(!$assign || empty($assign) || $assign ==null ) {
            $teacher->modules()->attach($module->id,[
                'tvet_section_id'=>$ts->id,
            ]);
        }
        else{
            $teacher->modules()->updateExistingPivot($module->id,[
                'tvet_section_id'=>$ts->id,
            ]);
        }


        return $teacher->full_name;
    }

}
