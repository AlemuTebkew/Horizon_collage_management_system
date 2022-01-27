<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeDepartment as ResourcesDegreeDepartment;
use App\Http\Resources\DegreeDepartmentResource;
use App\Models\DegreeDepartment;
use App\Models\Employee;
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
        return  DegreeDepartmentResource::collection(DegreeDepartment::with('programs','manager')->get());
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
            'short_name'=>'required',


        ]);

        $c= DegreeDepartment::where('name',$request->name)->first();
        $c1= DegreeDepartment::where('short_name',$request->short_name)->first();
        if ($c || $c1) {
            return response()->json('The department name or short name',202);
        }
         $dp= DegreeDepartment::create(['name'=>$request->name,'short_name'=>$request->short_name]);

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

    return new DegreeDepartmentResource($dp->load('programs','manager'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeDepartment  $degreeDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeDepartment $degreeDepartment)
    {
        return $degreeDepartment->load('programs','manager');
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
            'short_name'=>'required',
        ]);
        $c= DegreeDepartment::where('name',$request->name)
                       ->where('name','!=',$degreeDepartment->name)->first();
        $c1= DegreeDepartment::where('short_name',$request->short_name)
                    ->where('name','!=',$degreeDepartment->short_name)->first();
        if ($c || $c1) {
            return response()->json('The department name or short name',202);
        }

        $degreeDepartment->update(['name'=>$request->name,'short_name'=>$request->short_name]);

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

           return new DegreeDepartmentResource($degreeDepartment->load('programs','manager'));

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

      $head=Employee::find($request->employee_id);
      $head->role='degree_head';
      $head->save();
      return response()->json(new DegreeDepartmentResource($department->load('programs','manager')),200);

    }

    public function unAssignDepartmentHead(Request $request){
        $department=DegreeDepartment::find($request->department_id);
        $department->update(['department_head_id'=>null]);

        $head=Employee::find($request->employee_id);
        $head->role='department_head';
        $head->save();
        return response()->json(new DegreeDepartmentResource($department->load('programs','manager')),200);

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
