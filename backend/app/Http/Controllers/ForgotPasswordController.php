<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to phone
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        
        // Check if user exists with this phone
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        // Generate OTP
        $otpRecord = PasswordResetOtp::generateFor($phone);

        // Send OTP via SMS (using UltraMsg WhatsApp)
        try {
            $this->sendOtpViaSms($phone, $otpRecord->otp);
        } catch (\Exception $e) {
            return back()->withErrors(['phone' => 'Failed to send OTP. Please try again.']);
        }

        return redirect()->route('password.verify.form', ['phone' => $phone])
            ->with('success', 'OTP sent to your phone number.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyForm(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) {
            return redirect()->route('password.forgot');
        }
        
        return view('auth.verify-otp', [
            'phone' => $phone,
            'formAction' => route('password.verify-otp'),
            'resendAction' => route('password.resend-otp'),
            'backRoute' => route('password.forgot'),
            'backLabel' => 'Change Phone Number',
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        if (PasswordResetOtp::verify($phone, $request->otp)) {
            // Store in session for password reset
            session(['password_reset_phone' => $phone]);
            return redirect()->route('password.reset.form');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Show password reset form
     */
    public function showResetForm()
    {
        if (!session('password_reset_phone')) {
            return redirect()->route('password.forgot');
        }
        
        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $phone = session('password_reset_phone');
        if (!$phone) {
            return redirect()->route('password.forgot')
                ->withErrors(['error' => 'Session expired. Please start again.']);
        }

        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return redirect()->route('password.forgot')
                ->withErrors(['error' => 'User not found.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear session
        session()->forget('password_reset_phone');

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    /**
     * Send OTP via WhatsApp/SMS
     */
    private function sendOtpViaSms(string $phone, string $otp)
    {
        $notificationService = new \App\Services\NotificationService();
        $notificationService->sendOtpPasswordReset($phone, $otp);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        // Generate new OTP
        $otpRecord = PasswordResetOtp::generateFor($phone);

        try {
            $this->sendOtpViaSms($phone, $otpRecord->otp);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to resend OTP.']);
        }

        return back()->with('success', 'New OTP sent to your phone.');
    }
}
