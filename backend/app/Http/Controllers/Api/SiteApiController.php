<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\SiteSettings;
use Illuminate\Http\JsonResponse;

/**
 * Storefront-wide settings (identity, SEO, footer, moods, commerce, creed, bundle)
 * plus a few values derived from live catalogue data.
 */
class SiteApiController extends Controller
{
    public function show(): JsonResponse
    {
        $published = Product::published()->where('is_bundle', false);

        $prices = ProductVariant::query()
            ->where('is_active', true)
            ->whereIn('product_id', (clone $published)->select('id'))
            ->selectRaw('COALESCE(sale_price, price) as effective')
            ->pluck('effective');

        return response()->json([
            'data' => SiteSettings::get() + [
                'stats' => [
                    'productCount' => (clone $published)->count(),
                    'minPrice'     => $prices->isEmpty() ? null : (float) $prices->min(),
                    'maxPrice'     => $prices->isEmpty() ? null : (float) $prices->max(),
                ],
            ],
        ]);
    }
}

