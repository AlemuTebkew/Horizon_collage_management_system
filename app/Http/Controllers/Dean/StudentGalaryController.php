<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\StudentGalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentGalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page=request()->filled('per_page') ? request('per_page') : 10;
        return response()->json(StudentGalary::orderByDesc('id')->paginate($per_page),200);

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
            'image'=>'required|image',


        ]);

    if ($request->hasFile('image')) {

            $file=$request->file('image');
            $name = time().'.'.$file->extension();
            $file->move(public_path().'/images/student_images', $name);
            $image=new StudentGalary();
            $image->url=$name;
         $i=   $image->save();

     }
        if ($i) {
            // $image->url=asset('images/student_images/'.$image->url);
            return response()->json($image,201);
        }else {
            return response()->json('not added',400);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StudentGalary  $studentGalary
     * @return \Illuminate\Http\Response
     */
    public function show(StudentGalary $studentGalary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentGalary  $studentGalary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentGalary $studentGalary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudentGalary  $studentGalary
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $studentGalary= StudentGalary::find($id);
        $path= public_path('/images/student_images/');
        if($studentGalary->url && file_exists($path.$studentGalary->url)){
            Storage::delete($path.$studentGalary->url);
           // unlink($path.$category->image);
        }
       return $studentGalary->delete();
    }
}
