<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Removes legacy (pharmacy) brand content that earlier seed
 * migrations insert into `settings`, so any ORQ database ends up brand-clean
 * (fresh installs included, since this runs after those seed migrations).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->where(function ($q) {
                $q->where('key', 'like', 'about\_%')
                  ->orWhere('key', 'like', 'why\_choose\_%')
                  ->orWhere('value', 'like', '%medicamo%')
                  ->orWhere('value', 'like', '%mdcamo%')
                  ->orWhere('value', 'like', '%ميديكامو%');
            })
            ->delete();
    }

    public function down(): void
    {
        // Intentionally empty: legacy brand content is not restored.
    }
};

