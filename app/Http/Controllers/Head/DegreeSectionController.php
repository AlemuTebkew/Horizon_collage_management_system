<?php

namespace App\Http\Controllers\Head;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
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
        return DegreeSection::with('degree_department','academic_year','semester')->get();
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
        $data['degree_department_id']=$request->user()->manage->id;
        $ds= DegreeSection::create($data);
        return $ds->load('degree_department','academic_year','semester');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeSection  $degreeSection
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeSection $degreeSection)
    {
        return $degreeSection->load('degree_department','academic_year','semester');
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
            'degree_department_id'=>'required',
            'academic_year_id'=>'required',
            'semester_id'=>'required',

        ]);
      return $degreeSection->update($request->all());
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

    public function getSectionStudents(){
        $ds=DegreeSection::find(request('section_id'));
        return  $ds->degree_students;
    }

    public function AddStudentsToSection(){
        $sec= DegreeSection::find(request('section_id'));
        $sec->degree_students->attach(request('students_id'));
        return $sec->load('degree_students');

    }
    public function getSectionCourses(){
        $ds=DegreeSection::find(request('section_id'));
        $courses=Course::where('degree_department_id',request('degree_department_id'))
                        ->where('program_id',request('program_id'))
                        ->where('year_no',request('year_no'))
                        ->where('semester_no',request('semester_no'))->get();

        return $courses;
    }

    public function assignTeacherForCourse(){
        $ds=DegreeSection::find(request('section_id'));
        $course=Course::find(request('course_id'));
        $teacher=Teacher::find(request('teacher_id'));
        $teacher->coureses()->attach($course->id,[
            'degree_section_id'=>$ds->id,
        ]);

        return $course->load('teachers');
    }
    
}
