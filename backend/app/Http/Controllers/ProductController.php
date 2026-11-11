<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::published()->with('category');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        // Categories filter (multiple)
        if ($request->filled('categories')) {
            $categorySlugs = $request->categories;
            $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }
        
        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('orders_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }
        
        $products = $query->paginate(12);
        
        $categories = Category::active()
            ->withCount('products')
            ->orderBy('name')
            ->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        // Try to find by slug first, then by ID
        $product = Product::published()
            ->where('slug', $slug)
            ->with(['category', 'approvedReviews.user'])
            ->first();
        
        // If not found by slug, try by ID
        if (!$product) {
            $product = Product::published()
                ->where('id', $slug)
                ->with(['category', 'approvedReviews.user'])
                ->firstOrFail();
        }
        
        // Increment views
        $product->incrementViews();
        
        // Check if in wishlist
        $inWishlist = auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();
        
        // Related products
        $relatedProducts = Product::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
        
        return view('products.show', compact('product', 'relatedProducts', 'inWishlist'));
    }
}
