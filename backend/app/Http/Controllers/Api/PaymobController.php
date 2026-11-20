<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OnlineOrderService;
use App\Services\PaymobService;
use App\Services\StorefrontOrderNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymobController extends Controller
{
    public function webhook(
        Request $request,
        PaymobService $paymob,
        OnlineOrderService $orders,
        StorefrontOrderNotifications $notifications,
    ): Response {
        // Verify against the original payload before framework string normalization.
        $payload = json_decode($request->getContent(), true);
        if (($payload['type'] ?? null) === 'TOKEN') {
            return response()->noContent();
        }

        $transaction = $payload['obj'] ?? null;
        abort_unless(
            ($payload['type'] ?? null) === 'TRANSACTION'
            && is_array($transaction)
            && $paymob->verifyTransaction($transaction, $request->query('hmac')),
            403
        );

        $order = $orders->recordTransaction($transaction);
        if ($order) {
            $notifications->send(
                $order,
                $order->trashed() || $order->paymob_released_at || $order->status !== 'processing',
            );
        }

        return response()->noContent();
    }

    public function returned(string $key): RedirectResponse
    {
        return redirect()->away(rtrim(config('app.frontend_url'), '/') . '/payment/result?token=' . $key)
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'no-store');
    }

    public function status(string $key, OnlineOrderService $orders): JsonResponse
    {
        $order = Order::withTrashed()->where('payment_checkout_key', strtolower($key))->firstOrFail();
        $order = $orders->releaseReservation($order);

        $status = match (true) {
            $order->payment_status === 'refunded' => 'refunded',
            $order->payment_status === 'paid' => $order->trashed() || $order->paymob_released_at || $order->status === 'cancelled'
                ? 'review' : 'paid',
            $order->paymob_released_at !== null => 'expired',
            $order->paymob_expires_at->isPast() => 'awaiting_confirmation',
            $order->paymob_declined => 'declined',
            default => 'pending',
        };

        return response()->json(['data' => [
            'orderNumber' => $order->order_number,
            'total' => (float) $order->total,
            'status' => $status,
        ]], 200, ['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }
}
