<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\DegreeDepartment;
use Illuminate\Http\Request;

class DegreeDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DegreeDepartment::all();
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
            'name'=>'required|unique:degree_departments',
            // 'programs..no_of_semester'=>'required',
            // 'no_of_year'=>'required',

        ]);
         //  return $request->programs;
//request->only([]);
      $dp= DegreeDepartment::create(['name'=>$request->name]);

      foreach ($request->programs as $program) {
 //     return $program['program_id'];
    //    $check= DegreeDepartment::where('id',$dp) ->whereHas('programs' , function($q) use($program){
    //       $q->where('program_id',$program->program_id);
    //     })->get();

        // if (! $dp->programs()->where('id',$program['program_id'])->get()) {
            // return true;
            $dp->programs()->attach($program['program_id'],
            ['no_of_semester'=>$program['no_of_semester'],'no_of_year'=>$program['no_of_year']]);
        // }

      }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeDepartment  $degreeDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeDepartment $degreeDepartment)
    {
        return $degreeDepartment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeDepartment  $degreeDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeDepartment $degreeDepartment)
    {
        $request->validate([
            'name'=>'required',

        ]);
        $degreeDepartment->update(['name'=>$request->name]);

        //    $degreeDepartment->programs()->detach();
        foreach ($request->programs as $program) {

                 /*
                   //this is an other option ---detaching all first and attach it again
                    $degreeDepartment->programs()->attach($program->program_id,
                    ['no_of_term'=>$program->no_of_term,'no_of_year'=>$program->no_of_year]);
                  */

                    if ($degreeDepartment->programs()->where('id',$program->program_id)->get()) {
                        $degreeDepartment->programs()->updateExistingPivot($program->program_id,
                        ['no_of_term'=>$program->no_of_term,'no_of_year'=>$program->no_of_year]);
                    }else {
                        $degreeDepartment->programs()->attach($program->program_id,
                        ['no_of_term'=>$program->no_of_term,'no_of_year'=>$program->no_of_year]);
                    }
              }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeDepartment  $degreeDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeDepartment $degreeDepartment)
    {
        $degreeDepartment->delete();
    }

    public function assignDepartmentHead(Request $request){
      $department=DegreeDepartment::find($request->department_id);
      $department->update(['department_head_id'=>$request->employee_id]);
      return $department;
    }

    public function getDepartmentsWithPrograms(Request $request){
        $departments=DegreeDepartment::with('programs');
        return $departments;
      }

      public function getDepartmentWithPrograms(DegreeDepartment $degreeDepartment){
        $department=$degreeDepartment->load('programs');
        return $department;
     }
}
