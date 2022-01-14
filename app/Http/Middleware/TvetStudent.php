<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TvetStudent
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
        if ($request->user()->user_type== 'tvet_student') {
            return $next($request);

        }else {
           return response()->json('UN Authorized ',403);
        }
    }
}
