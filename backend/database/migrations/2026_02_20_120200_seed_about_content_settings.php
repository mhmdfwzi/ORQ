<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'key' => 'about_who_we_are',
                'value' => '<strong>Medicamo</strong> is a smart medical solutions company operating in the fields of:
<ul>
<li>Dietary Supplements</li>
<li>Medical Cosmeceuticals</li>
<li>Third-Party Manufacturing (Contract Manufacturing)</li>
</ul>',
            ],
            [
                'key' => 'about_vision',
                'value' => 'To become one of the leading Egyptian companies in the fields of dietary supplements, medical cosmeceuticals, and pharmaceuticals, recognized for product quality, innovation, and a science-driven approach.',
            ],
            [
                'key' => 'about_mission',
                'value' => 'At Medicamo, we are committed to delivering safe, effective, and scientifically grounded health and beauty products through high-quality manufacturing, professional management, and strong partnerships.',
            ],
            [
                'key' => 'about_home_image',
                'value' => '',
            ],
            [
                'key' => 'about_page_image',
                'value' => '',
            ],
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
            'about_who_we_are',
            'about_vision',
            'about_mission',
            'about_home_image',
            'about_page_image',
        ])->delete();
    }
};
