<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
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
        $academic_year_id=null;
        if (request()->has('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }
        else{
            $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }
          return TvetSection::with('tvet_department','levels');
        

        // return DegreeSectionResource::collection(DegreeSection::with('degree_department','semester')
        //       ->where('academic_year_id',$academic_year_id)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
