<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('show_on_homepage')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'status']);
            $table->index('show_on_homepage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
