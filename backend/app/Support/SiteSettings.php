<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Storefront-wide business content kept in the database (settings.site_settings)
 * and edited from the Filament "إعدادات الموقع" page: identity, SEO, footer,
 * moods, shipping/payment, governorates, brand creed and bundle highlights.
 *
 * Defaults are only used until an admin saves the page.
 */
class SiteSettings
{
    public const KEY = 'site_settings';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'name' => 'ORQ',
            'tagline' => 'LET EGO OWN SPACE',
            'motto' => 'OWN YOUR PRESENCE — عطور رجالية فقط',

            'seo' => [
                'title_default' => 'ORQ | عطور رجالية فاخرة',
                'title_template' => '%s | ORQ',
                'description' => 'اكتشف 12 شخصية عطرية صاغتها ORQ وفق شفرة الأصالة والندرة والجودة. توصيل لجميع محافظات مصر ودفع عند الاستلام.',
                'og_description' => 'اكتشف عطور ORQ الرجالية المصاغة وفق شفرة الأصالة والندرة والجودة.',
                'twitter_description' => 'اكتشف عطور ORQ الرجالية المصاغة وفق شفرة الأصالة والندرة والجودة.',
                'keywords' => [
                    'عطور رجالية', 'عطور فاخرة', 'ORQ', 'عطور مصر', 'عطور ثابتة', 'عطور فواحة',
                    'أفضل عطور رجالية', 'Luxury Men Fragrances Cairo',
                ],
                'twitter_handle' => '@ORQperfumes',
                'organization_description' => 'عطور رجالية فاخرة مصاغة وفق شفرة ORQ: الأصالة والندرة والجودة.',
            ],

            'footer' => [
                'description' => 'دار عطور رجالية متخصصة تصنع الحضور والهيبة من خلال خلاصات عطرية فاخرة. لأن العطر الحقيقي ليس مجرد رائحة.. بل بصمة تسبق كلماتك.',
                'links_title' => 'المجموعات العطرية',
                'support_title' => 'خدمة العملاء والاستفسارات',
                'support_text' => 'فريق العناية بالعملاء متاح يومياً لمساعدتك في اختيار العطر الأنسب لك أو كهدية فاخرة.',
                'support_badge' => 'خدمة عملاء ORQ المباشرة',
                'copyright' => '© 2026 ORQ. جميع الحقوق محفوظة.',
                'legal_links' => ['سياسة الاستبدال والاسترجاع', 'الشروط والأحكام', 'سياسة الخصوصية'],
                // icon: sample | delivery | cod | oils
                'trust_features' => [
                    ['icon' => 'sample', 'title' => 'عينة تجربة مجانية', 'desc' => 'اختبر العطر أولاً من العينة المرفقة قبل فتح الزجاجة الأصلية.'],
                    ['icon' => 'delivery', 'title' => 'توصيل لجميع المحافظات', 'desc' => 'نغلّف كل طلب لحماية الزجاجة. راجع سياسة الشحن لمعرفة المدة المتوقعة حسب محافظتك.'],
                    ['icon' => 'cod', 'title' => 'الدفع عند الاستلام (COD)', 'desc' => 'عاين الشحنة واستلم طلبك براحة تامة ثم ادفع نقداً.'],
                    ['icon' => 'oils', 'title' => 'خلاصات منتقاة بعناية', 'desc' => 'صُمم ليدوم معك طوال اليوم.'],
                ],
            ],

            'moods' => [
                'all_label' => 'جميع العطور',
                'items' => [
                    ['id' => 'royal_oud', 'label' => 'عود وهيبة ملكية', 'icon' => '👑'],
                    ['id' => 'night_allure', 'label' => 'جذاب وسهرات', 'icon' => '⚡'],
                    ['id' => 'sporty_fresh', 'label' => 'منعش ورياضي', 'icon' => '🌊'],
                    ['id' => 'warm_formal', 'label' => 'رسمي ودافئ', 'icon' => '💼'],
                ],
            ],

            'commerce' => [
                'currency' => 'ج.م',
                'shipping_cost' => 0,
                'shipping_note' => 'الشحن لجميع المحافظات',
                'shipping_free_label' => 'شحن مجاني',
                'payment_note' => 'الدفع كاش عند الاستلام',
                'governorates' => [
                    'القاهرة', 'الجيزة', 'الإسكندرية', 'القليوبية', 'الشرقية', 'الدقهلية', 'الغربية', 'المنوفية',
                    'البحيرة', 'كفر الشيخ', 'دمياط', 'بورسعيد', 'الإسماعيلية', 'السويس', 'شمال سيناء', 'جنوب سيناء',
                    'الفيوم', 'بني سويف', 'المنيا', 'أسيوط', 'سوهاج', 'قنا', 'الأقصر', 'أسوان', 'البحر الأحمر',
                    'الوادي الجديد', 'مطروح',
                ],
            ],

            'creed' => [
                'eyebrow' => 'ORQ',
                'quote' => 'حضورك له أثر لا يمحى من الذاكرة',
                'text' => 'نصنع عطورنا من خلاصات منتقاة بعناية بتركيز Eau de Parfum. صُمم ليدوم معك طوال اليوم.',
            ],

            'bundle' => [
                'badge' => 'باقة الهدايا والتجربة المتكاملة',
                'cta' => 'طلب الباقة الآن',
                'highlights' => [
                    ['title' => 'زجاجة 50 مل', 'desc' => 'عطرك المفضل'],
                    ['title' => 'معطر سيارة', 'desc' => 'ثبات يدوم طويلاً'],
                    ['title' => '4 عينات مجانية', 'desc' => 'لاكتشاف التشكيلة'],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function get(): array
    {
        $stored = json_decode((string) Setting::getValue(self::KEY, ''), true);

        return is_array($stored) ? self::merge(self::defaults(), $stored) : self::defaults();
    }

    /**
     * Merge stored values over defaults: associative arrays merge key by key,
     * while lists (e.g. keywords, governorates) are replaced wholesale so that
     * removing an item in the admin never resurrects the default one.
     *
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function merge(array $defaults, array $stored): array
    {
        foreach ($stored as $key => $value) {
            if (is_array($value) && !array_is_list($value) && isset($defaults[$key]) && is_array($defaults[$key])) {
                $defaults[$key] = self::merge($defaults[$key], $value);
            } else {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    /** Flat shipping fee used for orders (single source: the settings document). */
    public static function shippingCost(): float
    {
        return (float) (self::get()['commerce']['shipping_cost'] ?? 0);
    }

    /** @param array<string, mixed> $data */
    public static function save(array $data): void
    {
        Setting::setValue(self::KEY, json_encode($data, JSON_UNESCAPED_UNICODE), 'site');
    }
}
