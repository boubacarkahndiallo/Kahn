<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vous devez être connecté.');
        }

        // Vérifie si c’est un super admin
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->route('app_dashboard')->with('danger', 'Accès refusé.');
        }

        return $next($request);
    }
}
