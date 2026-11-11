<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Supported providers
     */
    protected array $providers = ['google', 'facebook'];

    /**
     * Redirect to provider
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid provider']);
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Socialite Redirect Error [' . $provider . ']: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Social login configuration error.']);
        }
    }

    /**
     * Handle provider callback
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid provider']);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Unable to authenticate with ' . ucfirst($provider)]);
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update social provider info
            $user->update([
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'phone' => null, // Social users may not have phone
                'password' => Hash::make(Str::random(24)),
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(), // Email is verified by provider
                'phone_verified_at' => null, // Phone not verified
            ]);
        }

        // Login the user
        Auth::login($user, true);

        return redirect()->intended(route('home'))
            ->with('success', 'Welcome, ' . $user->name . '!');
    }

    /**
     * Show complete profile form (for social users without phone)
     */
    public function showCompleteProfile()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->phone && $user->isPhoneVerified()) {
            return redirect()->route('home');
        }

        return view('auth.complete-profile', compact('user'));
    }

    /**
     * Save phone number for social user
     */
    public function savePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20|unique:users,phone,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->update(['phone' => $request->phone]);

        // Send OTP for phone verification
        $otp = \App\Models\OtpVerification::generateFor($user, 'registration');
        $whatsapp = new \App\Services\WhatsAppService();
        $whatsapp->sendOTP($user->phone, $otp->otp);

        return redirect()->route('otp.verify')
            ->with('success', 'Verification code sent to your WhatsApp.');
    }
}
