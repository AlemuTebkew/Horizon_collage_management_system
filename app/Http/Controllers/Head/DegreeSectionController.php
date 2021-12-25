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
use App\Models\Teacher;
use Illuminate\Http\Request;

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
    //   return $employee;
        $data['degree_department_id']=$employee->manage->id;
        $ds= DegreeSection::create($data);
        return new DegreeSectionResource($ds->load('degree_department','semester'));
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
     // return $employee;
        $data['degree_department_id']=$employee->manage->id;
        $degreeSection->update($data);
        return new DegreeSectionResource($degreeSection->load('degree_department','semester'));
     // return $degreeSection->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeSection $degreeSection)
    {
        $degreeSection->delete();
    }

    public function getSectionStudents($section_id){
        $ds=DegreeSection::find($section_id);
        return DegreeSectionStudentResource::collection($ds->degree_students);
    }

    public function addStudentsToSection(){
        $sec= DegreeSection::find(request('section_id'));
        foreach (request('student_ids')  as  $student_id) {
           $sec->degree_students()->attach($student_id);
        }
   
        return DegreeSectionStudentResource::collection($sec->degree_students);

    }




}
