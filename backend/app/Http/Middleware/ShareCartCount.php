<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ShareCartCount
{
    public function handle(Request $request, Closure $next)
    {
        $cartCount = 0;
        
        try {
            if (auth()->check()) {
                $cart = Cart::where('user_id', auth()->id())->first();
            } else {
                // Use same session ID method as CartController
                $sessionId = $request->session()->getId();
                $cart = Cart::where('session_id', $sessionId)->first();
            }
            
            if ($cart) {
                // Count unique products, not total quantity
                $cartCount = $cart->items()->count();
            }
        } catch (\Throwable $e) {
            Log::error('ShareCartCount error: ' . $e->getMessage());
        }
        
        View::share('cartCount', $cartCount);
        
        return $next($request);
    }
}

