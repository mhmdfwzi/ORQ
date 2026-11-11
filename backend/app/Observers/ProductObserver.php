<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Triggers Next.js on-demand revalidation when a Product or ProductVariant changes.
 */
class ProductObserver
{
    public function saved(Model $model): void
    {
        $this->revalidate();
    }

    public function deleted(Model $model): void
    {
        $this->revalidate();
    }

    private function revalidate(): void
    {
        try {
            $url = config('app.frontend_url', 'http://localhost:3000') . '/api/revalidate';
            Http::timeout(3)->withHeaders([
                'x-revalidate-token' => config('app.revalidate_secret'),
            ])->post($url);
        } catch (\Exception $e) {
            Log::warning('Frontend revalidation failed: ' . $e->getMessage());
        }
    }
}
