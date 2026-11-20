<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform a Product model into the JSON shape expected by the Next.js frontend.
 *
 * Maps snake_case database columns to camelCase keys matching the frontend
 * Product TypeScript interface.
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $gallery = array_map($this->imageUrl(...), $this->resource->galleryPaths());

        return [
            'slug'                => $this->slug,
            'name'                => $this->name,
            'arabicName'          => $this->name_ar,
            'subtitle'            => $this->subtitle,
            'originalInspiration' => $this->original_inspiration,
            'category'            => $this->category?->name,
            'brandFeel'           => $this->brand_feel,
            'scentType'           => $this->scent_type,
            'description'         => $this->description,
            'mood'                => $this->mood,
            'notes'               => [
                'top'   => $this->notes_top ?? [],
                'heart' => $this->notes_heart ?? [],
                'base'  => $this->notes_base ?? [],
            ],
            'variants'            => ProductVariantResource::collection(
                $this->whenLoaded('variants')
            ),
            'image'               => $gallery[0] ?? null,
            'gallery'             => $gallery,
            'isBestSeller'        => (bool) $this->is_best_seller,
            'isNew'               => (bool) $this->is_new,
        ];
    }

    /**
     * Resolve the main image to something the browser can load.
     *
     * Frontend-served assets ("/media/...") and absolute URLs pass through
     * unchanged; files uploaded via Filament (storage paths) become absolute
     * URLs on the public disk.
     */
    private function imageUrl(string $path): string
    {
        if (str_starts_with($path, '/') || preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
