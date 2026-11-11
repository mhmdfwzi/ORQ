<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'about_who_we_are_ar', 'value' => ''],
            ['key' => 'about_vision_ar', 'value' => ''],
            ['key' => 'about_mission_ar', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'about_who_we_are_ar',
            'about_vision_ar',
            'about_mission_ar',
        ])->delete();
    }
};
