<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = auth()->user();
            
            // Check if phone is verified (only for customers)
            if (!$user->isAdmin() && !$user->isPhoneVerified()) {
                // Send new OTP and redirect to verification
                $this->sendOtp($user);
                return redirect()->route('otp.verify');
            }
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended('/admin');
            }
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
        ])->onlyInput('phone');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $emailRequired = \App\Models\Setting::getValue('registration_email_required', 1);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => $emailRequired ? 'required|email|unique:users,email' : 'nullable|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(6)],
            'terms' => 'accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::login($user);

        // Send OTP via WhatsApp
        $this->sendOtp($user);

        return redirect()->route('otp.verify')->with('success', 'Please verify your phone number to continue.');
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerify()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->isPhoneVerified()) {
            return redirect()->route('home');
        }

        return view('auth.verify-otp', [
            'phone' => auth()->user()->phone,
            'formAction' => route('otp.verify.submit'),
            'resendAction' => route('otp.resend'),
            'backRoute' => route('home'),
            'backLabel' => 'Change Phone Number',
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        
        $otpRecord = OtpVerification::where('user_id', $user->id)
            ->where('type', 'registration')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord || $otpRecord->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Verify phone
        $user->update(['phone_verified_at' => now()]);

        return redirect()->route('home')->with('success', 'Phone verified successfully! Welcome to Medicamo.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if can resend (rate limiting - 1 minute)
        $lastOtp = OtpVerification::where('user_id', $user->id)
            ->where('type', 'registration')
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
            $waitSeconds = 60 - $lastOtp->created_at->diffInSeconds(now());
            return back()->withErrors(['otp' => "Please wait {$waitSeconds} seconds before requesting a new code."]);
        }

        $this->sendOtp($user);

        return back()->with('success', 'New verification code sent to your WhatsApp.');
    }

    /**
     * Send OTP to user
     */
    private function sendOtp(User $user): void
    {
        $otp = OtpVerification::generateFor($user, 'registration');
        
        $notificationService = new \App\Services\NotificationService();
        $notificationService->sendOtpVerification($user, $otp->otp);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

