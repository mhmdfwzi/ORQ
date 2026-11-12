<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform a ProductVariant model into the JSON shape expected by the Next.js frontend.
 */
class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'size'           => $this->size,
            // Effective (selling) price; compareAtPrice is the struck-through list price when on sale.
            'price'          => (float) ($this->sale_price ?? $this->price),
            'compareAtPrice' => ($this->sale_price !== null && (float) $this->sale_price < (float) $this->price) ? (float) $this->price : null,
            'inStock'        => $this->in_stock,
        ];
    }
}
