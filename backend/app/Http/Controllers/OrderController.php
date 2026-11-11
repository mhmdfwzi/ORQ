<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Track order status
     */
    public function track(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.track', compact('order'));
    }

    /**
     * Track order by number (public)
     */
    public function trackByNumber(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found']);
        }

        // Store in session for guest access
        session(['tracking_order_id' => $order->id]);

        return view('orders.track', compact('order'));
    }
}
