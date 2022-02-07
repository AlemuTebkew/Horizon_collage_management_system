<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use App\Models\Program;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class DegreeStudentDetail extends Controller
{
    public function getStudentInfoToEdit($id){

        $student=DegreeStudent::findOrFail($id);
       $is_approved= $student->semesters()->wherePivot('status','approved')->first();
        if ($is_approved) {
           $student->status='approved';
        }else {
            $student->status='waiting';
        }
        return response()->json($student->load(['birth_address','emergency_address','residential_address','degree_department','program']),200);
    }


    public function updateStudentPersonalInfo(Request $request, $id){

        DB::beginTransaction();
        try {

            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'sex'=>'required',
                'phone_no'=>'required',
                'dob'=>'required',

            ]);

            $degreeStudent=DegreeStudent::withTrashed()->findOrFail($id);

            $academic_year_id=null;
            if (request()->filled('academic_year_id')) {
                $academic_year_id=request('academic_year_id');
            }else{
                $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
            }

           $academic_year=AcademicYear::find($academic_year_id);

            $degreeStudent->birth_address()->update($request->birth_address);
            $degreeStudent->residential_address()->update($request->residential_address);
            $degreeStudent->emergency_address()->update($request->emergency_address);

            $data=$request->all();
            $data['dob']=date('Y-m-d',strtotime($request->dob));

            $degreeStudent->update($data);

            //////////start user login info
            $login= UserLogin::where('user_name',$degreeStudent->student_id)->first();
            if (!$login) {
                $login=new UserLogin();
            }
            $login->user_name=$degreeStudent->student_id;
            $login->password=Hash::make($request->last_name.'1234');
            $login->user_type='degree_student';
            $login->save();
            ///////////////////////end user login info


            DB::commit();
            return response()->json('succssfullly saved',200);


        } catch (\Exception $e) {
            DB::rollBack();
         //  return $e;
            return response()->json(['can t create student'.$e],400);
        }
 }

  public function updateStudentEducationalInfo($id){

    $degreeStudent=DegreeStudent::withTrashed()->findOrFail($id);

    $degreeStudent->update(request()->all());

    return response()->json('succesfully updated',200);

  }


  public function updateStudentAdmissionInfo($id){

    $degreeStudent=DegreeStudent::withTrashed()->findOrFail($id);

    $degreeStudent->update(request()->all());

    //update Student_id
    $id=$this->generateStudentId($degreeStudent);
    $degreeStudent->update(['student_id',$id]);
    return response()->json('succesfully updated',200);

  }
  public function updateStudentfinanceInfo($id){
    $degreeStudent=DegreeStudent::withTrashed()->findOrFail($id);
    $degreeStudent->update(request()->all());
    return response()->json('succesfully updated',200);
  }

  public function generateStudentId($student){
              //    //  ---------start student ID generation

              $academic_year_id=null;
              if (request()->filled('academic_year_id')) {
                  $academic_year_id=request('academic_year_id');
              }else{
                  $academic_year_id=AcademicYear::where('is_current',1)->first()->id;
              }

             $academic_year=AcademicYear::find($academic_year_id);


            $dep=DegreeDepartment::find($student->degree_department_id);
            $program=Program::find($student->program_id)->name;

            $p_short=null;
            if (str_starts_with($program,'R') || str_starts_with($program,'r')) {
                $p_short='R';
            }elseif (str_starts_with($program,'E') || str_starts_with($program,'e')) {
                $p_short='E';
            }

           $year_short= substr($academic_year->year,2);
            $no_of_st= $dep->degree_students()->withTrashed()
                                           ->where('batch',$academic_year->year)->count()+1;

            if ($no_of_st <= 9) {
                $student_id='HC'.$p_short.$dep->short_name.'00'.$no_of_st.'/'.$year_short;

            }elseif ($no_of_st <= 99) {
                $student_id='HC'.$p_short.$dep->short_name.'0'.$no_of_st.'/'.$year_short;

            }elseif ($no_of_st <= 999) {
                $student_id='HC'.$p_short.$dep->short_name.$no_of_st.'/'.$year_short;

            }
        //---------end student id generation
            return $student_id;
  }

}
