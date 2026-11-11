<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Toggle product in wishlist
     */
    public function toggle(Request $request, Product $product)
    {
        if (!auth()->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to add to wishlist',
                    'redirect' => route('login'),
                ], 401);
            }
            return redirect()->route('login');
        }

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $action = 'removed';
            $inWishlist = false;
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $action = 'added';
            $inWishlist = true;
        }

        $wishlistCount = Wishlist::where('user_id', auth()->id())->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'in_wishlist' => $inWishlist,
                'message' => $action === 'added' 
                    ? 'Product added to wishlist' 
                    : 'Product removed from wishlist',
                'wishlist_count' => $wishlistCount,
            ]);
        }

        return back()->with('success', $action === 'added' 
            ? 'Product added to wishlist!' 
            : 'Product removed from wishlist!');
    }

    /**
     * Get user's wishlist
     */
    public function index()
    {
        $wishlistItems = Wishlist::where('user_id', auth()->id())
            ->with('product.category')
            ->latest()
            ->get();

        return view('profile.wishlist', compact('wishlistItems'));
    }

    /**
     * Remove from wishlist
     */
    public function remove(Request $request, Product $product)
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
            ]);
        }

        return back()->with('success', 'Product removed from wishlist!');
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart(Request $request, Product $product)
    {
        // Remove from wishlist
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        // Add to cart
        $cart = \App\Models\Cart::getOrCreate(auth()->user());
        $cart->addProduct($product, 1);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product moved to cart',
                'cart_count' => $cart->items()->count(),
            ]);
        }

        return back()->with('success', 'Product moved to cart!');
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Product $product)
    {
        $inWishlist = auth()->check() && Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();

        return response()->json([
            'in_wishlist' => $inWishlist,
        ]);
    }
}
