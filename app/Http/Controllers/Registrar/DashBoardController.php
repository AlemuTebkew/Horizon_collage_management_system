<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Coc;
use App\Models\DegreeStudent;
use App\Models\Teacher;
use App\Models\TvetStudent;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    /**
     * Display a data for dashboard of registrar
     *not included paiad and unpaid student becouse of calander
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $dashboard_data=[];
            $dashboard_data['total_tvet_student']=TvetStudent::all()->count();
            $dashboard_data['total_degree_student']=DegreeStudent::all()->count();
            $current_academic_year=AcademicYear::where('is_current',1)->first();
           // return $current_academic_year->year;
           $dashboard_data['new_degree_student']=DegreeStudent::where('batch',$current_academic_year->year)->count();
           $dashboard_data['total_coc']=Coc::count();
           $dashboard_data['new_tvet_student']=TvetStudent::where('batch',$current_academic_year->year)->count();
           $dashboard_data['tvet_scholarship_students']=TvetStudent::where('fully_scholarship',1)->paginate(4);
           $dashboard_data['degree_scholarship_students']=DegreeStudent::where('fully_scholarship',1)->paginate(4);
           $dashboard_data['regular_teachers']=Teacher::where('type','regular')->paginate(5);
           $dashboard_data['contract_teachers']=Teacher::where('type','contract')->paginate(5);

            return response()->json(['students'=>  $dashboard_data]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
