<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Public API controller for ORQ perfume products.
 *
 * Serves product data to the Next.js frontend via RESTful JSON endpoints.
 */
class ProductApiController extends Controller
{
    /**
     * List all published products with their variants.
     */
    public function index(): AnonymousResourceCollection
    {
        $products = Product::published()
            ->where('is_bundle', false)
            ->with('variants')
            ->orderBy('id')
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * Get a single product by its slug.
     */
    public function show(string $slug): ProductResource|JsonResponse
    {
        $product = Product::published()
            ->with('variants')
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return new ProductResource($product);
    }

    /**
     * List published products filtered by mood.
     */
    public function byMood(string $mood): AnonymousResourceCollection
    {
        $products = Product::published()
            ->where('is_bundle', false)
            ->byMood($mood)
            ->with('variants')
            ->orderBy('id')
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * List best-seller products.
     */
    public function bestSellers(): AnonymousResourceCollection
    {
        $products = Product::published()
            ->where('is_bundle', false)
            ->bestSellers()
            ->with('variants')
            ->orderBy('id')
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * Get the bundle product.
     */
    public function bundle(): ProductResource|JsonResponse
    {
        $product = Product::published()
            ->bundles()
            ->with('variants')
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Bundle not found.'], 404);
        }

        return new ProductResource($product);
    }
}
