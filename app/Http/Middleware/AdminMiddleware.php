<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // dd(Auth::user()->role?->name);
        if (Auth::check() && Auth::user()->role?->name === 'admin') {
            return $next($request);
        }

        abort(403, 'Accès refusé : rôle admin requis');
    }
}