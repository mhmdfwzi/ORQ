<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Just make phone nullable without touching the unique index
        DB::statement('ALTER TABLE users MODIFY phone VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY phone VARCHAR(255) NOT NULL');
    }
};
