<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Models\Order;
use App\Services\OnlineOrderService;
use App\Services\OrderService;
use App\Services\StorefrontOrderNotifications;
use Illuminate\Http\JsonResponse;

/**
 * Checkout for guest and signed-in storefront customers.
 */
class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function store(
        StoreOrderRequest $request,
        OnlineOrderService $onlineOrders,
        StorefrontOrderNotifications $notifications,
    ): JsonResponse
    {
        return $this->checkout($request, $onlineOrders, $notifications, null);
    }

    public function storeForCustomer(
        StoreOrderRequest $request,
        OnlineOrderService $onlineOrders,
        StorefrontOrderNotifications $notifications,
    ): JsonResponse
    {
        return $this->checkout($request, $onlineOrders, $notifications, $request->user('storefront')->id);
    }

    private function checkout(
        StoreOrderRequest $request,
        OnlineOrderService $onlineOrders,
        StorefrontOrderNotifications $notifications,
        ?int $customerId,
    ): JsonResponse
    {
        $data = $request->validated();
        $usesCard = ($data['payment_method'] ?? 'cod') === 'card';
        $order = $usesCard ? $onlineOrders->place($data, $customerId) : $this->orders->placeOrder($data, $customerId);

        if (!$usesCard) {
            $notifications->send($order);
        }

        $response = [
            'orderNumber' => $order->order_number,
            'subtotal' => (float) $order->subtotal,
            'shippingCost' => (float) $order->shipping_cost,
            'total' => (float) $order->total,
            'paymentMethod' => $order->payment_method,
            'status' => $order->status,
        ];
        if ($usesCard) {
            $response['checkoutUrl'] = $order->paymob_expires_at->isFuture() && $order->status === 'pending'
                ? $order->paymob_checkout_url
                : null;
            $response['checkoutKey'] = $order->payment_checkout_key;
        }

        return response()->json(['data' => $response], 201, ['Cache-Control' => 'no-store']);
    }
}
