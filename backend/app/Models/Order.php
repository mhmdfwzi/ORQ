<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'shipping_governorate',
        'shipping_address',
        'user_id',
        'address_id',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'tracking_number',
        'shipping_carrier',
        'notes',
        'status_notes',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = config('orq.order_prefix', 'ORQ') . '-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Customer name: guest field first, registered user as fallback.
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->customer_name ?: ($this->user?->name ?? 'N/A');
    }

    /**
     * Customer phone: guest field first, registered user as fallback.
     */
    public function getCustomerDisplayPhoneAttribute(): string
    {
        return $this->customer_phone ?: ($this->user?->phone ?? 'N/A');
    }

    /**
     * Human readable shipping address for guest or registered orders.
     */
    public function getShippingAddressTextAttribute(): string
    {
        if ($this->shipping_address) {
            return trim(($this->shipping_governorate ? $this->shipping_governorate . ' - ' : '') . $this->shipping_address);
        }

        return $this->address ? $this->address->full_address : 'N/A';
    }

    /**
     * Update order status
     */
    public function updateStatus(string $status): void
    {
        $this->status = $status;

        switch ($status) {
            case 'confirmed':
                $this->confirmed_at = now();
                break;
            case 'shipped':
                $this->shipped_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                break;
        }

        $this->save();
    }

    /**
     * Get status timeline for tracking
     */
    public function getStatusTimeline(): array
    {
        return [
            [
                'status' => 'pending',
                'label' => 'Order Placed',
                'icon' => 'fa-shopping-cart',
                'completed' => true,
                'date' => $this->created_at,
            ],
            [
                'status' => 'confirmed',
                'label' => 'Order Confirmed',
                'icon' => 'fa-check-circle',
                'completed' => in_array($this->status, ['confirmed', 'processing', 'shipped', 'delivered']),
                'date' => $this->confirmed_at,
            ],
            [
                'status' => 'processing',
                'label' => 'Processing',
                'icon' => 'fa-cogs',
                'completed' => in_array($this->status, ['processing', 'shipped', 'delivered']),
                'date' => null,
            ],
            [
                'status' => 'shipped',
                'label' => 'Shipped',
                'icon' => 'fa-truck',
                'completed' => in_array($this->status, ['shipped', 'delivered']),
                'date' => $this->shipped_at,
            ],
            [
                'status' => 'delivered',
                'label' => 'Delivered',
                'icon' => 'fa-home',
                'completed' => $this->status === 'delivered',
                'date' => $this->delivered_at,
            ],
        ];
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'light',
        };
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

