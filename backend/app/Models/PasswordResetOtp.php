<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Generate a new OTP for phone number
     */
    public static function generateFor(string $phone): self
    {
        // Invalidate old OTPs
        self::where('phone', $phone)->update(['used' => true]);
        
        // Generate new 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        return self::create([
            'phone' => $phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Verify OTP
     */
    public static function verify(string $phone, string $otp): bool
    {
        $record = self::where('phone', $phone)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
        
        if ($record) {
            $record->update(['used' => true]);
            return true;
        }
        
        return false;
    }
}
