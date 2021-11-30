<?php

namespace App\Http\Controllers;

use App\Models\ExternalCocApplicant;
use Illuminate\Http\Request;

class ExternalCocApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ExternalCocApplicant::all();
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
            'first_name'=>'required',
            'last_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'marital_status'=>'required',
            'coc_id'=>'required',
            'level_id'=>'required',
            'application_date'=>'required',
            'result'=>'required',
            'nature_of_assesment'=>'required',


        ]);
     return ExternalCocApplicant::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExternalCocApplicant  $externalCocApplicant
     * @return \Illuminate\Http\Response
     */
    public function show(ExternalCocApplicant $externalCocApplicant)
    {
       return $externalCocApplicant;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExternalCocApplicant  $externalCocApplicant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExternalCocApplicant $externalCocApplicant)
    {
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'marital_status'=>'required',
            'coc_id'=>'required',
            'level_id'=>'required',
            'application_date'=>'required',
            'result'=>'required',
            'nature_of_assesment'=>'required',


        ]);
       $externalCocApplicant->update($request->all());
      return $externalCocApplicant;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExternalCocApplicant  $externalCocApplicant
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExternalCocApplicant $externalCocApplicant)
    {
        $externalCocApplicant->delete();
    }
}
