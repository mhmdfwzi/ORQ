<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StorefrontOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user('storefront')->orders()->withCount('items')
            ->orderByDesc('created_at')->orderByDesc('id')->simplePaginate(10);

        return $this->privateJson([
            'data' => $orders->getCollection()->map($this->summary(...)),
            'nextPage' => $orders->hasMorePages() ? $orders->currentPage() + 1 : null,
        ]);
    }

    public function show(Request $request, string $number): JsonResponse
    {
        $order = $request->user('storefront')->orders()->with(['items', 'address'])->withCount('items')
            ->where('order_number', $number)->first();
        if (! $order) {
            return $this->privateJson(['message' => 'الطلب غير موجود.'], 404);
        }

        return $this->privateJson(['data' => [
            ...$this->summary($order),
            'subtotal' => (float) $order->subtotal,
            'shippingCost' => (float) $order->shipping_cost,
            'discount' => (float) $order->discount,
            'address' => $order->shipping_address || $order->address ? $order->shipping_address_text : null,
            'trackingNumber' => $order->tracking_number,
            'shippingCarrier' => $order->shipping_carrier,
            'timeline' => array_map(fn ($step) => [
                'status' => $step['status'],
                'completed' => $step['completed'],
                'date' => $step['date']?->toIso8601String(),
            ], $order->getStatusTimeline()),
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'total' => (float) $item->total_price,
            ]),
        ]]);
    }

    private function summary(Order $order): array
    {
        return [
            'number' => $order->order_number,
            'date' => $order->created_at->toIso8601String(),
            'status' => $order->status,
            'total' => (float) $order->total,
            'paymentMethod' => $order->payment_method,
            'paymentStatus' => $order->payment_status,
            'itemCount' => $order->items_count,
        ];
    }

    private function privateJson(array $payload, int $status = 200): JsonResponse
    {
        return response()->json($payload, $status)->header('Cache-Control', 'private, no-store');
    }
}
