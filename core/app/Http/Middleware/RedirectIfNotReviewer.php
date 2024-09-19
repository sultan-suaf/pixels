<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotReviewer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = 'reviewer')
    {
        if (!Auth::guard($guard)->check()) {
            return to_route('reviewer.login');
        }
        
        if (!Auth::guard($guard)->user()->status) {
            return to_route('reviewer.login');
        }

        return $next($request);
    }
}
