<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Http\Resources\Module\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ModuleResource::collection(Module::with('department','program')->get());
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
            'code'=>'required',
            'title'=>'required',
            'training_hour'=>'required',
            'level_id'=>'required',
            'tvet_department_id'=>'required',

        ]);
      $module= Module::create($request->all());
      return response()->json([new ModuleResource($module->load('department','program')),200]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        return response()->json([new ModuleResource($module->load('department','program')),200]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'code'=>'required',
            'title'=>'required',
            'training_hour'=>'required',
            'level_id'=>'required',
            'tvet_department_id'=>'required',

        ]);
       $module->update($request->all());
       return response()->json([new ModuleResource($module->load('department','program')),200]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        $module->delete();
    }
}
