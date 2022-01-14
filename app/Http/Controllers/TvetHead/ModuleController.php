<?php

namespace App\Http\Controllers\TvetHead;

use App\Http\Controllers\Controller;
use App\Http\Resources\Module\ModuleResource;
use App\Http\Resources\Module\SectionModuleResource;
use App\Models\Employee;
use App\Models\Level;
use App\Models\Module;
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
        $courses=Module::where('tvet_department_id',$ts->tvet_department_id)
                        ->where('program_id',$ts->program_id)
                        ->where('level_id',$ts->level_id)->get();
     return SectionModuleResource::collection( $courses->load('department','program','teacher'));

    }

}
