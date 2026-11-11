<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds the ORQ perfume catalogue (12 perfumes + gift bundle) from
 * database/seeders/data/orq_products.json, exported from the frontend's
 * original hardcoded data. Idempotent: re-running updates by slug.
 */
class ORQProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = json_decode(
            file_get_contents(database_path('seeders/data/orq_products.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        // Skip the revalidation observer while bulk seeding.
        Product::withoutEvents(fn () => DB::transaction(function () use ($items) {
            foreach ($items as $i => $item) {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($item['category'])],
                    ['name' => $item['category'], 'is_active' => true]
                );

                $product = Product::withTrashed()->updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'category_id'          => $category->id,
                        'name'                 => $item['name'],
                        'name_ar'              => $item['arabicName'],
                        'subtitle'             => $item['subtitle'],
                        'original_inspiration' => $item['originalInspiration'],
                        'brand_feel'           => $item['brandFeel'],
                        'scent_type'           => $item['scentType'],
                        'description'          => $item['description'],
                        'sku'                  => 'ORQ-' . strtoupper($item['slug']),
                        'price'                => $item['variants'][0]['price'],
                        'stock_quantity'       => 100,
                        'mood'                 => $item['mood'],
                        'notes_top'            => $item['notes']['top'],
                        'notes_heart'          => $item['notes']['heart'],
                        'notes_base'           => $item['notes']['base'],
                        'is_best_seller'       => $item['isBestSeller'] ?? false,
                        'is_new'               => $item['isNew'] ?? false,
                        'is_bundle'            => $item['isBundle'] ?? false,
                        'images'               => [$item['image']],
                        'status'               => 'published',
                        'is_active'            => true,
                        'deleted_at'           => null,
                    ]
                );

                foreach ($item['variants'] as $order => $variant) {
                    $product->variants()->updateOrCreate(
                        ['size' => $variant['size']],
                        [
                            'price'          => $variant['price'],
                            'stock_quantity' => ($variant['inStock'] ?? true) ? 100 : 0,
                            'sku'            => 'ORQ-' . strtoupper($item['slug']) . '-' . $variant['size'],
                            'is_active'      => true,
                            'sort_order'     => $order,
                        ]
                    );
                }
            }
        }));
    }
}
