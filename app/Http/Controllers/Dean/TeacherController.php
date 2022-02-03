<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Teacher::all();
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
            'email'=>'required|unique:teachers',
            'phone_no'=>'required|unique:teachers',
            'type'=>'required',
            'qualification'=>'required',
            'gpa'=>'required',


        ]);

        DB::beginTransaction();

        try {

            $login=new UserLogin();
            $login->user_name=$request->email;
            $login->password=Hash::make($request->last_name.'1234');
            $login->user_type='teacher';
            $login->save();

          $teacher= Teacher::create($request->all());
            DB::commit();
          return response()->json($teacher,201);

        } catch (\Throwable $th) {
          DB::rollBack();
          return response()->json('error while saving teacher',501);

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        return $teacher;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'phone_no'=>'required',
            'type'=>'required',
            'qualification'=>'required',
            'gpa'=>'required',

        ]);


        DB::beginTransaction();

        try {

            $login= UserLogin::where('user_name',$teacher->email)->first();
            $login->user_name=$request->email;
            $login->password=Hash::make($request->last_name.'1234');
            $login->user_type='teacher';
            $login->save();
            $teacher->update($request->all());
            DB::commit();
          return response()->json($teacher,200);

        } catch (\Throwable $th) {
          DB::rollBack();
          return response()->json('error while saving teacher',501);

        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        DB::beginTransaction();
        try {
            UserLogin::where('user_name',$teacher->email)->first()->delete();
            $teacher->delete();
            DB::commit();
            return response()->json('Successfully deleted',200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json('error while saving teacher',501);
        }

    }
    public function getActiveTeacher()
    {
        $teachers=Teacher::where('status',1)->get();
        return  $teachers->makeHidden('type','qualification','status','gpa');
    }
}
