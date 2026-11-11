<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Guest checkout endpoint for the Next.js storefront.
 */
class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->placeGuestOrder($request->validated());

        // Notifications must never fail the order itself.
        try {
            app(WhatsAppService::class)->notifyAdminNewOrder($order);
        } catch (\Throwable $e) {
            Log::error('Guest order notification failed: ' . $e->getMessage(), ['order' => $order->order_number]);
        }

        return response()->json([
            'data' => [
                'orderNumber'   => $order->order_number,
                'subtotal'      => (float) $order->subtotal,
                'shippingCost'  => (float) $order->shipping_cost,
                'total'         => (float) $order->total,
                'paymentMethod' => $order->payment_method,
                'status'        => $order->status,
            ],
        ], 201);
    }
}
