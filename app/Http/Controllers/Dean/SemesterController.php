<?php

namespace App\Http\Controllers\Dean;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Semester::all();
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
            'number'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
        $data=$request->all();
        $data['start_date']=date('Y-m-d',strtotime($request->start_date));
        $data['end_date']=date('Y-m-d',strtotime($request->end_date));
        return Semester::create($data);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        return $semester;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'number'=>'required',
            'academic_year_id'=>'required',
            'program_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
        $data=$request->all();
        $data['start_date']=date('Y-m-d',strtotime($request->start_date));
        $data['end_date']=date('Y-m-d',strtotime($request->end_date));
         $semester->update($data);
         return $semester;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();
    }
}
