<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Coc;
use Illuminate\Http\Request;

class CocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return request('academic_year_id');
        $academic_year_id=null;
        if (request()->filled('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
            return response()->json(['select academic year'],400);
                   // $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        return response()->json(Coc::where('academic_year_id',$academic_year_id)->get(),200);
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
            'start_date'=>'required',
            'end_date'=>'required',
            'exam_week'=>'required',
            'academic_year_id'=>'required',

        ]);
      return response()->json(Coc::create($request->all()),201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coc  $coc
     * @return \Illuminate\Http\Response
     */
    public function show(Coc $coc)
    {
        return  $coc;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coc  $coc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coc $coc)
    {
        $request->validate([
            'start_date'=>'required',
            'end_date'=>'required',
            'exam_week'=>'required',
            'academic_year_id'=>'required',



        ]);
        $coc->update($request->all());
        return response()->json($coc,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coc  $coc
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coc $coc)
    {
        $coc->delete();
    }
}
