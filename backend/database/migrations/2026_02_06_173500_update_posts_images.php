<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update blog posts with product images
        DB::table('posts')
            ->where('slug', 'essential-vitamins-immune-system')
            ->update(['image' => 'products/01KEAYHMAV2Q4DTPM4HPT0E12V.jpeg']);
        
        DB::table('posts')
            ->where('slug', 'complete-guide-skincare-products')
            ->update(['image' => 'products/01KEAYWP353ZNX492BGDJB17MY.jpeg']);
        
        DB::table('posts')
            ->where('slug', 'natural-remedies-vs-modern-medicine')
            ->update(['image' => 'products/01KEB0C0XYA1EHZB1E5VRC5MJ9.jpeg']);
    }

    public function down(): void
    {
        DB::table('posts')->update(['image' => null]);
    }
};
