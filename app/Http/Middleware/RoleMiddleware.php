<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if(auth()->check() && auth()->user()->role->role === $role){
            return $next($request);
        }
        if(auth()->user()->role == 'admin'){
            return redirect()->route('administrator.home');
        }
        if(auth()->user()->role->role == 'teacher'){
            return redirect()->route('teacher.home');
        }
        if(auth()->user()->role->role == 'student'){
            return redirect()->route('student.home');
        }
    }
}
