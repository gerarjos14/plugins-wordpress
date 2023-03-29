<?php

namespace App\Http\Middleware;

use Closure;

class IsAgencyMiddleware
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
        if(!auth()->check() || !auth()->user()->isAgency()){
            return redirect('/');
        }
        return $next($request);
    }
}
