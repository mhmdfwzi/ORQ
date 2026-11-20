<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OnlineOrderService
{
    // Give payment callbacks in flight time to arrive before releasing inventory.
    private const CALLBACK_GRACE_MINUTES = 30;

    public function __construct(
        private readonly OrderService $orders,
        private readonly PaymobService $paymob,
    ) {}

    public function place(array $data): Order
    {
        $key = strtolower($data['checkout_key']);
        $data['checkout_key'] = $key;
        $requestHash = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

        try {
            return Cache::lock('orq.checkout.' . $key, 30)->block(5, function () use ($data, $key, $requestHash) {
                $existing = Order::where('payment_checkout_key', $key)->first();
                if ($existing) {
                    abort_unless(hash_equals($existing->payment_request_hash, $requestHash), 409, 'بيانات محاولة الدفع مختلفة. ابدأ محاولة جديدة.');

                    return $existing;
                }

                abort_unless($this->paymob->isConfigured(), 503, 'الدفع الإلكتروني غير متاح حالياً. يمكنك الدفع عند الاستلام.');

                // The checkout URL is only returned after both the reservation and the
                // Paymob order ID are committed. A provider error rolls back the stock.
                return DB::transaction(function () use ($data, $key, $requestHash) {
                    $order = $this->orders->placeGuestOrder($data);
                    $checkout = $this->paymob->createCheckout($order, $data['billing_email'], $key);
                    $order->forceFill([
                        'payment_checkout_key' => $key,
                        'payment_request_hash' => $requestHash,
                        'paymob_order_id' => $checkout['order_id'],
                        'paymob_integration_id' => $checkout['integration_id'],
                        'paymob_checkout_url' => $checkout['url'],
                        'paymob_expires_at' => now()->addSeconds(PaymobService::CHECKOUT_SECONDS),
                    ])->save();

                    return $order;
                });
            });
        } catch (LockTimeoutException) {
            abort(409, 'جارٍ تجهيز الدفع. انتظر لحظة وحاول مرة أخرى.');
        }
    }

    public function recordTransaction(array $transaction): ?Order
    {
        return DB::transaction(function () use ($transaction) {
            $order = Order::withTrashed()
                ->where('paymob_order_id', (string) $transaction['order']['id'])
                ->where('payment_method', 'card')
                ->lockForUpdate()
                ->first();

            if (!$order
                || (string) $transaction['integration_id'] !== $order->paymob_integration_id
                || $transaction['currency'] !== 'EGP'
                || (string) $transaction['amount_cents'] !== (string) PaymobService::amountInPiastres($order)
                || $order->payment_status !== 'pending'
                || $transaction['pending'] !== false
                || $transaction['is_voided'] !== false
                || $transaction['is_refunded'] !== false
                || ($transaction['is_refund'] ?? false) === true
                || ($transaction['is_void'] ?? false) === true
                || $transaction['is_auth'] !== false
                || ($transaction['is_standalone_payment'] !== true && $transaction['is_capture'] !== true)) {
                return null;
            }

            if ($transaction['success'] !== true || $transaction['error_occured'] !== false) {
                $order->forceFill(['paymob_declined' => true])->save();

                return null;
            }

            $canFulfill = !$order->trashed() && $order->status === 'pending' && !$order->paymob_released_at;
            if ($canFulfill) {
                foreach ($order->items()->orderBy('product_id')->get() as $item) {
                    Product::whereKey($item->product_id)->increment('orders_count', $item->quantity);
                }
            }

            $order->forceFill([
                'payment_status' => 'paid',
                'status' => $canFulfill ? 'processing' : $order->status,
                'paymob_transaction_id' => (string) $transaction['id'],
                'paymob_checkout_url' => null,
            ])->save();

            return $order;
        }, 3);
    }

    public function releaseExpired(): void
    {
        Order::withTrashed()
            ->where('payment_status', 'pending')
            ->whereNull('paymob_released_at')
            ->where('paymob_expires_at', '<=', now()->subMinutes(self::CALLBACK_GRACE_MINUTES))
            ->eachById(fn (Order $order) => $this->releaseReservation($order));
    }

    public function releaseReservation(Order $order): Order
    {
        if ($order->payment_status !== 'pending' || $order->paymob_released_at
            || !$order->paymob_expires_at
            || $order->paymob_expires_at->isAfter(now()->subMinutes(self::CALLBACK_GRACE_MINUTES))) {
            return $order;
        }

        return DB::transaction(function () use ($order) {
            $current = Order::withTrashed()->lockForUpdate()->findOrFail($order->getKey());
            if ($current->payment_status !== 'pending' || $current->paymob_released_at) {
                return $current;
            }

            foreach ($current->items()->whereNotNull('product_variant_id')->orderBy('product_variant_id')->get() as $item) {
                ProductVariant::whereKey($item->product_variant_id)->increment('stock_quantity', $item->quantity);
            }

            $current->forceFill([
                'status' => 'cancelled',
                'paymob_released_at' => now(),
                'paymob_checkout_url' => null,
            ])->save();

            return $current;
        }, 3);
    }
}
