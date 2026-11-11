<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'status',
        'show_on_homepage',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'show_on_homepage' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Approve the review
     */
    public function approve(bool $showOnHomepage = false): void
    {
        $this->update([
            'status' => 'approved',
            'show_on_homepage' => $showOnHomepage,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the review
     */
    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending reviews
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for homepage reviews
     */
    public function scopeForHomepage($query)
    {
        return $query->approved()->where('show_on_homepage', true);
    }
}
