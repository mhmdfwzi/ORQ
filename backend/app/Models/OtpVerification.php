<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
        'type',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP is valid
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Generate new OTP for user
     */
    public static function generateFor(User $user, string $type = 'registration'): self
    {
        // Invalidate existing OTPs
        self::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        return self::create([
            'user_id' => $user->id,
            'otp' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
