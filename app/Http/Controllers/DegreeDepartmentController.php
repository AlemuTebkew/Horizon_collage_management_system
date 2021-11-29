<?php

namespace App\Http\Controllers;

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
            'name'=>'required',
            'no_of_semester'=>'required',
            'no_of_year'=>'required',

        ]);
      return DegreeDepartment::create($request->all());
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
            'no_of_semester'=>'required',
            'no_of_year'=>'required',

        ]);
        $degreeDepartment->update($request->all());
        return $degreeDepartment;

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
}
