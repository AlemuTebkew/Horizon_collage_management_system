<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;

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
        return Coc::all();
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
      return Coc::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coc  $coc
     * @return \Illuminate\Http\Response
     */
    public function show(Coc $coc)
    {
        return $coc;
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
      return $coc->update($request->all());
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
