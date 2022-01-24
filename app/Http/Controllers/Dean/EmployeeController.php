<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
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


        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:employees',
            'phone_no'=>'required|unique:employees',
            'role'=>'required',

        ]);

        DB::beginTransaction();
        try{

        $login=new UserLogin();
        $login->user_name=$request->email;
        $login->password=Hash::make($request->last_name.'1234');
        $login->user_type='employee';
        $login->save();

        $em=new Employee();
        $em->first_name=$request->first_name;
        $em->last_name=$request->last_name;
        $em->email=$request->email;
        $em->phone_no=$request->phone_no;
        $em->role=$request->role;
        $em->save();
     // $employee= Employee::create($request->all());

      DB::commit();
      return response()->json($em,201);
  } catch (\Throwable $th) {
      DB::rollBack();
     return response()->json('error while creating',501);
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
            $user_login->password=Hash::make($request->last_name.'1234') ;
            $user_login->save();
            $employee->update($request->all());

            DB::commit();
            return response()->json($employee,200);

        } catch (\Throwable $th) {
            DB::rollBack();
           return response()->json('error while updating',501);
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

        DB::beginTransaction();
        try {
            UserLogin::where('user_name',$employee->email)->first()->delete();
            $employee->delete();
            DB::commit();
            return response()->json('Successfully deleted',200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json('error while saving teacher',501);
        }

    }

    public function getUnAssignedDepartmentHeads(){
        return Employee::where('role','department_head')->get();
    }


    public function getDepartmentHeads(){
        return Employee::where('role','department_head')
                         ->orWhere('role','degree_head')
                         ->orWhere('role','tvet_head')
                         ->get();
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

    public function get_notifications($id){


        $new= Employee:: find($id)->unReadNotifications;
        return $new;
     }


      public function mark_as_read($id){
         $new= Employee::find($id)->unReadNotifications()->delete();
         //$new->markAsRead();
         return response()->json([],200);
      }
}
