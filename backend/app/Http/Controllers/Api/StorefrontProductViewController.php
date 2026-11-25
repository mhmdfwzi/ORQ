<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Support\ProductSlugs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StorefrontProductViewController
{
    public function __invoke(Request $request, string $slug): Response
    {
        $data = $request->validate(['session_id' => ['required', 'uuid']]);
        $product = Product::published()->where('slug', ProductSlugs::canonical($slug))->firstOrFail(['id']);

        DB::table('storefront_product_views')->insertOrIgnore([
            'day' => now('Africa/Cairo')->toDateString(),
            'product_id' => $product->id,
            'session_id' => strtolower($data['session_id']),
        ]);

        return response()->noContent();
    }
}
