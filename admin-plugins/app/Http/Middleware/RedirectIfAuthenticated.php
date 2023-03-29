<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            // return redirect(RouteServiceProvider::HOME);

            if(auth()->user()->isAdmin()){
                return redirect()->route('admin.dashboard');
            }
            if(auth()->user()->isAgency()){
                return redirect()->route('agency.dashboard');
            }
            if(auth()->user()->isCustomer()){
                return redirect()->route('customer.subscriptions.index');
            }
            return redirect('/');
        }

        return $next($request);
    }
}
