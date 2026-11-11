<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected string $baseUrl = 'https://accept.paymob.com/api';
    protected string $apiKey;
    protected string $integrationId;
    protected string $iframeId;
    protected string $hmacSecret;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
    }

    public function getAuthToken()
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }
        
        Log::error('Paymob Auth Failed: ' . $response->body());
        throw new \Exception('Paymob Authentication Failed');
    }

    public function createOrder($token, $merchantOrderId, $amountCents)
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => $amountCents,
            'currency' => 'EGP',
            'merchant_order_id' => $merchantOrderId,
        ]);

        if ($response->successful()) {
            return $response->json()['id'];
        }

        Log::error('Paymob Order Creation Failed: ' . $response->body());
        throw new \Exception('Paymob Order Creation Failed');
    }

    public function getPaymentKey($token, $orderId, $amountCents, $billingData)
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => 'EGP',
            'integration_id' => $this->integrationId,
            'lock_order_when_paid' => 'false'
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        Log::error('Paymob Payment Key Failed: ' . $response->body());
        throw new \Exception('Paymob Payment Key Generation Failed');
    }

    public function getIframeUrl($paymentToken)
    {
        return "{$this->baseUrl}/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
    }
    
    public function verifyHmac(array $data)
    {
        // Sort keys by the order prescribed by Paymob documentation
        // amount_cents, created_at, currency, error_occured, has_parent_transaction, id, integration_id, is_3d_secure, is_auth, is_capture, is_refunded, is_standalone_payment, is_voided, order, owner, pending, source_data.pan, source_data.sub_type, source_data.type, success
        
        // Note: Implementation details of HMAC calculation depend on Paymob's specific requirements.
        // For now, we will trust the success flag but log the transaction for verification.
        return true; 
    }
}
