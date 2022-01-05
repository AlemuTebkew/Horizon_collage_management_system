<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;

use App\Models\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(AboutUs::first(),200);
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
            'short_description'=>'required',
            'more_description'=>'required',
            'vision'=>'required',
            'mission'=>'required',
            'values'=>'required',
            'educational_goal'=>'required',
        ]);
      return response()->json(AboutUs::create($request->all,200));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AboutUs  $aboutUs
     * @return \Illuminate\Http\Response
     */
    public function show(AboutUs $aboutUs)
    {
        return $aboutUs;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AboutUs  $aboutUs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AboutUs $aboutUs)
    {
        $request->validate([
            'short_description'=>'required',
            'more_description'=>'required',
            'vision'=>'required',
            'mission'=>'required',
            'values'=>'required',
            'educational_goal'=>'required',
        ]);
        $aboutUs->update($request->all);
      return response()->json($aboutUs,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AboutUs  $aboutUs
     * @return \Illuminate\Http\Response
     */
    public function destroy(AboutUs $aboutUs)
    {
        $aboutUs->delete();
    }
}
