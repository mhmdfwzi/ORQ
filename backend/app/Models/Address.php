<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'full_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'governorate_id',
        'city_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function governorate()
    {
        return $this->belongsTo(\App\Models\Destination::class, 'governorate_id');
    }

    public function destination()
    {
        return $this->belongsTo(\App\Models\Destination::class, 'city_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get full address string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Set as default address
     */
    public function setAsDefault(): void
    {
        // Unset all other defaults for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
