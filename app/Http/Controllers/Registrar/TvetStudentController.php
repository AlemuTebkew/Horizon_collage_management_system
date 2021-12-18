<?php

namespace App\Http\Controllers\Registrar;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentLevelResource;
use App\Models\AcademicYear;
use App\Models\Address;
use App\Models\FeeType;
use App\Models\Level;
use App\Models\Month;
use App\Models\TvetStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TvetStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TvetStudent::with('tvet_department','program','month_payments')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public $id=0;
    public function store(Request $request)
    {

        // DB::beginTransaction();

        // try {

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'middle_name'=>'required',
            'sex'=>'required',
            'dob'=>'required',
            'phone_no'=>'required',
            'maritial_status'=>'required',
            'emergency_contact_name'=>'required',
        ]);

        $academic_year=AcademicYear::where('status',1)->first();
        $month_id= $academic_year->months()->select('months.id')->get()->makeHidden('pivot');
        $level=Level::find($request->level_id);
        $birth_address=Address::create($request->birth_address);
        $residential_address=Address::create($request->residential_address);
        $emergency_address=Address::create($request->emergency_address);

        $data=$request->all();
        $data['birth_address_id']=$birth_address->id;
        $data['residential_address_id']=$residential_address->id;
        $data['emergency_address_id']=$emergency_address->id;
        $data['password']=Hash::make('HR'.$request->last_name);
        $data['batch']=$academic_year->year;
        $data['current_level_no']=$level->level_no;
        $data['dob']=date('Y-m-d',strtotime($request->dob));



        $student= TvetStudent::create($data);
        $student->levels()->attach($level->id,
        [

            'academic_year_id'=>$academic_year->id,
            'partial_scholarship'=>$request->partial_scholarship

        ]);

        $reg_fee_id= FeeType::where('name','Registration Fee')->first()->id;
        $monthly_fee_id= FeeType::where('name','Monthly Fee')->first()->id;


      // return $month_id;
        foreach ($month_id as $id) {

            $student->month_payments()->attach($id,[
                'academic_year_id'=>$academic_year->id,

            ]);
        }

        foreach ($request->tuition_months as $id) {

            $student->month_payments()->updateExistingPivot($id,[
                'fee_type_id'=>$monthly_fee_id,
                'academic_year_id'=>$academic_year->id,
                'receipt_no'=>$request->receipt_no,
                'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
                'paid_amount'=>$request->tuition_fee,
                'is_paid'=>1

            ]);
          //  $student->month_payments->forget($id);

        }
       // $student->month_payments->forget($this->id);

        $student->tvet_other_fees()->attach($reg_fee_id,[
            'academic_year_id'=>$academic_year->id,
            'receipt_no'=>$request->receipt_no,
            'paid_date'=>date('Y-m-d',strtotime($request->paid_date)),
            'paid_amount'=>$request->registration_fee,
            'is_paid'=>1

        ]);
        return $student->load('month_payments');
    //     DB::commit();
    //     return $student->load('levels');
    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return $e;
    //     return response()->json(['can t create student']);
    // }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function show(TvetStudent $tvetStudent)
    {
       return $tvetStudent->load('month_payments');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TvetStudent $tvetStudent)
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
       $tvetStudent->update($request->all());
       return $tvetStudent;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TvetStudent  $tvetStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(TvetStudent $tvetStudent)
    {
        DB::beginTransaction();

        try {
            $tvetStudent->birth_address()->delete();
            $tvetStudent->contact_address()->delete();
            $tvetStudent->residential_address()->delete();
            $tvetStudent->delete();

            DB::commit();
            return response()->json(['succesfully deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['not succesfully deleted'.$e],500);

        }
    }


    public function registerStudentForLevel(Request $request){

        $level=Level::find($request->level_id);
        $student= TvetStudent::find($request->student_id);
        $student->levels()->attach($request->level_id,
        [

            'academic_year_id'=>$request->academic_year_id,
            'scholarship'=>$request->scholarship

        ]);

      }

      public function getStudentLevels( $tvetStudent_id){
        $tvetStudent= TvetStudent::find($tvetStudent_id);
        return new StudentLevelResource($tvetStudent->load('levels'));
    }
}
