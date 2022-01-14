<?php

namespace App\Http\Controllers\Head;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Head\DegreeSectionResource;
use App\Http\Resources\Head\DegreeSectionStudentResource;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DegreeSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $academic_year_id=null;
        if (request()->has('academic_year_id')) {
            $academic_year_id=request('academic_year_id');
        }else{
        $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
        }

        return DegreeSectionResource::collection(DegreeSection::with('degree_department','semester')
              ->where('academic_year_id',$academic_year_id)->get());
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
            'year_no'=>'required',
            'academic_year_id'=>'required',
            'semester_id'=>'required',

        ]);
        $data=$request->all();
        $employee=Employee::where('email',$request->user()->user_name)->first();

       $data['degree_department_id']=$employee->manage->id;
      $data['semester_no']=Semester::find($request->semester_id)->number;
      $s=DegreeSection::where('name',$request->name)->where('year_no',$request->year_no)
                             ->where('semester_id',$request->semester_id)
                             ->where('degree_department_id',$employee->manage->id)
                             ->where('academic_year_id',$request->academic_year_id)
                             ->where('program_id',$request->program_id)
                             ->first();
      if (!$s) {
        $ds= DegreeSection::create($data);
        return response()->json(new DegreeSectionResource($ds->load('degree_department','semester')),200) ;
      }else {
          return response()->json(['error_message'=>'Section Already Added'],400);
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeSection $degreeSection)
    {
        return new DegreeSectionResource($degreeSection->load('degree_department','semester'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeSection $degreeSection)
    {
        $request->validate([
            'name'=>'required',
            'year_no'=>'required',
            'academic_year_id'=>'required',
            'semester_id'=>'required',

        ]);
        //'email',$request->user()->user_name)->first()
        $data=$request->all();
        $employee=Employee::where('email',$request->user()->user_name)->first();
     // $employee->manage->id;
        $data['degree_department_id']=$employee->manage->id;
        $data['semester_no']=Semester::find($request->semester_id)->number;
        $s=DegreeSection::where('name',$request->name)
                                ->where('year_no',$request->year_no)
                               ->where('semester_id',$request->semester_id)
                               ->where('degree_department_id',$employee->manage->id)
                               ->where('academic_year_id',$request->academic_year_id)
                               ->where('program_id',$request->program_id)
                               ->first();
        if (!$s) {
            $degreeSection->update($data);
            return response()->json(new DegreeSectionResource($degreeSection->load('degree_department','semester')),200) ;
        }else {
            return response()->json(['error_message'=>'Section Already Added'],400);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeSection $degreeSection)
    {

        DB::beginTransaction();
        try {

            if ($degreeSection->degree_students->isEmpty()) {
                $degreeSection->delete();
            }else {
                return response()->json(['You can not delete'],500);

            }
            DB::commit();
            return response()->json(['succesfully deleted'],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }





}
