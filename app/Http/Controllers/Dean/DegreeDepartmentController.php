<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeDepartment as ResourcesDegreeDepartment;
use App\Http\Resources\DegreeDepartmentResource;
use App\Models\DegreeDepartment;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DegreeDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  DegreeDepartmentResource::collection(DegreeDepartment::with('programs')->get());
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
          //  'name'=>'required|unique:degree_departments',


        ]);
          // return $request->all();
//request->only([]);
      $dp= DegreeDepartment::create(['name'=>$request->name]);

      foreach ($request->programs as $program) {
           $id=Program::where(function($q1) use ($program){
                        $q1->where('name', strtoupper($program['type']))
                        ->orWhere('name', strtolower($program['type']));
                    })
                        ->where(function($q){
                            $q->where('type','degree')
                            ->orWhere('type','Degree');
                        })->first()->id;
            $dp->programs()->attach($id,
            ['no_of_semester'=>$program['no_of_semester'],'no_of_year'=>$program['no_of_year']]);


      }

    return new DegreeDepartmentResource($dp->load('programs'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeDepartment  $degreeDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeDepartment $degreeDepartment)
    {
        return $degreeDepartment->load('programs');
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

        foreach ($request->programs as $program) {
          $id=Program::where(function($q1) use ($program){
                       $q1->where('name', strtoupper($program['type']))
                       ->orWhere('name', strtolower($program['type']));
                   })
                       ->where(function($q){
                           $q->where('type','degree')
                           ->orWhere('type','Degree');
                       })->first()->id;
           $degreeDepartment->programs()->updateExistingPivot($id,
           ['no_of_semester'=>$program['no_of_semester'],'no_of_year'=>$program['no_of_year']]);

           return new DegreeDepartmentResource($degreeDepartment->load('programs'));

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
      return $department->load('manager');
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
