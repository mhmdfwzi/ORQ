<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product.category');
        
        $shipping = 0; // Default to 0 instead of 5
        
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->city_id) {
                // Determine shipping based on user's saved city
                $city = \App\Models\Destination::find($user->city_id);
                if ($city) {
                    $shipping = $city->price;
                }
            } elseif ($user->addresses->isNotEmpty()) {
                // Or from their first address
                $address = $user->addresses->first();
                if ($address->city_id && $address->destination) {
                    $shipping = $address->destination->price;
                }
            }
        }
        
        return view('cart.index', compact('cart', 'shipping'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'integer|min:1'
        ]);
        
        $cart = $this->getCart();
        $cart->addProduct($product, $request->quantity ?? 1);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cartCount' => $cart->items()->count() // Count unique products
            ]);
        }
        
        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1'
        ]);
        
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);
        
        if ($request->has('quantity')) {
            $item->update(['quantity' => $request->quantity]);
        } elseif ($request->has('delta')) {
            $newQuantity = $item->quantity + $request->delta;
            if ($newQuantity <= 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $newQuantity]);
            }
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Item removed from cart!');
    }

    private function getCart()
    {
        if (auth()->check()) {
            return Cart::getOrCreate(auth()->user());
        }
        
        $sessionId = session()->getId();
        return Cart::getOrCreate(null, $sessionId);
    }
}
