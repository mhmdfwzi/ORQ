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
            'size'    => $this->size,
            'price'   => (float) $this->price,
            'inStock' => $this->in_stock,
        ];
    }
}
