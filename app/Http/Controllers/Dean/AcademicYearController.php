<?php

namespace App\Http\Controllers\Dean;
use  App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\DegreeSection;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\FeeType;
use App\Models\Month;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


        ]);

        try {
            DB::beginTransaction();

            $ay=AcademicYear::where('is_current',1)->first();

          $academic_year=new AcademicYear();
          $academic_year->year=$request->year;
          $academic_year->is_current=1;
          $academic_year->is_closed=0;
          $academic_year->save();

          $fee_type_ids=FeeType::pluck('id');
          $month_ids=Month::pluck('id');

          //attach months to academic year
   //       $academic_year->months()->attach($month_ids);
          //end

          //create academic fee
          if ($ay) {
            $old_fees=$ay->fee_types;
            foreach ($old_fees as $old_fee) {
                $academic_year->fee_types()->attach($old_fee->id,['amount'=>$old_fee->pivot->amount]);
             }

             $ay->is_current=0;
             $ay->save();
          }else {
            $academic_year->fee_types()->attach($fee_type_ids,['amount'=>0.0]);

          }


          $reg_id=Program::where('name','regular')->where('type','degree')->first()->id;
          $ext_id=Program::where('name','extension')->where('type','degree')->first()->id;

          //creating regular semesters
          foreach ($request->regular as  $semester ) {

             $new_s=new Semester();
             $new_s->start_date=date('Y-m-d',strtotime($semester['start_date']));
             $new_s->end_date=date('Y-m-d',strtotime($semester['end_date']));
             $new_s->number=$semester['number'];
             $new_s->is_closed=0;
             $new_s->program_id=$reg_id;
             $new_s->academic_year_id=$academic_year->id;

             if ($semester['number'] == 1) {
               $new_s->is_current=1;
             }else{
              $new_s->is_current=0;
             }
             $new_s->save();

             //attaching semester months
             $month_ids=$semester['months'];
             $new_s->months()->attach($month_ids);
             //end

             $activities=$semester['activities'];

             foreach ($activities as $activity) {

                DB::table('degree_calender_activities')->insert([
                     'date'=>$activity['date'],
                     'semester_id'=>$new_s->id,
                     'activity'=>$activity['task']
                 ]);
             }



          }

          //creating extension semesters
          foreach ($request->extension as $semester) {

             $new_s=new Semester();
             $new_s->start_date=date('Y-m-d',strtotime($semester['start_date']));
             $new_s->end_date=date('Y-m-d',strtotime($semester['end_date']));
             $new_s->number=$semester['number'];
             $new_s->is_closed=0;
             $new_s->program_id=$ext_id;
             $new_s->academic_year_id=$academic_year->id;

             if ($semester['number'] == 1) {
               $new_s->is_current=1;
             }else{
              $new_s->is_current=0;
             }
             $new_s->save();

             $month_ids=$semester['months'];
             $new_s->months()->attach($month_ids);
             //end

             $activities=$semester['activities'];

             foreach ($activities as $activity) {

                 DB::table('degree_calender_activities')->insert([
                    'date'=>$activity['date'],
                    'activity'=>$activity['task'],
                     'semester_id'=>$new_s->id,
                 ]);

          }
        }

          //creating tvet calender
          foreach ($request->tvet_activities as $activity) {
            DB::table('tvet_calender_activities')->insert([
                'date'=>$activity['date'],
                'activity'=>$activity['task'],
                'academic_year_id'=> $academic_year->id ,
             ]);
          }


         DB::table('tvet_calenders')->insert([
            'start_date'=>date('Y-m-d',strtotime($request->tvet_start_date)),
            'end_date'=> date('Y-m-d',strtotime($request->tvet_end_date)) ,
            'academic_year_id'=> $academic_year->id ,
        ]);

            DB::commit();
            return response()->json(['Succesfully saved'],201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error'=>'data can not be saved'.$th],501);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academic_year)
    {


        $reg_id=Program::where('name','Regular')->where('type','degree')->first()->id;
        $ext_id=Program::where('name','Extension')->where('type','degree')->first()->id;

        foreach($academic_year->semesters as $semester){

            if($semester->program_id == $reg_id){
                $regular[]=$semester;
                $regulara[]=$semester->load('degree_calender_activities')->select('id');
                $regularm[]=$semester->load('months:id,name');
            }else if($semester->program_id == $ext_id){
                $ext[]=$semester;
                $exta[]=$semester->load('degree_calender_activities');
               $extm[]=$semester->load('months:id,name');

            }

        }
        $act['year'] =  $academic_year->year;
        $act['regulars']=$regular;
        $act['extensions']=$ext;
        $act['regularm']=$regularm;
        $act['extensionm']=$extm;
        $act['regularsa']=$regulara;
        $act['extensionsa']=$exta;

        return response()->json($act,200);

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


    public function getAcademicCalenderActivities(){
        $academic_year=null;
        if (request('academic_year_id')) {
           $academic_year=AcademicYear::with('semesters.degree_calender_activities')->find(request('academic_year_id'));

        }else {

            $academic_year=AcademicYear::with('semesters.degree_calender_activities')->where('is_current',1)->first();
        }

        $reg_id=Program::where('name','regular')->where('type','degree')->first()->id;
        $ext_id=Program::where('name','extension')->where('type','degree')->first()->id;

        foreach($academic_year->semesters as $semester){

            if($semester->program_id == $reg_id){
                $regular[]=$semester;
            }else if($semester->program_id == $ext_id){
                $ext[]=$semester;

            }

        }
        $act['regular']=$regular;
        $act['extension']=$ext;

        $de['degree'] =  $act;
        $de['year'] =  $academic_year->year;

        $de['tvet']= $academic_year->tvet_calender_activities;

        return response()->json($de,200);

    }

    public function editAcademicYearSemester(){

        $academic_year=null;
        if (request('academic_year_id')) {
            $academic_year=AcademicYear::find(request('academic_year_id'));

        }else {
            $academic_year=AcademicYear::where('is_current',1)->first();
        }

        $reg_id=Program::where('name','Regular')->where('type','degree')->first()->id;
        $ext_id=Program::where('name','Extension')->where('type','degree')->first()->id;

        foreach($academic_year->semesters as $semester){

            if($semester->program_id == $reg_id){
                $regular[]=$semester->load('months','degree_calender_activities');

            }else if($semester->program_id == $ext_id){

                $ext[]=$semester->load('months','degree_calender_activities');


            }

        }
        $act['year'] =  $academic_year->year;

        $act['regular']=$regular;
        $act['extension']=$ext;

        return response()->json($act,200);

    }

    public function closeAcademicYear(AcademicYear $academicYear){
      $academicYear->update(['is_closed'=>1,'is_current'=>0]);
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
