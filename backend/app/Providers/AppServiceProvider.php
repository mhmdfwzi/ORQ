<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\ProductVariant::observe(\App\Observers\ProductObserver::class);

        try {
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
            \Illuminate\Support\Facades\View::share('settings', $settings);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AppServiceProvider settings error: ' . $e->getMessage());
            \Illuminate\Support\Facades\View::share('settings', collect([]));
        }
    }
}

