<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultFeatures = [
            [
                'icon' => 'fas fa-certificate',
                'title_en' => 'Guaranteed Quality',
                'title_ar' => 'جودة مضمونة',
                'desc_en' => 'All our products are authentic and certified by health authorities with full quality guarantee',
                'desc_ar' => 'جميع منتجاتنا أصلية ومعتمدة من السلطات الصحية مع ضمان الجودة الكامل'
            ],
            [
                'icon' => 'fas fa-tag',
                'title_en' => 'Competitive Prices',
                'title_ar' => 'أسعار تنافسية',
                'desc_en' => 'We offer you the best prices in the market with exclusive offers and discounts year-round',
                'desc_ar' => 'نقدم لك أفضل الأسعار في السوق مع عروض وخصومات حصرية على مدار العام'
            ],
            [
                'icon' => 'fas fa-shipping-fast',
                'title_en' => 'Fast Delivery',
                'title_ar' => 'توصيل سريع',
                'desc_en' => 'Fast and secure delivery service to all regions with real-time order tracking',
                'desc_ar' => 'خدمة توصيل سريعة وآمنة لجميع المناطق مع تتبع الطلب في الوقت الفعلي'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'title_en' => 'Secure Payment',
                'title_ar' => 'دفع آمن',
                'desc_en' => 'Secure and encrypted payment system with multiple payment options including cash on delivery',
                'desc_ar' => 'نظام دفع آمن ومشفر مع خيارات دفع متعددة بما في ذلك الدفع عند الاستلام'
            ]
        ];

        DB::table('settings')->insert([
            [
                'key' => 'why_choose_title_en',
                'value' => 'Why Choose Medicamo?',
                'group' => 'about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'why_choose_title_ar',
                'value' => 'لماذا تختار ميديكامو؟',
                'group' => 'about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'why_choose_subtitle_en',
                'value' => 'We offer you the best pharmaceutical shopping experience',
                'group' => 'about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'why_choose_subtitle_ar',
                'value' => 'نقدم لك أفضل تجربة تسوق للمنتجات الطبية',
                'group' => 'about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'why_choose_features',
                'value' => json_encode($defaultFeatures),
                'group' => 'about',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', [
                'why_choose_title_en',
                'why_choose_title_ar',
                'why_choose_subtitle_en',
                'why_choose_subtitle_ar',
                'why_choose_features'
            ])
            ->delete();
    }
};
