<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Http\Resources\TvetDepartmentResource;
use App\Models\Employee;
use App\Models\Level;
use App\Models\Program;
use App\Models\TvetDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TvetDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TvetDepartmentResource::collection(TvetDepartment::with('levels')->get());
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
            'name'=>'required|unique:tvet_departments',
            'short_name'=>'required|unique:tvet_departments',

        ]);

     try {

        DB::beginTransaction();
        if ($request->levels->count() > 0) {
         $td= TvetDepartment::create(['name'=>$request->name,'sector'=>$request->sector,'short_name'=>$request->short_name]);
         $programs=['regular','extension'];
         foreach ($programs as $program) {
           $id=Program::where(function($q1) use ($program){
                        $q1->where('name', strtoupper($program))
                        ->orWhere('name', strtolower($program));
                    })
                        ->where(function($q){
                            $q->where('type','tvet')
                            ->orWhere('type','Tvet');
                        })->first()->id;
            $td->programs()->attach($id);
           }

          foreach ($request->levels as $level) {
              $l=new Level();
              $l->level_no=$level['level_no'];
              $l->occupation_name=$level['occupation_name'];
              $l->tvet_department_id=$td->id;
              $l->save();
          }

          DB::commit();
          return new TvetDepartmentResource($td->load('programs','manager'));

        }
     } catch (\Throwable $th) {
        DB::rollBack();
     }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TvetDepartment  $tvetDepartment
     * @return \Illuminate\Http\Response
     */
    public function show(TvetDepartment $tvetDepartment)
    {
        return $tvetDepartment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TvetDepartment  $tvetDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TvetDepartment $tvetDepartment)
    {
        $request->validate([
            'name'=>'required',
            'short_name'=>'required',

        ]);

        try {

            DB::beginTransaction();

            $tvetDepartment->update(['name'=>$request->name,'sector'=>$request->sector,'short_name'=>$request->short_name]);

            if(! $tvetDepartment->levels->isEmpty()){
                $tvetDepartment->levels()->delete();
            }

               foreach ($request->levels as $level) {
                $l=new Level();
                $l->level_no=$level['level_no'];
                $l->occupation_name=$level['occupation_name'];
                $l->tvet_department_id=$tvetDepartment->id;
                $l->save();
            }
            DB::commit();
            return new TvetDepartmentResource($tvetDepartment->load('programs','manager'));


        } catch (\Throwable $th) {
            DB::rollBack();
        }

        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetDepartment  $tvetDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetDepartment $tvetDepartment)
    {
        if ($tvetDepartment->tvet_students()->count() == 0) {
            $tvetDepartment->programs()->detach();
            $tvetDepartment->delete();

            return  response()->json('Successfully deleted',200);
        }else {
            return  response()->json('impossible delete Department that have student',202);
        }

    }


    public function assignDepartmentHead(Request $request){
        $department=TvetDepartment::find($request->department_id);
        $department->update(['department_head_id'=>$request->employee_id]);
        $head=Employee::find($request->employee_id);
        $head->role='tvet_head';
        $head->save();
        return response()->json(new TvetDepartmentResource($department->load('programs','manager')),200);
    }


    public function unAssignDepartmentHead(Request $request){
        $department=TvetDepartment::find($request->department_id);
        $department->update(['department_head_id'=>null]);
        $head=Employee::find($request->employee_id);
        $head->role='department_head';
        $head->save();
        return response()->json(new TvetDepartmentResource($department->load('programs','manager')),200);
    }
}
