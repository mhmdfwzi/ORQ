<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\PaymobController;
use App\Http\Controllers\Api\SiteApiController;
use App\Http\Controllers\Api\StorefrontFunnelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| ORQ Perfume public API v1 – consumed by the Next.js frontend.
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/mood/{mood}', [ProductApiController::class, 'byMood']);
    Route::get('/products/best-sellers', [ProductApiController::class, 'bestSellers']);
    Route::get('/products/bundle', [ProductApiController::class, 'bundle']);
    Route::get('/products/{slug}', [ProductApiController::class, 'show']);

    // Brand story ("شفرة ORQ")
    Route::get('/brand', [BrandApiController::class, 'show']);

    // Storefront-wide settings (identity, SEO, footer, moods, commerce, ...)
    Route::get('/site', [SiteApiController::class, 'show']);

    Route::post('/contact', [ContactApiController::class, 'store'])->middleware('throttle:5,10');

    // Guest checkout. Throttled per IP to limit abuse.
    Route::post('/orders', [OrderApiController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/funnel', StorefrontFunnelController::class)->middleware('throttle:60,1,orq-funnel:');
    Route::post('/payments/paymob/webhook', [PaymobController::class, 'webhook']);
    Route::get('/payments/paymob/return/{key}', [PaymobController::class, 'returned'])->whereUuid('key');
    Route::get('/payments/paymob/status/{key}', [PaymobController::class, 'status'])->whereUuid('key')->middleware('throttle:30,1');
});
