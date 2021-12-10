<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeStudentResource;
use App\Models\AcademicYear;
use App\Models\Address;
use App\Models\DegreeStudent;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class DegreeStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //getting all students
        return DegreeStudent::with('degree_department','program')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

   //  return $request->month_ids;
        DB::beginTransaction();
        try {

            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'sex'=>'required',
                'dob'=>'required',
                'phone_no'=>'required',
                'martial_status'=>'required',
                'emergency_contact_name'=>'required',

            ]);

            $academic_year=AcademicYear::where('status',1)->first();
            $birth_address=Address::create($request->birth_address);
            $residential_address=Address::create($request->residential_address);
            $emergency_address=Address::create($request->emergency_address);

            $data=$request->all();
            $data['birth_address_id']=$birth_address->id;
            $data['residential_address_id']=$residential_address->id;
            $data['emergency_address_id']=$emergency_address->id;
            $data['password']=Hash::make('HR'.$request->last_name);
            $data['batch']=$academic_year->year;
            $data['dob']=date('Y-m-d',strtotime($request->dob));

             $semester=Semester::find($request->semester_id);
            $student= DegreeStudent::create($data);
            $student->semesters()->attach($request->semester_id,
            [
                'year_no'=>$request->year_no,
                'semester_no'=>$request->semester_no,
                'tution_type'=>$request->tution_type,
                'scholarship'=>$request->scholarship

            ]);
          if ($request->tution_type == 'cp') {
            $semester->student_payments()->attach($student->id,
            [
              'academic_fee_id'=>$request->academic_fee_id1,
              'receipt_no'=>$request->receipt_no,
              'paid_date'=>now()->toDateTime(),
              'is_paid'=>1
            ]);

           $semester->student_payments->forget($student->id);
            $semester->student_payments()->attach($student->id,
            [
              'academic_fee_id'=>$request->academic_fee_id2,
              'receipt_no'=>$request->receipt_no,
              'paid_date'=>now()->toDateTime(),
              'is_paid'=>1
            ]);


          }elseif ($request->tution_type == 'monthly') {

            $semester->student_payments()->attach($student->id,
            [
              'academic_fee_id'=>$request->academic_fee_id2,
              'receipt_no'=>$request->receipt_no,
              'paid_date'=>now()->toDateTime(),
              'is_paid'=>1

          ]);

            foreach ($request->month_ids as $id) {

                $student->month_payments()->attach($id,[
                    'academic_fee_id'=>$request->academic_fee_id1,
                    'academic_year_id'=>$academic_year->id,
                    'receipt_no'=>$request->receipt_no,
                    'paid_date'=>now()->toDateTime(),
                    'is_paid'=>1

                ]);
            }
          }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            return response()->json(['can t create student']);
        }


       return $student->load('semesters');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function show(DegreeStudent $degreeStudent)
    {
        return new DegreeStudentResource($degreeStudent->load('semesters'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DegreeStudent $degreeStudent)
    {
        $request->validate([
            'student_id'=>'required',
            'first_name'=>'required',
            'last_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'martial_status'=>'required',
            'emergency_contact_name'=>'required',

        ]);
       $degreeStudent->update($request->all());
       return $degreeStudent;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DegreeStudent  $degreeStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(DegreeStudent $degreeStudent)
    {

        DB::beginTransaction();

        try {
            $degreeStudent->birth_address()->delete();
            $degreeStudent->contact_address()->delete();
            $degreeStudent->residential_address()->delete();
            $degreeStudent->delete();

            DB::commit();
            return response()->json(['succesfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e]);

        }
    }

    public function registerStudentForSemester(Request $request){
      $student=DegreeStudent::find($request->student_id);
    //   $semester=Semester::find($request->semester_id);
      $student->semesters()->attach($request->semester_id,
      [
          'year_no'=>$request->year_no,
          'semester_no'=>$request->semester_no,
          'tution_type'=>$request->tution_type,
          'scholarship'=>$request->scholarship
      ]);
    }

    public function getStudentCourses(){

    }
}
