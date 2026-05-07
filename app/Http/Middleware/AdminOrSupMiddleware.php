<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrSupMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && in_array(Auth::user()->role?->name, ['admin', 'sup'])) {
            return $next($request);
        }

        abort(403, 'Accès refusé : rôle admin ou sup requis');
    }
}
