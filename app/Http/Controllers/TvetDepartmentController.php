<?php

namespace App\Http\Controllers;

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
            'sector'=>'required',

        ]);
      return TvetDepartment::create($request->all());
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
}
