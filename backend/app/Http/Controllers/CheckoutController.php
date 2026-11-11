<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function index()
    {
        try {
            $cart = Cart::getOrCreate(auth()->user());
            $cart->load('items.product');
            
            if ($cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty');
            }
            
            $user = auth()->user();
            $addresses = $user->addresses;
            
            // Get governorates for dropdown
            $governorates = \App\Models\Destination::governorates()->get();
            
            // Calculate initial shipping based on saved address or default
            $shipping = 0;
            $userCityId = $user->city_id ?? null;
            
            if ($userCityId) {
                $city = \App\Models\Destination::find($userCityId);
                if ($city) {
                    $shipping = $city->price;
                }
            } elseif ($addresses->isNotEmpty() && $addresses->first()->city_id) {
                $shipping = optional($addresses->first()->destination)->price ?? 0;
            }
            
            return view('checkout.index', compact('cart', 'addresses', 'shipping', 'governorates'));
        } catch (\Throwable $e) {
            \Log::error('Checkout Index Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->route('cart.index')->with('error', 'An error occurred loading checkout. Please try again.');
        }
    }

    public function process(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $existing = \App\Models\User::where('phone', $value)
                        ->where('id', '!=', auth()->id())
                        ->first();
                    if ($existing) {
                        $fail(__('This phone number is already registered to another account.'));
                    }
                },
            ],
            'street_address' => 'required|string|max:255',
            'governorate_id' => 'required|exists:destinations,id',
            'city_id' => 'required|exists:destinations,id',
            'payment_method' => 'required|in:cod,card',
        ]);
        
        $user = auth()->user();
        $cart = Cart::getOrCreate($user);
        $cart->load('items.product');
        
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        // Get shipping cost from city
        $city = \App\Models\Destination::findOrFail($request->city_id);
        $shipping = $city->price;

        // Save user's phone if not set (e.g., Google-registered users)
        if (empty($user->phone)) {
            $user->update(['phone' => $request->phone]);
        }

        // Save user's default destination if not set
        if (!$user->governorate_id) {
            $user->update([
                'governorate_id' => $request->governorate_id,
                'city_id' => $request->city_id,
                'street_address' => $request->street_address,
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Build full name
            $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));

            // Create or get address
            $address = Address::create([
                'user_id' => $user->id,
                'label' => 'Shipping',
                'full_name' => $fullName,
                'phone' => $request->phone,
                'address_line_1' => $request->street_address,
                'governorate_id' => $request->governorate_id,
                'city_id' => $request->city_id,
                'city' => $city->name, // Keep string for backward compatibility
                'state' => $city->parent->name ?? '', // Governorate name
                'postal_code' => null,
                'country' => 'EG',
                'is_default' => $user->addresses()->count() == 0,
            ]);
            
            $subtotal = $cart->subtotal;
            
            // Check for coupon
            $discount = 0;
            if (session()->has('coupon_discount')) {
                $discount = session('coupon_discount');
            }

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'discount' => $discount,
                'total' => $subtotal + $shipping - $discount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => $request->notes ?? null,
            ]);
            
            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku ?? 'N/A',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);
                
                // Decrease stock
                $item->product->decrement('stock_quantity', $item->quantity);
                $item->product->increment('orders_count');
            }
            
            // Clear cart & coupon
            $cart->items()->delete();
            session()->forget(['coupon_id', 'coupon_code', 'coupon_discount']);
            
            DB::commit();
            
            // Handle Payment Methods
            if ($request->payment_method == 'card') {
                // Temporary: Show "Coming Soon" message
                // When Paymob keys are available, remove this block and uncomment the code below
                return redirect()->route('checkout.index')->with('error', 'Credit Card payment service will be available soon. Please choose Cash on Delivery.');
                
                /* 
                // Paymob Integration Logic (Ready for enablement)
                try {
                    $paymob = new \App\Services\PaymobService();
                    
                    // 1. Authentication
                    $token = $paymob->getAuthToken();
                    
                    // 2. Register Order
                    $paymobOrderId = $paymob->createOrder(
                        $token, 
                        $order->order_number, 
                        $order->total * 100 // Amount in cents
                    );
                    
                    // 3. Get Payment Key
                    $billingData = [
                        "apartement" => "NA", 
                        "email" => $user->email, 
                        "floor" => "NA", 
                        "first_name" => $request->first_name, 
                        "street" => $request->street_address, 
                        "building" => "NA", 
                        "phone_number" => $request->phone, 
                        "shipping_method" => "NA", 
                        "postal_code" => "NA", 
                        "city" => $address->city, 
                        "country" => "EG", 
                        "last_name" => $request->last_name, 
                        "state" => $address->state ?? "NA"
                    ];
                    
                    $paymentKey = $paymob->getPaymentKey(
                        $token, 
                        $paymobOrderId, 
                        $order->total * 100, 
                        $billingData
                    );
                    
                    // 4. Redirect to Payment Page (iframe or direct link)
                    return redirect()->away("https://accept.paymobsolutions.com/api/acceptance/iframes/" . config('services.paymob.iframe_id') . "?payment_token=" . $paymentKey);
                    
                } catch (\Exception $e) {
                    \Log::error('Paymob Init Error: ' . $e->getMessage());
                    return redirect()->route('checkout.index')->with('error', 'Online payment is currently unavailable. Please try again or use Cash on Delivery.');
                }
                */
            }
            
            // Send notifications (WhatsApp/Email based on admin settings)
            try {
                $notificationService = new \App\Services\NotificationService();
                $notificationService->sendOrderConfirmationToCustomer($order);
                $notificationService->sendOrderConfirmationToAdmin($order);
            } catch (\Throwable $e) {
                // Log error but don't fail the order
                \Log::error('Order notification failed: ' . $e->getMessage());
            }
            
            return redirect()->route('checkout.success', $order->order_number);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('items')
            ->firstOrFail();
        
        return view('checkout.success', compact('order'));
    }
}
