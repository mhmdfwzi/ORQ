<?php

namespace App\Services;

use App\Mail\AdminNewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OtpPasswordResetMail;
use App\Mail\OtpVerificationMail;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected WhatsAppService $whatsapp;

    public function __construct()
    {
        $this->whatsapp = new WhatsAppService();
    }

    /**
     * Resolve the effective channel based on user's available contact info.
     * If admin chose 'whatsapp' but user has no phone → fallback to 'email'
     * If admin chose 'email' but user has no email → fallback to 'whatsapp'
     * If admin chose 'both' → only send via available channels
     */
    private function resolveChannel(string $configuredChannel, bool $hasPhone, bool $hasEmail): array
    {
        $sendWhatsApp = false;
        $sendEmail = false;

        switch ($configuredChannel) {
            case 'whatsapp':
                if ($hasPhone) {
                    $sendWhatsApp = true;
                } elseif ($hasEmail) {
                    // Fallback: user has no phone, send via email instead
                    $sendEmail = true;
                    Log::info('Notification fallback: WhatsApp → Email (user has no phone)');
                }
                break;

            case 'email':
                if ($hasEmail) {
                    $sendEmail = true;
                } elseif ($hasPhone) {
                    // Fallback: user has no email, send via WhatsApp instead
                    $sendWhatsApp = true;
                    Log::info('Notification fallback: Email → WhatsApp (user has no email)');
                }
                break;

            case 'both':
                $sendWhatsApp = $hasPhone;
                $sendEmail = $hasEmail;
                break;
        }

        return ['whatsapp' => $sendWhatsApp, 'email' => $sendEmail];
    }

    /**
     * Send OTP for account verification
     */
    public function sendOtpVerification(User $user, string $otp): void
    {
        $channel = Setting::getValue('otp_verify_channel', 'whatsapp');
        $resolved = $this->resolveChannel($channel, !empty($user->phone), !empty($user->email));

        if ($resolved['whatsapp']) {
            try {
                $this->whatsapp->sendOTP($user->phone, $otp);
            } catch (\Throwable $e) {
                Log::error('OTP WhatsApp failed: ' . $e->getMessage());
            }
        }

        if ($resolved['email']) {
            try {
                Mail::to($user->email)->send(new OtpVerificationMail($otp));
            } catch (\Throwable $e) {
                Log::error('OTP Email failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtpPasswordReset(string $phone, string $otp, ?string $email = null): void
    {
        $channel = Setting::getValue('otp_reset_channel', 'whatsapp');

        // Find user email if not provided
        if (!$email) {
            $user = User::where('phone', $phone)->first();
            $email = $user?->email;
        }

        $resolved = $this->resolveChannel($channel, !empty($phone), !empty($email));

        if ($resolved['whatsapp']) {
            try {
                $this->whatsapp->sendOTP($phone, $otp);
            } catch (\Throwable $e) {
                Log::error('Password Reset OTP WhatsApp failed: ' . $e->getMessage());
            }
        }

        if ($resolved['email'] && $email) {
            try {
                Mail::to($email)->send(new OtpPasswordResetMail($otp));
            } catch (\Throwable $e) {
                Log::error('Password Reset OTP Email failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send order confirmation to customer
     */
    public function sendOrderConfirmationToCustomer(Order $order): void
    {
        $channel = Setting::getValue('order_confirm_customer_channel', 'whatsapp');
        $user = $order->user;
        $resolved = $this->resolveChannel($channel, !empty($user->phone), !empty($user->email));

        if ($resolved['whatsapp']) {
            try {
                $this->whatsapp->sendOrderConfirmation($order);
            } catch (\Throwable $e) {
                Log::error('Order confirmation WhatsApp failed: ' . $e->getMessage());
            }
        }

        if ($resolved['email']) {
            try {
                $order->load('items');
                Mail::to($user->email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $e) {
                Log::error('Order confirmation Email failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send order notification to admin
     */
    public function sendOrderConfirmationToAdmin(Order $order): void
    {
        $channel = Setting::getValue('order_confirm_admin_channel', 'whatsapp');
        $adminEmail = Setting::getValue('admin_email', 'medicamodrug@gmail.com');
        $adminPhones = config('services.ultramsg.admin_phone');

        $resolved = $this->resolveChannel($channel, !empty($adminPhones), !empty($adminEmail));

        if ($resolved['whatsapp']) {
            try {
                $this->whatsapp->notifyAdminNewOrder($order);
            } catch (\Throwable $e) {
                Log::error('Admin order WhatsApp failed: ' . $e->getMessage());
            }
        }

        if ($resolved['email']) {
            try {
                $order->load('items');
                Mail::to($adminEmail)->send(new AdminNewOrderMail($order));
            } catch (\Throwable $e) {
                Log::error('Admin order Email failed: ' . $e->getMessage());
            }
        }
    }
}
