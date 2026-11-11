<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive changes enabling guest checkout (no account, no saved address)
 * and per-size (variant) order lines.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('address_id')->nullable()->change();

            $table->string('customer_name')->nullable()->after('order_number');
            $table->string('customer_phone', 20)->nullable()->after('customer_name');
            $table->string('shipping_governorate')->nullable()->after('customer_phone');
            $table->text('shipping_address')->nullable()->after('shipping_governorate');

            $table->index('customer_phone');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')
                ->constrained('product_variants')->nullOnDelete();
            $table->string('size', 10)->nullable()->after('product_sku');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
            $table->dropColumn('size');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_phone']);
            $table->dropColumn(['customer_name', 'customer_phone', 'shipping_governorate', 'shipping_address']);
        });
    }
};
