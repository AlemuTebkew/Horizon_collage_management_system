<?php

namespace App\Http\Controllers;

use App\Models\BranchOffice;
use Illuminate\Http\Request;

class BranchOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BranchOffice::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //user_name','email','password','location
        $request->validate([
            'user_name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'location'=>'required',

        ]);
      return BranchOffice::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BranchOffice  $branchOffice
     * @return \Illuminate\Http\Response
     */
    public function show(BranchOffice $branchOffice)
    {
        return $branchOffice;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BranchOffice  $branchOffice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BranchOffice $branchOffice)
    {
        $request->validate([
            'user_name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'location'=>'required',

        ]);
      return $branchOffice->update($request->all());

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BranchOffice  $branchOffice
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchOffice $branchOffice)
    {
        $branchOffice->delete();
    }
}
