<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, string $rol)
    {
        if (!Auth::check() || Auth::user()->type !== $rol) {
            return redirect()->route('login.show')
                ->withErrors(['email' => 'Acceso no autorizado.']);
        }

        return $next($request);
    }
}
