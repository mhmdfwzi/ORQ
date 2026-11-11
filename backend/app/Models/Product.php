<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'name_ar',
        'slug',
        'sku',
        'short_description',
        'short_description_ar',
        'description',
        'description_ar',
        'price',
        'sale_price',
        'offer_label',
        'offer_start_date',
        'offer_end_date',
        'is_offer_featured',
        'stock_quantity',
        'manufacturer',
        'size',
        'status',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'images',
        'views_count',
        'orders_count',
        // ORQ Perfume fields
        'subtitle',
        'original_inspiration',
        'brand_feel',
        'scent_type',
        'mood',
        'notes_top',
        'notes_heart',
        'notes_base',
        'is_best_seller',
        'is_new',
        'is_bundle',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_offer_featured' => 'boolean',
        'views_count' => 'integer',
        'orders_count' => 'integer',
        'images' => 'array',
        'offer_start_date' => 'datetime',
        'offer_end_date' => 'datetime',
        // ORQ Perfume casts
        'mood' => 'string',
        'notes_top' => 'array',
        'notes_heart' => 'array',
        'notes_base' => 'array',
        'is_best_seller' => 'boolean',
        'is_new' => 'boolean',
        'is_bundle' => 'boolean',
    ];

    /**
     * Get category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product variants (ORQ perfume size/price options).
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    /**
     * Get product images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get primary image
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews only
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    /**
     * Get order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get current price (sale or regular)
     */
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Check if in stock
     */
    public function getInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Scope for published products
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    /**
     * Scope for products filtered by scent mood
     */
    public function scopeByMood($query, string $mood)
    {
        return $query->where('mood', $mood);
    }

    /**
     * Scope for best-seller products
     */
    public function scopeBestSellers($query)
    {
        return $query->where('is_best_seller', true);
    }

    /**
     * Scope for bundle (gift box) products
     */
    public function scopeBundles($query)
    {
        return $query->where('is_bundle', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products with active offers
     */
    public function scopeActiveOffers($query)
    {
        return $query->whereNotNull('sale_price')
            ->where('sale_price', '<', \DB::raw('price'))
            ->where(function ($q) {
                $q->whereNull('offer_end_date')
                  ->orWhere('offer_end_date', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('offer_start_date')
                  ->orWhere('offer_start_date', '<=', now());
            });
    }

    /**
     * Scope for featured offers (to show in homepage)
     */
    public function scopeFeaturedOffers($query)
    {
        return $query->activeOffers()
            ->where('is_offer_featured', true)
            ->published();
    }

    /**
     * Check if product has active offer
     */
    public function hasActiveOffer(): bool
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return false;
        }

        $now = now();
        
        if ($this->offer_start_date && $this->offer_start_date > $now) {
            return false;
        }

        if ($this->offer_end_date && $this->offer_end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get time remaining for offer
     */
    public function getOfferTimeRemainingAttribute()
    {
        if (!$this->offer_end_date) {
            return null;
        }
        return $this->offer_end_date->diffForHumans();
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
