<?php

use App\Support\ProductSlugs;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const NAME_CHANGES = [
        'royal-oud' => ['name', 'ROYAL OUD', 'متمرد'],
        'oud-ambassador' => ['name_ar', 'Oud Ambassador', 'غرور'],
        'oud-madawi' => ['name_ar', 'Oud Madawi', 'كاسح'],
    ];

    public function up(): void
    {
        $this->renameProducts(ProductSlugs::LEGACY, self::NAME_CHANGES);
    }

    public function down(): void
    {
        $names = [];
        foreach (self::NAME_CHANGES as $slug => [$column, $from, $to]) {
            $names[ProductSlugs::LEGACY[$slug]] = [$column, $to, $from];
        }
        $this->renameProducts(array_flip(ProductSlugs::LEGACY), $names);
    }

    private function renameProducts(array $slugs, array $names): void
    {
        DB::transaction(function () use ($slugs, $names): void {
            $imagePaths = [];
            foreach ($slugs as $from => $to) {
                $oldProduct = DB::table('products')->where('slug', $from);
                if ($oldProduct->exists()) {
                    if (DB::table('products')->where('slug', $to)->exists()) {
                        throw new RuntimeException("Both product slugs {$from} and {$to} exist. Resolve the duplicate before migrating.");
                    }

                    [$column, $oldName, $newName] = $names[$from];
                    (clone $oldProduct)->where($column, $oldName)->update([$column => $newName]);
                    if ($from === 'oud-ambassador') {
                        $notes = json_decode($oldProduct->value('notes_base') ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                        if (is_array($notes) && in_array('العود المعتق الفاخر', $notes, true)) {
                            $notes = array_map(fn ($note) => $note === 'العود المعتق الفاخر' ? 'العود' : $note, $notes);
                            $oldProduct->update(['notes_base' => json_encode($notes, JSON_THROW_ON_ERROR)]);
                        }
                    }
                    $oldProduct->update(['slug' => $to]);
                }

                foreach (['reviews', 'order_items', 'cart_items'] as $table) {
                    if (Schema::hasColumn($table, 'product_slug')) {
                        DB::table($table)->where('product_slug', $from)->update(['product_slug' => $to]);
                    }
                }

                $imagePaths["/media/orq/{$from}.webp"] = "/media/orq/{$to}.webp";
            }

            DB::table('products')->select(['id', 'images'])->whereNotNull('images')->chunkById(200, function ($products) use ($imagePaths): void {
                foreach ($products as $product) {
                    $images = json_decode($product->images, true, 512, JSON_THROW_ON_ERROR);
                    if (! is_array($images)) {
                        continue;
                    }

                    $updated = array_map(fn ($path) => is_string($path) ? ($imagePaths[$path] ?? $path) : $path, $images);
                    if ($updated !== $images) {
                        DB::table('products')->where('id', $product->id)->update(['images' => json_encode($updated, JSON_THROW_ON_ERROR)]);
                    }
                }
            });

            foreach ($imagePaths as $from => $to) {
                DB::table('product_images')->where('image_path', $from)->update(['image_path' => $to]);
            }
        });
    }
};
