<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('payment_checkout_key')->nullable()->unique();
            $table->char('payment_request_hash', 64)->nullable();
            $table->text('paymob_checkout_url')->nullable();
            $table->string('paymob_order_id', 32)->nullable()->unique();
            $table->string('paymob_integration_id', 32)->nullable();
            $table->string('paymob_transaction_id', 32)->nullable()->unique();
            $table->timestamp('paymob_expires_at')->nullable()->index();
            $table->timestamp('paymob_released_at')->nullable();
            $table->boolean('paymob_declined')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['payment_checkout_key']);
            $table->dropUnique(['paymob_order_id']);
            $table->dropUnique(['paymob_transaction_id']);
            $table->dropIndex(['paymob_expires_at']);
            $table->dropColumn([
                'payment_checkout_key', 'payment_request_hash', 'paymob_checkout_url', 'paymob_order_id',
                'paymob_integration_id', 'paymob_transaction_id', 'paymob_expires_at', 'paymob_released_at', 'paymob_declined',
            ]);
        });
    }
};
