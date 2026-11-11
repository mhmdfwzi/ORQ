<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('offer_label')->nullable()->after('sale_price');
            $table->timestamp('offer_start_date')->nullable()->after('offer_label');
            $table->timestamp('offer_end_date')->nullable()->after('offer_start_date');
            $table->boolean('is_offer_featured')->default(false)->after('offer_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['offer_label', 'offer_start_date', 'offer_end_date', 'is_offer_featured']);
        });
    }
};
