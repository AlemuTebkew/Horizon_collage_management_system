<?php

namespace App\Http\Controllers;

use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\TvetStudent;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Account extends Controller
{
    public function login(Request $request){

/*
        $credentials['email']=$request->email;
        $credentials['password']=$request->password;
        if(!Auth::attempt($credentials)){
            return response()->json(
                [
                'message'=>'un authenticated',
                ]
               ,404 );
        }
        $user=UserLogin::where('email',$credentials['email'])
        ->where('is_active',1)->first();
        if(!$user){
         return response()->json([
             'message'=>' unauthorized',
             ]
            ,404 );

         }
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token'=>$token,
            'user'=>$user,
        ],200);

*/
        $request->validate([

            'user_name'=>'required',
            'password'=>'required',

        ]);
        $user=UserLogin::where('user_name',$request->user_name)->first();
        if (! $user ) {
            return response()->json([
                'message'=>' incorrect user_name and password',
                ]
               ,404 );
        }
        $check=Hash::check($request->password, $user->password);
        if (! $check ) {
            return response()->json([
                'message'=>' incorrect password',
                ]
               ,404 );
        }

        if ($user->user_type == 'teacher') {

            $teacher=Teacher::where('email',$user->user_name)->first();

            $token= $teacher->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'user'=>$teacher,
            ],200);

         }elseif ($user->user_type == 'degree_student') {
            $degree_student=DegreeStudent::where('student_id',$user->user_name)->first();

            $token= $degree_student->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'user'=>$degree_student,
            ],200);

         } elseif ($user->user_type == 'tvet_student') {
            $tvet_student=TvetStudent::where('student_id',$user->user_name)->first();

            $token= $tvet_student->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'user'=>$tvet_student,
            ],200);

         } elseif ($user->user_type == 'employee') {
           //  return $user->user_type;
            $employee=Employee::where('email',$user->user_name)->first();

            if ($employee->role == 'department head') {
                return true;
                if ($employee->manage) {
                    $token= $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'access_token'=>$token,
                        'user'=>$employee->load('manage'),
                    ],200);
                }else {
                    return response()->json(['Un Authorized'],401);
                }
            }else{
            $token= $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'user'=>$employee,
            ],200);
        }
         }


     }
     public function logout(Request $request){
     //  return  $request->user();
         $request->user()->currentAccessToken()->delete();
         return response()->json([
             'message'=>$request->user(),
         ],200);

     }

    public function changePassword(Request $request){

        $user=UserLogin::where('user_name',$request->user_name);
        if (! $user ) {
            return response()->json([
                'message'=>' incorrect user_name and password',
                ]
               ,404 );
        }

        $check=Hash::check($request->old_password, $user->password);
        if (! $check ) {
            return response()->json([
                'message'=>' incorrect password',
                ]
               ,404 );
        }

        $user->password=$request->new_password;
        $user->save();
    }

}
