<?php

namespace App\Http\Controllers\TvetHead;

use App\Models\TvetSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Head\TvetSectionResource;
use App\Http\Resources\Head\TvetSectionStudentResource;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class TvetSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TvetSection::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'tvet_department_id'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
             'level_id'=>'required',

        ]);
        $data=$request->all();
        $employee=Employee::where('email',$request->user()->user_name)->first();

       $data['tvet_department_id']=$employee->managet->id;
    //   $data['semester_no']=Semester::find($request->semester_id)->number;
      $s=TvetSection::where('name',$request->name)
                             ->where('level_id',$request->level_id)
                             ->where('tvet_department_id',$employee->managet->id)
                             ->where('academic_year_id',$request->academic_year_id)
                             ->where('program_id',$request->program_id)
                             ->first();
      if (!$s) {
        $ts= TvetSection::create($data);
        return response()->json(new TvetSectionResource($ts->load('tvet_department','level','program')),200) ;
      }else {
          return response()->json(['error_message'=>'Section Already Added'],400);
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TvetSection  $tvetSection
     * @return \Illuminate\Http\Response
     */
    public function show(TvetSection $tvetSection)
    {
        return $tvetSection;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TvetSection  $tvetSection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TvetSection $tvetSection)
    {
        $request->validate([
            'tvet_department_id'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
             'level_id'=>'required',

        ]);
        $data=$request->all();
        $employee=Employee::where('email',$request->user()->user_name)->first();

       $data['tvet_department_id']=$employee->managet->id;
    //   $data['semester_no']=Semester::find($request->semester_id)->number;
      $s=TvetSection::where('name',$request->name)
                             ->where('level_id',$request->level_id)
                             ->where('tvet_department_id',$employee->managet->id)
                             ->where('academic_year_id',$request->academic_year_id)
                             ->where('program_id',$request->program_id)
                             ->first();
      if (!$s) {
        $tvetSection->update($data);
        return response()->json(new TvetSectionResource($tvetSection->load('tvet_department','level','program')),200) ;
      }else {
          return response()->json(['error_message'=>'Section Already Added'],400);
      }
       return $tvetSection;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetSection  $tvetSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetSection $tvetSection)
    {
        DB::beginTransaction();
        try {

            if ($tvetSection->tvet_students->isEmpty()) {
                $tvetSection->delete();
            }else {
                return response()->json(['You can not delete'],500);

            }
            DB::commit();
            return response()->json(['succesfully deleted'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }




}
