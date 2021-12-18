<?php

namespace App\Http\Controllers\Dean;
use  App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Semester;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AcademicYear::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //year','start_date','end_date','status','is_current
        $request->validate([
            'year'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
       $ay=AcademicYear::where('status',1)->first();
       if ($ay) {
           return response()->json(['close active Academic year before creating']);
       }
       $data=$request->all();
       $data['start_date']=date('Y-m-d',strtotime($request->start_date));
       $data['end_date']=date('Y-m-d',strtotime($request->end_date));

       
      return AcademicYear::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
        return $academicYear;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'year'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',

        ]);
        $data=$request->all();
        $data['start_date']=date('Y-m-d',strtotime($request->start_date));
        $data['end_date']=date('Y-m-d',strtotime($request->end_date));
      return $academicYear->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
    }

    public function closeAcademicYear(AcademicYear $academicYear){
      $academicYear->update(['status'=>0,'is_current'=>0]);
    }
    public function getAllAcademicYear($departmentHeadId){
        $current_academic_year=AcademicYear::where('status',1);
        $current_semester=Semester::where('status',1);

      return  $department_head=Employee::find($departmentHeadId);
        $department=$department_head->manage();
        $academic_years=AcademicYear::all();
        $all_academic_year=[];
        foreach ( $academic_years as  $academic_year) { 
          $all_academic_year[$academic_year->year]=$academic_year->semesters;
        }
        $course=Course::where('department_id',$department->id);
        $section=DegreeSection::where('department_id',$department->id)
                                ->orwhere('acadmic_year_id',$current_academic_year->id)
                                ->orWhere('semester_id',$current_semester->id)->get();
        $students= DegreeStudent::where('department_id',$department->id)->with('degree_department','program')->get();
        foreach ($students as $student) {
           return $student->courses;
        }
    }
}
