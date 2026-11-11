<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;

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
});
