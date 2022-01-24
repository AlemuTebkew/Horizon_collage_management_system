<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FeeType::all();
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
            'name'=>'required|unique:fee_types',

        ]);


     $fee= FeeType::create($request->all());


     $ac_y=AcademicYear::find(request('academic_year_id'));
     $ac_y->fee_types()->attach($fee->id,[
         'amount'=>request('amount')
     ]);

     return response()->json(['id'=>$fee->id,'name'=>$fee->name,'amount'=>request('amount')],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function show(FeeType $feeType)
    {
        return $feeType;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeeType $feeType)
    {
        $request->validate([
            'name'=>'required',

        ]);
      $feeType->update($request->all());
      return $feeType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeeType $feeType)
    {
        $feeType->delete();
    }
}
