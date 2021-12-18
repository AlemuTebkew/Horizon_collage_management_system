<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Program::with('semesters')->get();
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
            'name'=>'required',
          //  'types.type'=>'required',

        ]);

      $name=$request->name;
        foreach ($request->types as $type) {
           // return $a=$type['type'];
        //    return $type['semisters_in_year'];
             $program=new Program();
             $program->name=$name;
             $program->type=$type['type'];
             if ( array_key_exists('semesters_in_year',$type)) {
               $program->semisters_in_year=$type['semesters_in_year'];

             }
             $program->save();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        return $program;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name'=>'required',
            'type'=>'required',
        ]);
       $program->update($request->all());
       return $program;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Program  $program
     * @return \Illuminate\Http\Response
     */
    public function destroy(Program $program)
    {
        $program->delete();
    }

    public function getDegreeProgram(){
      return response()->json(Program::where('type','degree')->get(),200);
    }

    public function getTvetProgram(){
        return response()->json(Program::where('type','tvet')->get(),200);

    }
}
