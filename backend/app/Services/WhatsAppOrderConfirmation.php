<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOrderConfirmation
{
    public function isConfigured(): bool
    {
        return ctype_digit((string) config('services.whatsapp.phone_number_id'))
            && filled(config('services.whatsapp.access_token'))
            && preg_match('/\Av\d+\.\d+\z/', (string) config('services.whatsapp.graph_version')) === 1
            && filled(config('services.whatsapp.order_template'));
    }

    public function send(Order $order): void
    {
        $version = config('services.whatsapp.graph_version');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $recipient = '+20' . substr($order->customer_phone, 1);

        try {
            $response = Http::withToken(config('services.whatsapp.access_token'))
                ->connectTimeout(2)
                ->timeout(5)
                ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $recipient,
                    'type' => 'template',
                    'template' => [
                        'name' => config('services.whatsapp.order_template'),
                        'language' => ['code' => 'ar'],
                        'components' => [[
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $order->order_number],
                                ['type' => 'text', 'text' => $order->total],
                            ],
                        ]],
                    ],
                ]);
        } catch (ConnectionException) {
            Log::warning('WhatsApp order confirmation connection failed', ['order' => $order->order_number]);

            return;
        }

        if ($response->failed() || ! $response->json('messages.0.id')) {
            Log::warning('WhatsApp order confirmation was not accepted', [
                'order' => $order->order_number,
                'status' => $response->status(),
                'meta_code' => $response->json('error.code'),
                'meta_subcode' => $response->json('error.error_subcode'),
            ]);
        }
    }
}
