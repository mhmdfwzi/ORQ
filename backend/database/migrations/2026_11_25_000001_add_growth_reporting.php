<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('analytics_session_id')->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->index('created_at');
        });

        Schema::create('storefront_funnel_events', function (Blueprint $table) {
            $table->uuid('session_id');
            $table->date('day');
            $table->string('stage', 20);
            $table->primary(['day', 'stage', 'session_id']);
        });

        Schema::create('campaign_spends', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->string('utm_source', 100);
            $table->string('utm_medium', 100);
            $table->string('utm_campaign', 100);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
            $table->unique(['day', 'utm_source', 'utm_medium', 'utm_campaign'], 'campaign_spends_daily_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_spends');
        Schema::dropIfExists('storefront_funnel_events');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropColumn(['analytics_session_id', 'utm_source', 'utm_medium', 'utm_campaign']);
        });
    }
};
