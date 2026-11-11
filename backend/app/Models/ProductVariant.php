<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product variant representing a specific size/price option for a perfume product.
 *
 * @property int $id
 * @property int $product_id
 * @property string $size
 * @property string $price
 * @property string|null $sale_price
 * @property int $stock_quantity
 * @property string|null $sku
 * @property bool $is_active
 * @property int $sort_order
 * @property-read bool $in_stock
 * @property-read string $current_price
 * @property-read Product $product
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Determine if this variant is in stock.
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get the current effective price (sale price if available, otherwise regular price).
     */
    public function getCurrentPriceAttribute(): string
    {
        return $this->sale_price ?? $this->price;
    }
}
