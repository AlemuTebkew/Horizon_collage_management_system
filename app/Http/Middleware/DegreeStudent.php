<?php

namespace App\Http\Middleware;

use App\Models\DegreeStudent as ModelsDegreeStudent;
use Closure;
use Illuminate\Http\Request;

class DegreeStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
       // $user= ModelsDegreeStudent::where('student_id',$request->user()->user_name)->first();
        if ($request->user()->user_type== 'degree_student') {
            return $next($request);

        }else {
           return response()->json('UN Authorized ',403);
        }
    }
}
