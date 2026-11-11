<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add ORQ perfume-specific fields to the products table.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('name_ar');
            $table->string('original_inspiration')->nullable()->after('subtitle');
            $table->string('brand_feel')->nullable()->after('original_inspiration');
            $table->string('scent_type')->nullable()->after('brand_feel');
            $table->enum('mood', ['royal_oud', 'night_allure', 'sporty_fresh', 'warm_formal'])
                  ->nullable()
                  ->index()
                  ->after('scent_type');
            $table->json('notes_top')->nullable()->after('mood');
            $table->json('notes_heart')->nullable()->after('notes_top');
            $table->json('notes_base')->nullable()->after('notes_heart');
            $table->boolean('is_best_seller')->default(false)->after('notes_base');
            $table->boolean('is_new')->default(false)->after('is_best_seller');
            $table->boolean('is_bundle')->default(false)->after('is_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['mood']);
            $table->dropColumn([
                'subtitle',
                'original_inspiration',
                'brand_feel',
                'scent_type',
                'mood',
                'notes_top',
                'notes_heart',
                'notes_base',
                'is_best_seller',
                'is_new',
                'is_bundle',
            ]);
        });
    }
};
