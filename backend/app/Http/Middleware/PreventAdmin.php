<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdmin
{
    /**
     * Handle an incoming request.
     * Prevent admin users from accessing certain routes (like checkout)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect('/admin')->with('info', 'Redirected to admin dashboard.');
        }

        return $next($request);
    }
}
