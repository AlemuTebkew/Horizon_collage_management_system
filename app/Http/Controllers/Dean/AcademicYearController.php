<?php

namespace App\Http\Controllers\Dean;
use  App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AcademicYear::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //year','start_date','end_date','status','is_current
        $request->validate([
            'year'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
       $ay=AcademicYear::where('status',1)->first();
       if ($ay) {
           return response()->json(['close active Academic year before creating']);
       }
       $data=$request->all();
       $data['start_date']=date('Y-m-d',strtotime($request->start_date));
       $data['end_date']=date('Y-m-d',strtotime($request->end_date));

       
      return AcademicYear::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
        return $academicYear;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'year'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
        $data=$request->all();
        $data['start_date']=date('Y-m-d',strtotime($request->start_date));
        $data['end_date']=date('Y-m-d',strtotime($request->end_date));
      return $academicYear->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
    }

    public function closeAcademicYear(AcademicYear $academicYear){
      $academicYear->update(['status'=>0,'is_current'=>0]);
    }
}
