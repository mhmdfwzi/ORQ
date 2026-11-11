<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Social login users (Google/Facebook) skip phone verification
        if ($user->google_id || $user->facebook_id) {
            return $next($request);
        }

        if (!$user->isPhoneVerified()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your phone number',
                    'redirect' => route('otp.verify'),
                ], 403);
            }
            
            return redirect()->route('otp.verify')
                ->with('warning', 'Please verify your phone number to continue.');
        }

        return $next($request);
    }
}
