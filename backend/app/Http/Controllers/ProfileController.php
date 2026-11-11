<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        
        $totalOrders = Order::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $deliveredOrders = Order::where('user_id', $user->id)->where('status', 'delivered')->count();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        
        $recentOrders = Order::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->take(5)
            ->get();
        
        $orders = Order::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->get();
        
        $addresses = Address::where('user_id', $user->id)->get();
        
        $wishlist = Wishlist::where('user_id', $user->id)
            ->with('product')
            ->get();
        
        return view('profile.index', compact(
            'totalOrders', 
            'pendingOrders', 
            'deliveredOrders', 
            'wishlistCount',
            'recentOrders',
            'orders',
            'addresses',
            'wishlist'
        ));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->latest()
            ->paginate(10);
        
        return view('profile.orders', compact('orders'));
    }

    public function orderDetails($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->firstOrFail();
        
        return view('profile.order', compact('order'));
    }

    public function addresses()
    {
        $addresses = Address::where('user_id', auth()->id())->get();
        
        return view('profile.addresses', compact('addresses'));
    }

    public function wishlist()
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->with('product')
            ->paginate(12);
        
        return view('profile.wishlist', compact('wishlist'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
