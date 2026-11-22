<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ARCHIVE = 'products_original_inspiration_backup';

    public function up(): void
    {
        if (! Schema::hasTable(self::ARCHIVE)) {
            Schema::create(self::ARCHIVE, function (Blueprint $table): void {
                $table->unsignedBigInteger('product_id')->primary();
                $table->string('original_inspiration');
            });
        }

        DB::transaction(function (): void {
            DB::table('products')->select(['id', 'original_inspiration'])->whereNotNull('original_inspiration')
                ->chunkById(200, function ($products): void {
                    $rows = $products->map(fn ($product) => [
                        'product_id' => $product->id,
                        'original_inspiration' => $product->original_inspiration,
                    ])->all();
                    DB::table(self::ARCHIVE)->insertOrIgnore($rows);
                    DB::table('products')->whereIn('id', $products->pluck('id'))->update(['original_inspiration' => null]);
                });
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            DB::table(self::ARCHIVE)->chunkById(200, function ($products): void {
                foreach ($products as $product) {
                    DB::table('products')->where('id', $product->product_id)->whereNull('original_inspiration')
                        ->update(['original_inspiration' => $product->original_inspiration]);
                }
            }, 'product_id');
        });

        Schema::drop(self::ARCHIVE);
    }
};
