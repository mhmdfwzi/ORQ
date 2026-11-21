<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Services\PaymobService;
use App\Services\WhatsAppOrderConfirmation;
use App\Support\SiteSettings;
use Illuminate\Http\JsonResponse;

/**
 * Storefront-wide settings (identity, SEO, footer, moods, commerce, creed, bundle)
 * plus a few values derived from live catalogue data.
 */
class SiteApiController extends Controller
{
    public function show(WhatsAppOrderConfirmation $confirmation, PaymobService $paymob): JsonResponse
    {
        $published = Product::published()->where('is_bundle', false);

        $prices = ProductVariant::forSale()
            ->whereIn('product_id', (clone $published)->select('id'))
            ->selectRaw('COALESCE(sale_price, price) as effective')
            ->pluck('effective');

        return response()->json([
            'data' => [
                'whatsapp_order_confirmation_available' => $confirmation->isConfigured(),
                'online_payment_available' => $paymob->isConfigured(),
                'contact' => Setting::where('group', 'contact')->whereIn('key', [
                    'contact_phone', 'contact_phone_2', 'whatsapp_number', 'contact_email', 'contact_email_2',
                    'contact_address', 'working_hours',
                ])->pluck('value', 'key')->all() ?: (object) [],
            ] + SiteSettings::get() + [
                'stats' => [
                    'productCount' => (clone $published)->count(),
                    'minPrice'     => $prices->isEmpty() ? null : (float) $prices->min(),
                    'maxPrice'     => $prices->isEmpty() ? null : (float) $prices->max(),
                ],
            ],
        ]);
    }
}

