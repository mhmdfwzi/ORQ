<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'ar'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, default to 'en'
        $locale = Session::get('locale', config('app.locale', 'en'));

        // Validate the locale
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }

        // Set application locale
        App::setLocale($locale);

        return $next($request);
    }
}
