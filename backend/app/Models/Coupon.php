<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'per_user_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get coupon usages
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(?float $orderTotal = null, ?int $userId = null): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        // Check expiry date
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        // Check minimum order amount
        if ($orderTotal && $this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return false;
        }

        // Check per user limit
        if ($userId && $this->per_user_limit) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $orderTotal * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        // Apply max discount limit
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        // Don't exceed order total
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return round($discount, 2);
    }

    /**
     * Get formatted discount display
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return '$' . number_format($this->value, 2);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
