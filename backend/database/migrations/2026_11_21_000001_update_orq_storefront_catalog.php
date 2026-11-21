<?php

use App\Support\SiteSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $catalog = json_decode(
            file_get_contents(database_path('seeders/data/orq_catalog_20261121.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($catalog): void {
            DB::table('product_variants')->where('size', '100ml')->where('is_active', true)->update(['is_active' => false]);

            foreach ($catalog as $item) {
                $slugs = array_filter([$item['slug'], $item['old_slug']]);
                $productId = DB::table('products')->whereIn('slug', $slugs)->whereNull('deleted_at')
                    ->orderByRaw('CASE WHEN slug = ? THEN 0 ELSE 1 END', [$item['slug']])->value('id');

                if ($productId === null) {
                    continue;
                }

                $description = implode("\n\n", $item['description']);
                DB::table('products')->where('id', $productId)->update([
                    'sku' => $item['sku'],
                    'description' => $description,
                    'description_ar' => $description,
                    'meta_title' => $item['meta_title'],
                    'meta_description' => $item['meta_description'],
                ]);
                DB::table('product_variants')->where('product_id', $productId)->where('size', '50ml')
                    ->update(['sku' => $item['sku'].'-50ml']);
            }

            $this->updateSiteCopy();
        });
    }

    private function updateSiteCopy(): void
    {
        $setting = DB::table('settings')->where('key', 'site_settings');
        $stored = json_decode((string) $setting->value('value'), true);
        if (! is_array($stored)) {
            return;
        }

        $defaults = SiteSettings::defaults();
        $outdated = fn ($value): bool => is_string($value) && preg_match('/نيش|haute parfumerie|12\s*ساعة/iu', $value) === 1;

        foreach (['title_default', 'description', 'og_description', 'twitter_description', 'organization_description'] as $key) {
            if ($outdated($stored['seo'][$key] ?? null)) {
                $stored['seo'][$key] = $defaults['seo'][$key];
            }
        }
        if (is_array($stored['seo']['keywords'] ?? null)) {
            $stored['seo']['keywords'] = array_values(array_filter($stored['seo']['keywords'], fn ($value) => ! $outdated($value)));
        }
        if ($outdated($stored['tagline'] ?? null)) {
            $stored['tagline'] = $defaults['tagline'];
        }
        foreach (['eyebrow', 'text'] as $key) {
            if ($outdated($stored['creed'][$key] ?? null)) {
                $stored['creed'][$key] = $defaults['creed'][$key];
            }
        }
        foreach ([['seo', 'title_template'], ['footer', 'copyright']] as [$group, $key]) {
            if (is_string($stored[$group][$key] ?? null)) {
                $stored[$group][$key] = str_ireplace('ORQ Haute Parfumerie', 'ORQ', $stored[$group][$key]);
            }
        }
        foreach ($stored['footer']['trust_features'] ?? [] as $index => $feature) {
            if (($feature['icon'] ?? null) === 'oils' && $outdated($feature['title'] ?? null)) {
                $stored['footer']['trust_features'][$index]['title'] = 'خلاصات منتقاة بعناية';
            }
            if ($outdated($feature['desc'] ?? null)) {
                $stored['footer']['trust_features'][$index]['desc'] = 'صُمم ليدوم معك طوال اليوم.';
            }
        }

        $setting->update(['value' => json_encode($stored, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);
    }

    public function down(): void
    {
        // Keep the new catalogue copy and the archived variant records on rollback.
    }
};
