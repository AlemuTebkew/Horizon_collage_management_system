<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
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
      return Employee::create($request->all());
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
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'phone_no'=>'required',
            'role'=>'required',

        ]);
        $employee->update($request->all());
        return $employee;
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



}
