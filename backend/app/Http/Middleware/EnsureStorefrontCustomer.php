<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStorefrontCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = $request->user('storefront');
        if (! $customer?->isCustomer() || ! $customer->isActive()) {
            return response()->json(['message' => 'سجّل الدخول لمتابعة حسابك.'], 401)
                ->header('Cache-Control', 'private, no-store');
        }

        return $next($request);
    }
}
