<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }

    /**
     * Add product to cart
     */
    public function addProduct(Product $product, int $quantity = 1)
    {
        $existingItem = $this->items()->where('product_id', $product->id)->first();
        
        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->current_price,
        ]);
    }

    /**
     * Get or create cart for user/session
     */
    public static function getOrCreate(?User $user = null, ?string $sessionId = null): self
    {
        if ($user) {
            return self::firstOrCreate(['user_id' => $user->id]);
        }

        return self::firstOrCreate(['session_id' => $sessionId]);
    }
}
