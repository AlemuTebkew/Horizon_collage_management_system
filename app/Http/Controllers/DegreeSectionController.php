<?php

namespace App\Http\Controllers;

use App\Models\DegreeSection;
use Illuminate\Http\Request;

class DegreeSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DegreeSection::all();
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
            'year_no'=>'required',
            'degree_department_id'=>'required',
            'academic_year_id'=>'required',
            'semester_id'=>'required',

        ]);
      return DegreeSection::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeSection $degreeSection)
    {
        return $degreeSection;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeSection $degreeSection)
    {
        $request->validate([
            'name'=>'required',
            'year_no'=>'required',
            'degree_department_id'=>'required',
            'academic_year_id'=>'required',
            'semester_id'=>'required',

        ]);
      return $degreeSection->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeSection $degreeSection)
    {
        $degreeSection->delete();
    }
}
