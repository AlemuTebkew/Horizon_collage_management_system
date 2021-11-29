<?php

namespace App\Http\Controllers;

use App\Models\BranchOfficeFile;
use Illuminate\Http\Request;

class BranchOfficeFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BranchOfficeFile::all();

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
            'branch_office_id'=>'required',
            'file_id'=>'required',
            'description'=>'required',


        ]);
      return BranchOfficeFile::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BranchOfficeFile  $branchOfficeFile
     * @return \Illuminate\Http\Response
     */
    public function show(BranchOfficeFile $branchOfficeFile)
    {
        return $branchOfficeFile;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BranchOfficeFile  $branchOfficeFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BranchOfficeFile $branchOfficeFile)
    {
        $request->validate([
            'branch_office_id'=>'required',
            'file_id'=>'required',
            'description'=>'required',


        ]);
       $branchOfficeFile->update($request->all());
       return $branchOfficeFile;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BranchOfficeFile  $branchOfficeFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchOfficeFile $branchOfficeFile)
    {
        $branchOfficeFile->delete();
    }
}
