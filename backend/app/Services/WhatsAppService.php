<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\User;

class WhatsAppService
{
    private string $apiUrl;
    private string $token;

    public function __construct()
    {
        $instance = config('services.ultramsg.instance', 'instance56146');
        $this->apiUrl = "https://api.ultramsg.com/{$instance}/messages/chat";
        $this->token = config('services.ultramsg.token', '24ytu6qpg9s7hepi');
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage(?string $phone, string $message): bool
    {
        if (empty($phone)) {
            Log::warning('WhatsApp: Cannot send message, phone number is empty');
            return false;
        }

        try {
            $response = Http::timeout(30)->post($this->apiUrl, [
                'token' => $this->token,
                'to' => $this->formatPhone($phone),
                'body' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('WhatsApp message failed', [
                'phone' => $phone,
                'response' => $response->body()
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp service error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send OTP verification code
     */
    public function sendOTP(string $phone, string $otp): bool
    {
        $message = "🔐 *Medicamo Verification*\n\n";
        $message .= "Your verification code is: *{$otp}*\n\n";
        $message .= "This code expires in 10 minutes.\n";
        $message .= "Do not share this code with anyone.";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Notify admin(s) about new order - supports multiple phone numbers
     */
    public function notifyAdminNewOrder(Order $order): bool
    {
        $adminPhones = config('services.ultramsg.admin_phone');
        
        if (!$adminPhones) {
            Log::warning('Admin phone not configured for WhatsApp notifications');
            return false;
        }

        $fullAddress = $order->shipping_address_text;

        $message = "🛒 *New Order Received!*\n\n";
        $message .= "📦 Order: #{$order->order_number}\n";
        $message .= "👤 Customer: {$order->customer_display_name}\n";
        $message .= "📱 Phone: {$order->customer_display_phone}\n";
        foreach ($order->items as $item) {
            $message .= "🧴 {$item->product_name} {$item->size} × {$item->quantity}\n";
        }
        $message .= "💰 Total: {$order->total} EGP\n";
        $message .= "💳 Payment: " . strtoupper($order->payment_method) . "\n";
        $message .= "📍 Address: {$fullAddress}\n\n";
        $message .= "🕐 " . now('Africa/Cairo')->format('Y-m-d H:i');

        // Support multiple phone numbers separated by comma
        $phones = array_map('trim', explode(',', $adminPhones));
        $success = true;
        
        foreach ($phones as $phone) {
            if (!empty($phone)) {
                $result = $this->sendMessage($phone, $message);
                if (!$result) $success = false;
            }
        }

        return $success;
    }

    /**
     * Send order confirmation to customer
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        $message = "✅ *Order Confirmed!*\n\n";
        $message .= "Thank you for your order at *Medicamo*!\n\n";
        $message .= "📦 Order: #{$order->order_number}\n";
        $message .= "💰 Total: {$order->total} EGP\n";
        $message .= "💳 Payment: " . strtoupper($order->payment_method) . "\n\n";
        $message .= "We'll notify you when your order is shipped.\n\n";
        $message .= "Thank you for choosing Medicamo! 💚";

        return $this->sendMessage($order->user->phone, $message);
    }

    /**
     * Send order status update
     */
    public function sendOrderStatusUpdate(Order $order): bool
    {
        $statusMessages = [
            'confirmed' => '✅ Your order has been confirmed and is being prepared.',
            'processing' => '📦 Your order is being processed.',
            'shipped' => '🚚 Your order has been shipped!',
            'delivered' => '🎉 Your order has been delivered. Enjoy!',
            'cancelled' => '❌ Your order has been cancelled.',
        ];

        $statusMessage = $statusMessages[$order->status] ?? 'Your order status has been updated.';

        $message = "📋 *Order Update*\n\n";
        $message .= "Order: #{$order->order_number}\n";
        $message .= "{$statusMessage}\n\n";
        $message .= "Thank you for choosing Medicamo! 💚";

        return $this->sendMessage($order->user->phone, $message);
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhone(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure it starts with + for international format
        if (!str_starts_with($phone, '+')) {
            // Assume Egypt if no country code
            if (str_starts_with($phone, '0')) {
                $phone = '+2' . $phone;
            } else {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }
}
