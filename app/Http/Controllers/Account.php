<?php

namespace App\Http\Controllers;

use App\Http\Resources\DegreeStudentInfo\StudentResource;
use App\Http\Resources\TvetStudentInfo\StudentResource as TvetStudentInfoStudentResource;
use App\Models\DegreeDepartment;
use App\Models\DegreeStudent;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\TvetDepartment;
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

            if ($teacher->status) {
                $token= $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'access_token'=>$token,
                    'role'=>'teacher',
                    'user'=>$teacher,
                ],200);
                }else {
                    return response()->json([
                        'message'=>' Un Authorized',
                        ]
                       ,404 );
                }


         } elseif ($user->user_type == 'employee') {
           //  return $user->user_type;
            $employee=Employee::where('email',$user->user_name)->first();

            if ($employee->status) {

            if ($employee->role == 'department_head') {


                $employee->department_role='degree_head';
                $deg_head=  DegreeDepartment::where('id',$employee->id)->first();
                $tvet_head=  TvetDepartment::where('id',$employee->id)->first();
                if ($deg_head) {
                    $token= $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'access_token'=>$token,
                        // 'department_role'=>'degree_head',
                        'user'=>$employee->load('manage'),
                    ],200);

                }else if($tvet_head) {
                    $employee->department_role='degree_head';

                    $token= $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'access_token'=>$token,
                        'role'=>'tvet_head',
                        'user'=>$employee->load('manage'),
                    ],200);
                }else {
                    $token= $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'access_token'=>$token,
                        'role'=>'head',
                        'user'=>$employee->load('manage'),
                    ],200);
                }

            }else{
                $token= $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'access_token'=>$token,
                    'user'=>$employee,
                ],200);
            }
        }else {
            return response()->json([
                'message'=>' UN Authorized ...',
                ]
                ,404 );
        }
     }else {
        return response()->json([
            'message'=>' UN Authorized ...',
            ]
            ,404 );
        }


     }


     public function studentLogin(Request $request){
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

       if ($user->user_type == 'degree_student') {
          $degree_student=DegreeStudent::where('student_id',$user->user_name)->first();
           if (!$degree_student->is_graduated) {

            $token= $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'role'=>'degree_student',
                'user'=>new StudentResource($degree_student),
            ],200);

        }else {
            return response()->json([
                'message'=>' UN Authorized ...',
                ]
               ,404 );
         }

     } elseif ($user->user_type == 'tvet_student') {
        $tvet_student=TvetStudent::where('student_id',$user->user_name)->first();

        if (!$tvet_student->is_graduated) {

            $token= $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token'=>$token,
                'role'=>'tvet_student',
                'user'=>new TvetStudentInfoStudentResource($tvet_student),
            ],200);
        }else {
            return response()->json(['message'=>' UN Authorized ...', ],404 );
         }

     }else {
        return response()->json([
            'message'=>' UN Authorized....',
            ]
           ,404 );
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

        $user=UserLogin::where('user_name',$request->user_name)->first();
        if (! $user ) {
            return response()->json([
                'message'=>' incorrect old password',
                ]
               ,404 );
        }

        $check=Hash::check($request->old_password, $user->password);
        if (! $check ) {
            return response()->json([
                'message'=>'  incorrect old password',
                ]
               ,404 );
        }

        $user->password=Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'message'=>'Successfully  Reset',
            ]
           ,200 );
    }

    public function resetStudentPassword(Request $request){


        // return $request->user_name;
         $user=UserLogin::where('user_name',$request->user_name)->first();

        if ($user->user_type == 'degree_student') {
            $student=DegreeStudent::where('student_id',$request->user_name)->first();
        }else if ($user->user_type == 'tvet_student') {
            $student=TvetStudent::where('student_id',$request->user_name)->first();
        }


        if (! $user ) {
            return response()->json([
                'message'=>' incorrect user_name and password',
                ]
               ,404 );
        }

        $user->password=Hash::make($student->last_name.'1234');
        $user->save();

        return response()->json([
            'message'=>'Successfully  Reset',
            ]
           ,200 );
    }

    public function resetEmployeePassword(Request $request){

        $user=UserLogin::where('user_name',$request->user_name)->first();

        $employee=Employee::where('email',$request->user_name)->first();



        if (! $user ) {
            return response()->json([
                'message'=>' incorrect user_name and password',
                ]
               ,404 );
        }

        $user->password=Hash::make($employee->last_name.'1234');
        $user->save();
        return response()->json([
            'message'=>'Successfully  Reset',
            ]
           ,200 );
    }
}
