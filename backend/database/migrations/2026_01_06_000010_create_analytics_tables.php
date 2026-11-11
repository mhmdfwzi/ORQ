<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'created_at']);
        });

        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('activity_type'); // page_view, product_view, add_to_cart, checkout, etc.
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45);
            $table->timestamps();
            
            $table->index(['activity_type', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('product_views');
    }
};
