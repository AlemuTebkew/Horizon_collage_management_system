<?php

namespace App\Http\Controllers;

use App\Models\TvetSection;
use Illuminate\Http\Request;

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
            'tvet_department_id'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
             'level_id'=>'required',

        ]);
      return TvetSection::create($request->all());
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
       $tvetSection->update($request->all());
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
        $tvetSection->delete();
    }
}
