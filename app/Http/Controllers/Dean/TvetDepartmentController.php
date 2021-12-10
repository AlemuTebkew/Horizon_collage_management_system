<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;

use App\Models\Level;
use App\Models\TvetDepartment;
use Illuminate\Http\Request;

class TvetDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TvetDepartment::all();
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
            // 'sector'=>'required',

        ]);
      $td= TvetDepartment::create(['name'=>$request->name,'sector'=>$request->sector]);

       foreach ($request->levels as $level) {
           $l=new Level();
           $l->level_no=$level['level_no'];
           $l->occupation_name=$level['occupation_name'];
           $l->tvet_department_id=$td->id;
           $l->save();
       }
       return $td->load('levels');
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
            'sector'=>'required',

        ]);
       $tvetDepartment->update($request->all());
      return $tvetDepartment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetDepartment  $tvetDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetDepartment $tvetDepartment)
    {
        $tvetDepartment->delete();
    }


    public function assignDepartmentHead(Request $request){
        $department=TvetDepartment::find($request->department_id);
        $department->update(['department_head_id'=>$request->employee_id]);
        return $department;
      }
}
