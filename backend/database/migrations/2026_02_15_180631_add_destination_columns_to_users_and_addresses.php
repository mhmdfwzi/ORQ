<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'governorate_id')) {
                $table->unsignedBigInteger('governorate_id')->nullable()->after('phone_verified_at');
            }
            if (!Schema::hasColumn('users', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('governorate_id');
            }
            if (!Schema::hasColumn('users', 'street_address')) {
                $table->text('street_address')->nullable()->after('city_id');
            }
            
            // Re-adding constraints just in case they were missed
            $table->foreign('governorate_id')->references('id')->on('destinations')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('destinations')->nullOnDelete();
        });

        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'governorate_id')) {
                $table->unsignedBigInteger('governorate_id')->nullable()->after('country');
            }
            if (!Schema::hasColumn('addresses', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('governorate_id');
            }
            
            $table->foreign('governorate_id')->references('id')->on('destinations')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('destinations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['governorate_id', 'city_id', 'street_address']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['governorate_id', 'city_id']);
        });
    }
};
