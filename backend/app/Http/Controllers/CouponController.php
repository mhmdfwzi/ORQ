<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Apply coupon to cart
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code',
            ], 400);
        }

        // Get cart subtotal
        $cart = $this->getCart();
        $subtotal = $cart ? $cart->subtotal : 0;

        // Validate coupon
        if (!$coupon->isValid($subtotal, auth()->id())) {
            $message = $this->getInvalidMessage($coupon, $subtotal);
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        // Store in session
        session([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'coupon_discount' => $discount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
            'discount_formatted' => '$' . number_format($discount, 2),
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
        ]);
    }

    /**
     * Remove coupon from cart
     */
    public function remove(Request $request)
    {
        session()->forget(['coupon_id', 'coupon_code', 'coupon_discount']);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed',
        ]);
    }

    /**
     * Get cart
     */
    private function getCart()
    {
        if (auth()->check()) {
            return \App\Models\Cart::where('user_id', auth()->id())->first();
        }
        return \App\Models\Cart::where('session_id', session()->getId())->first();
    }

    /**
     * Get invalid coupon message
     */
    private function getInvalidMessage(Coupon $coupon, float $subtotal): string
    {
        if (!$coupon->is_active) {
            return 'This coupon is not active';
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return 'This coupon is not yet valid';
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return 'This coupon has expired';
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return 'This coupon has reached its usage limit';
        }

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return 'Minimum order amount is $' . number_format($coupon->min_order_amount, 2);
        }

        if (auth()->check() && $coupon->per_user_limit) {
            $userUsageCount = $coupon->usages()->where('user_id', auth()->id())->count();
            if ($userUsageCount >= $coupon->per_user_limit) {
                return 'You have already used this coupon';
            }
        }

        return 'Invalid coupon';
    }
}
