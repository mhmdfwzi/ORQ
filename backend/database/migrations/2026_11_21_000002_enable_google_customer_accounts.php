<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('users')->select('google_id')->whereNotNull('google_id')->groupBy('google_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Resolve duplicate Google identities before enabling customer Google sign-in.');
        }

        $hasUniqueGoogleId = Schema::hasIndex('users', ['google_id'], 'unique');
        Schema::table('users', function (Blueprint $table) use ($hasUniqueGoogleId): void {
            $table->string('phone')->nullable()->change();
            if (! $hasUniqueGoogleId) {
                $table->unique('google_id');
            }
        });
    }

    public function down(): void
    {
        if (DB::table('users')->whereNull('phone')->exists()) {
            throw new RuntimeException('Customer accounts without a phone must be retained.');
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone')->nullable(false)->change();
            $table->dropUnique(['google_id']);
        });
    }
};
