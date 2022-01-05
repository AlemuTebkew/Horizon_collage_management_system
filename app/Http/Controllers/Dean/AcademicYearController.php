<?php

namespace App\Http\Controllers\Dean;
use  App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\FeeType;
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
        return AcademicYear::with('semesters.months')->get();
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


      $fee_type_id=FeeType::pluck('id');
      $year= AcademicYear::create($data);
        if ($year) {
            $year->fee_types()->attach($fee_type_id);
        }

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


    public function getAcademicFee(){
        $academic_year=null;
        if (request('academic_year_id')) {
            $academic_year=AcademicYear::find(request('academic_year_id'));

        }else {
            $academic_year=AcademicYear::where('is_current',1)->first();
        }

            $all=[];

           foreach ($academic_year->fee_types as $fee_type) {
            $fee=[];
            $fee['id']=$fee_type->id;
            $fee['name']=$fee_type->name;
            $fee['amount']=$fee_type->pivot->amount;
            $all[]=$fee;
        }
     return response()->json($all,200);
    }



    public function setAcademicFee($year_id){

     $year=AcademicYear::find($year_id);

     $year->fee_types()->updateExistingPivot(request('id'),[
         'amount'=>request('amount'),
     ]);

    return response()->json(['message'=>'succesfully set'],200);

    }

    public function closeAcademicYear(AcademicYear $academicYear){
      $academicYear->update(['status'=>0,'is_current'=>0]);
    }
    public function getAllAcademicYear($departmentHeadId){
        $current_academic_year=AcademicYear::where('is_current',1);
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
