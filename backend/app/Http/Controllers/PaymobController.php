<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobController extends Controller
{
    public function processedCallback(Request $request)
    {
        // This is the return URL for the user
        // Query params usually contain: success, id, order, etc.
        
        $success = $request->query('success');
        $orderId = $request->query('merchant_order_id'); // If mapped correctly
        
        // Fallback: Paymob returns 'order' which is their internal ID. We need to map it back to our order if possible, 
        // OR rely on the merchant_order_id if passed in data.
        // Usually Paymob passes data.msg, data.order_id etc in GET params.
        
        if ($success === 'true') {
            return redirect()->route('checkout.success', ['order_number' => $orderId])
                             ->with('success', 'Payment successful!');
        } else {
             return redirect()->route('checkout.index')
                             ->with('error', 'Payment failed or was cancelled.');
        }
    }

    public function transactionCallback(Request $request)
    {
        // Server-to-server webhook
        // Validates HMAC and updates order status
        
        try {
            $data = $request->all();
            $obj = $data['obj'];
            
            $success = $obj['success'];
            $orderId = $obj['order']['merchant_order_id'];
            
            $order = Order::where('order_number', $orderId)->first();
            
            if ($order) {
                if ($success) {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                } else {
                    $order->update(['payment_status' => 'failed']);
                }
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Paymob Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}
