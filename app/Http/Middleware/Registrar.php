<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;

class Registrar
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
        $user= Employee::where('email',$request->user()->user_name)->first();
        if ($user->role == 'registrar') {
            return $next($request);

        }else {
           return response()->json('UN Authorized ',403);
        }
    }
}
