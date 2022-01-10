<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return Employee::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:employees',
            'phone_no'=>'required',
            'role'=>'required',

        ]);
        $login=new UserLogin();
        $login->user_name=$request->email;
        $login->password=Hash::make($request->last_name.'1234');
        $login->user_type='employee';
        $login->save();
      $employee= Employee::create($request->all());

      DB::commit();
      return response()->json($employee,201);
  } catch (\Throwable $th) {
      DB::rollBack();
     return response()->json('error while creating',$th->getCode());
    }
 }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required',
                'phone_no'=>'required',
                'role'=>'required',

            ]);
            $user_login=UserLogin::where('user_name',$employee->email)->first();
            $user_login->user_name=$request->email;
            $user_login->password=$request->last_name.'1234' ;
            $user_login->save();
            $employee->update($request->all());

            DB::commit();
            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();
           return response()->json('error while updating',$th->getCode());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {

        if( $employee->delete()) {
            return $this->successResponse('successfully Deleted ',200);
        }
        else{
            return $this->errorResponse('fail to Delete',501);
        }


    }

    public function getDepartmentHeads(){
        return Employee::where('role','department head')->get();
    }
    public function getCashiers(){
        return Employee::where('role','cashier')->get();
    }
    public function getRegistrars(){
        return Employee::where('role','registrar')->get();
    }

    public function getDeans(){
        return Employee::where('role','dean')->get();
    }


}
