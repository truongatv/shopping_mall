<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\User;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->role != 1 && Auth::user()->role != 2){
            return response(trans('errors.not_exist'), 404);
        }

        return $next($request);
    }
}
