<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Check header request and determine localizaton
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : $user->local;

            // set laravel localization
            app()->setLocale($local);
        } else {
            // Check header request and determine localizaton
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'ar';

            // set laravel localization
            app()->setLocale($local);
        }


        // continue request
        return $next($request);
    }
}
