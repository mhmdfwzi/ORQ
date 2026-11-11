<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('manufacturer')->nullable();
            $table->string('size')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            // Stats
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'is_active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
