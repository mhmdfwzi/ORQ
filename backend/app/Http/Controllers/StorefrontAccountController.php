<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class StorefrontAccountController extends Controller
{
    public function session(Request $request): JsonResponse
    {
        $user = Auth::guard('storefront')->user();
        $customer = $user?->isCustomer() && $user->isActive() ? $user : null;

        return response()->json([
            'googleAvailable' => $this->googleAvailable(),
            'customer' => $customer ? [
                'name' => $customer->name,
                'email' => $customer->email,
            ] : null,
            'csrfToken' => $customer ? $request->session()->token() : null,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        if (! $this->googleAvailable()) {
            return $this->accountPage('unavailable');
        }

        return Socialite::driver('google')->enablePKCE()->redirect()
            ->header('Cache-Control', 'private, no-store');
    }

    public function googleCallback(Request $request): RedirectResponse
    {
        if (! $this->googleAvailable()) {
            return $this->accountPage('unavailable');
        }

        try {
            if (! $request->filled('code')) {
                return $this->accountPage($request->query('error') === 'access_denied' ? 'cancelled' : 'failed');
            }

            // Socialite validates the session state before exchanging the authorization code.
            $google = Socialite::driver('google')->enablePKCE()->user();
        } catch (InvalidStateException) {
            return $this->accountPage('failed');
        } catch (Throwable $exception) {
            Log::warning('Storefront Google sign-in could not complete', ['exception_type' => $exception::class]);

            return $this->accountPage('failed');
        } finally {
            $request->session()->forget(['state', 'code_verifier']);
        }

        $subject = $google->getId();
        $email = Str::lower(trim((string) $google->getEmail()));
        if (! is_string($subject) || $subject === '' || strlen($subject) > 255
            || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255
            || ($google->getRaw()['email_verified'] ?? false) !== true) {
            return $this->accountPage('failed');
        }

        $customer = User::withTrashed()->where('google_id', $subject)->first();

        if (! $customer) {
            if (User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                return $this->accountPage('existing');
            }

            try {
                $customer = User::firstOrCreate(['google_id' => $subject], [
                    'name' => Str::limit(trim((string) $google->getName()) ?: Str::before($email, '@'), 255, ''),
                    'email' => $email,
                    'password' => Str::random(64),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException) {
                return $this->accountPage('existing');
            }
        }

        if ($customer->trashed() || ! $customer->isCustomer() || ! $customer->isActive()) {
            return $this->accountPage('failed');
        }

        Auth::guard('storefront')->login($customer);

        return $this->accountPage();
    }

    public function logout(Request $request): Response
    {
        Auth::guard('storefront')->logoutCurrentDevice();
        $request->session()->regenerate(true);

        return response()->noContent()->header('Cache-Control', 'private, no-store');
    }

    public function accountPage(?string $reason = null): RedirectResponse
    {
        $url = rtrim(config('app.frontend_url'), '/') . '/account';

        return redirect()->away($url . ($reason ? '?google=' . $reason : ''))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }

    private function googleAvailable(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
