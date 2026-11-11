<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs_listing', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->text('requirements')->nullable();
            $table->text('requirements_ar')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['full-time', 'part-time', 'remote', 'contract'])->default('full-time');
            $table->string('department')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('deadline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs_listing');
    }
};
