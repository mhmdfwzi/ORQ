<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    public const CHECKOUT_SECONDS = 900;

    private const HOST = 'https://accept.paymob.com';

    private const SIGNED_FIELDS = [
        'amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction',
        'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded',
        'is_standalone_payment', 'is_voided', 'order.id', 'owner', 'pending',
        'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success',
    ];

    public function isConfigured(): bool
    {
        return config('services.paymob.enabled')
            && filled(config('services.paymob.secret_key'))
            && filled(config('services.paymob.public_key'))
            && filled(config('services.paymob.hmac_secret'))
            && ctype_digit((string) config('services.paymob.integration_id'))
            && (int) config('services.paymob.integration_id') > 0;
    }

    /** @return array{order_id: string, url: string, integration_id: string} */
    public function createCheckout(Order $order, string $email, string $checkoutKey): array
    {
        $integrationId = (int) config('services.paymob.integration_id');
        $amount = self::amountInPiastres($order);
        $name = preg_split('/\s+/u', $order->customer_name, 2);

        try {
            $response = Http::acceptJson()
                ->withToken(config('services.paymob.secret_key'), 'Token')
                ->connectTimeout(3)
                ->timeout(8)
                ->post(self::HOST . '/v1/intention/', [
                    'amount' => $amount,
                    'currency' => 'EGP',
                    'payment_methods' => [$integrationId],
                    'items' => [[
                        'name' => 'ORQ ' . $order->order_number,
                        'amount' => $amount,
                        'quantity' => 1,
                    ]],
                    'billing_data' => [
                        'first_name' => $name[0],
                        'last_name' => $name[1] ?? $name[0],
                        'email' => $email,
                        'phone_number' => '+2' . $order->customer_phone,
                        'street' => $order->shipping_address,
                        'city' => $order->shipping_governorate,
                        'state' => $order->shipping_governorate,
                        'country' => 'EG',
                        'building' => 'NA',
                        'floor' => 'NA',
                        'apartment' => 'NA',
                    ],
                    'special_reference' => $order->order_number,
                    'expiration' => self::CHECKOUT_SECONDS,
                    'notification_url' => rtrim(config('app.url'), '/') . '/api/v1/payments/paymob/webhook',
                    'redirection_url' => rtrim(config('app.url'), '/') . '/api/v1/payments/paymob/return/' . $checkoutKey,
                ]);
        } catch (ConnectionException) {
            Log::warning('Paymob checkout request timed out or could not connect.');
            abort(503, 'الدفع الإلكتروني غير متاح حالياً. يمكنك المحاولة مرة أخرى أو الدفع عند الاستلام.');
        }

        $orderId = $response->json('intention_order_id');
        $clientSecret = $response->json('client_secret');

        if (!$response->successful()
            || !(is_int($orderId) || is_string($orderId))
            || !preg_match('/\A[1-9][0-9]{0,31}\z/', (string) $orderId)
            || !is_string($clientSecret) || $clientSecret === '') {
            Log::warning('Paymob checkout could not be initialized.', ['http_status' => $response->status()]);
            abort(503, 'الدفع الإلكتروني غير متاح حالياً. يمكنك المحاولة مرة أخرى أو الدفع عند الاستلام.');
        }

        return [
            'order_id' => (string) $orderId,
            'integration_id' => (string) $integrationId,
            'url' => self::HOST . '/unifiedcheckout/?' . http_build_query([
                'publicKey' => config('services.paymob.public_key'),
                'clientSecret' => $clientSecret,
            ], '', '&', PHP_QUERY_RFC3986),
        ];
    }

    public function verifyTransaction(array $transaction, mixed $signature): bool
    {
        $secret = config('services.paymob.hmac_secret');
        if (!$secret || !is_string($signature) || !preg_match('/\A[a-f0-9]{128}\z/i', $signature)) {
            return false;
        }

        $message = '';
        foreach (self::SIGNED_FIELDS as $field) {
            if (!Arr::has($transaction, $field)) {
                return false;
            }

            $value = data_get($transaction, $field);
            if ($value === null && str_starts_with($field, 'source_data.')) {
                $message .= 'null';
            } elseif (is_bool($value)) {
                $message .= $value ? 'true' : 'false';
            } elseif (is_string($value) || is_int($value)) {
                $message .= $value;
            } else {
                return false;
            }
        }

        return hash_equals(hash_hmac('sha512', $message, $secret), strtolower($signature));
    }

    public static function amountInPiastres(Order $order): int
    {
        return (int) round((float) $order->total * 100);
    }
}
