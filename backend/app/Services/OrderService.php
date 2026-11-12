<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Places guest (account-less) cash-on-delivery orders.
 *
 * Never trusts client data for money: prices and availability are resolved
 * from the database inside a transaction with row locks on stock.
 */
class OrderService
{
    /**
     * @param  array{name:string,phone:string,governorate:string,address:string,notes?:?string,items:array<int,array{slug:string,size:string,quantity:int}>}  $data
     *
     * @throws ValidationException when an item is unavailable or out of stock
     */
    public function placeGuestOrder(array $data): Order
    {
        $lines = $this->mergeLines($data['items']);

        return DB::transaction(function () use ($data, $lines) {
            $subtotal = 0;
            $resolved = [];

            foreach ($lines as $line) {
                $product = Product::published()->where('slug', $line['slug'])->first();

                $variant = $product
                    ? ProductVariant::where('product_id', $product->id)
                        ->where('size', $line['size'])
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->first()
                    : null;

                if (!$product || !$variant) {
                    throw ValidationException::withMessages([
                        'items' => ["المنتج {$line['slug']} ({$line['size']}) غير متاح."],
                    ]);
                }

                if ($variant->stock_quantity < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["الكمية المطلوبة من {$product->name} ({$line['size']}) غير متوفرة حالياً."],
                    ]);
                }

                $unit = (float) ($variant->sale_price ?? $variant->price);
                $total = $unit * $line['quantity'];
                $subtotal += $total;

                $resolved[] = compact('product', 'variant', 'unit', 'total') + ['quantity' => $line['quantity']];
            }

            $shipping = SiteSettings::shippingCost();

            $order = Order::create([
                'order_number'         => Order::generateOrderNumber(),
                'customer_name'        => trim($data['name']),
                'customer_phone'       => $this->normalizePhone($data['phone']),
                'shipping_governorate' => trim($data['governorate']),
                'shipping_address'     => trim($data['address']),
                'subtotal'             => $subtotal,
                'shipping_cost'        => $shipping,
                'discount'             => 0,
                'total'                => $subtotal + $shipping,
                'status'               => 'pending',
                'payment_method'       => 'cod',
                'payment_status'       => 'pending',
                'notes'                => $data['notes'] ?? null,
            ]);

            foreach ($resolved as $row) {
                $order->items()->create([
                    'product_id'         => $row['product']->id,
                    'product_variant_id' => $row['variant']->id,
                    'product_name'       => $row['product']->name,
                    'product_sku'        => $row['variant']->sku,
                    'size'               => $row['variant']->size,
                    'quantity'           => $row['quantity'],
                    'unit_price'         => $row['unit'],
                    'total_price'        => $row['total'],
                ]);

                $row['variant']->decrement('stock_quantity', $row['quantity']);
                $row['product']->increment('orders_count', $row['quantity']);
            }

            return $order->load('items');
        });
    }

    /**
     * Collapse duplicate slug+size lines so stock checks see the real total.
     *
     * @param  array<int,array{slug:string,size:string,quantity:int}>  $items
     * @return array<int,array{slug:string,size:string,quantity:int}>
     */
    private function mergeLines(array $items): array
    {
        $merged = [];

        foreach ($items as $item) {
            $key = $item['slug'] . '|' . $item['size'];
            $merged[$key] = [
                'slug'     => $item['slug'],
                'size'     => $item['size'],
                'quantity' => ($merged[$key]['quantity'] ?? 0) + (int) $item['quantity'],
            ];
        }

        return array_values($merged);
    }

    /** Store phones as local 01xxxxxxxxx. */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $digits = preg_replace('/^(0020|20)/', '', $digits);

        return str_starts_with($digits, '0') ? $digits : '0' . $digits;
    }
}
