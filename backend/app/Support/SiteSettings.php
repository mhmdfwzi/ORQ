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
            'tagline' => 'HAUTE PARFUMERIE',
            'motto' => 'OWN YOUR PRESENCE — عطور رجالية فقط',

            'seo' => [
                'title_default' => 'ORQ Haute Parfumerie | عطور النيش الرجالية الفاخرة',
                'title_template' => '%s | ORQ Haute Parfumerie',
                'description' => 'دار عطور نيش فاخرة متخصصة في أرقى العطور الرجالية المعتقة بتركيزات Eau de Parfum تدوم لأكثر من 12 ساعة. استكشف تشكيلة عطور نادرة مع شحن سريع لجميع المحافظات والدفع عند الاستلام وعينة تجربة مجانية.',
                'og_description' => 'حضورك له أثر لا يمحى من الذاكرة • استكشف عطور نيش معتقة بتركيزات تدوم لأكثر من 12 ساعة مع شحن سريع ودفع عند الاستلام وعينة تجربة مجانية.',
                'twitter_description' => 'حضورك له أثر لا يمحى من الذاكرة • عطور رجالية بتركيز نيش مكثف وشحن سريع لكافة المحافظات.',
                'keywords' => [
                    'عطور رجالية', 'عطور نيش', 'عطور فاخرة', 'ORQ', 'عطور مصر', 'عطور ثابتة', 'عطور فواحة',
                    'أفضل عطور رجالية', 'Haute Parfumerie Egypt', 'Luxury Men Fragrances Cairo',
                ],
                'twitter_handle' => '@ORQperfumes',
                'organization_description' => 'دار عطور نيش فاخرة متخصصة في أرقى العطور الرجالية المعتقة بتركيزات مكثفة تدوم لأكثر من 12 ساعة.',
            ],

            'footer' => [
                'description' => 'دار عطور رجالية متخصصة تصنع الحضور والهيبة من خلال خلاصات عطرية فاخرة. لأن العطر الحقيقي ليس مجرد رائحة.. بل بصمة تسبق كلماتك.',
                'links_title' => 'المجموعات العطرية',
                'support_title' => 'خدمة العملاء والاستفسارات',
                'support_text' => 'فريق العناية بالعملاء متاح يومياً لمساعدتك في اختيار العطر الأنسب لك أو كهدية فاخرة.',
                'support_badge' => 'خدمة عملاء ORQ المباشرة',
                'copyright' => '© 2026 ORQ Haute Parfumerie. جميع الحقوق محفوظة.',
                'legal_links' => ['سياسة الاستبدال والاسترجاع', 'الشروط والأحكام', 'سياسة الخصوصية'],
                // icon: sample | delivery | cod | oils
                'trust_features' => [
                    ['icon' => 'sample', 'title' => 'عينة تجربة مجانية', 'desc' => 'اختبر العطر أولاً من العينة المرفقة قبل فتح الزجاجة الأصلية.'],
                    ['icon' => 'delivery', 'title' => 'توصيل سريع ومؤمّن', 'desc' => 'تغليف فاخر ومحكم يصلك إلى باب بيتك خلال 24 إلى 48 ساعة.'],
                    ['icon' => 'cod', 'title' => 'الدفع عند الاستلام (COD)', 'desc' => 'عاين الشحنة واستلم طلبك براحة تامة ثم ادفع نقداً.'],
                    ['icon' => 'oils', 'title' => 'زيوت نيش أصلية نادرة', 'desc' => 'تركيزات Eau de Parfum تدوم وتفوح لأكثر من 12 ساعة.'],
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
                'eyebrow' => 'ORQ HAUTE PARFUMERIE',
                'quote' => 'حضورك له أثر لا يمحى من الذاكرة',
                'text' => 'نصنع خلطاتنا من خلاصات الزيوت النادرة بتركيزات مكثفة تدوم لأكثر من 12 ساعة لتمنحك كاريزما واثقة وهادئة.',
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
