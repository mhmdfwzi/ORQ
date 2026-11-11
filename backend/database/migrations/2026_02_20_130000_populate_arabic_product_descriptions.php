<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $translations = [
            // Product 1: Parfaite Massage Gel
            [
                'slug' => 'parfaite-massage-gel',
                'short_description_ar' => 'جل مساج علاجي لتخفيف آلام العضلات والمفاصل',
                'description_ar' => '<p>جل مساج بارفيت الفاخر مصمم خصيصاً لتوفير راحة فعالة من آلام العضلات والمفاصل. مصنوع من مكونات طبيعية تتغلغل بعمق في الأنسجة.</p><ul><li>تركيبة سريعة المفعول</li><li>راحة طويلة الأمد</li><li>مكونات طبيعية</li></ul>',
            ],
            // Product 2: Skin Repair Serum
            [
                'slug' => 'skin-repair-serum',
                'short_description_ar' => 'سيروم متطور لإصلاح البشرة وتجديد الخلايا',
                'description_ar' => '<p>سيروم إصلاح البشرة المتطور يستخدم تقنيات حديثة لتعزيز تجديد الخلايا وإصلاح البشرة. مثالي للاستخدام اليومي للحفاظ على بشرة شابة وصحية.</p>',
            ],
            // Product 3: Pain Relief Cream
            [
                'slug' => 'pain-relief-cream',
                'short_description_ar' => 'كريم فعال لتخفيف آلام العضلات والمفاصل',
                'description_ar' => '<p>كريم مسكن سريع المفعول يوفر راحة فورية. مثالي لالتهاب المفاصل وإجهاد العضلات وآلام المفاصل.</p>',
            ],
            // Product 4: Hydrating Face Cream
            [
                'slug' => 'hydrating-face-cream',
                'short_description_ar' => 'ترطيب عميق للبشرة الجافة والحساسة',
                'description_ar' => '<p>كريم ترطيب مكثف مصمم للبشرة الجافة والحساسة. يوفر حماية ترطيبية على مدار 24 ساعة.</p>',
            ],
            // Product 5: Vitamin D3 Capsules
            [
                'slug' => 'vitamin-d3',
                'short_description_ar' => 'فيتامين د3 أساسي لصحة العظام والمناعة',
                'description_ar' => '<p>مكمل فيتامين د3 عالي الجودة لدعم صحة العظام ووظائف المناعة والعافية العامة. 1000 وحدة دولية لكل كبسولة.</p>',
            ],
            // Product 6: Omega-3 Fish Oil
            [
                'slug' => 'omega-3',
                'short_description_ar' => 'زيت سمك نقي لصحة القلب والدماغ',
                'description_ar' => '<p>مكمل زيت السمك أوميغا 3 الفاخر لصحة القلب والأوعية الدموية والصحة الذهنية. مصنوع من أسماك برية.</p>',
            ],
            // Product 7: Sports Recovery Gel
            [
                'slug' => 'sports-recovery-gel',
                'short_description_ar' => 'جل استشفاء احترافي للرياضيين',
                'description_ar' => '<p>مصمم خصيصاً للرياضيين والأشخاص النشطين. يساعد في تسريع عملية الاستشفاء بعد التمارين المكثفة.</p>',
            ],
            // Product 8: Arthritis Relief Balm
            [
                'slug' => 'arthritis-relief-balm',
                'short_description_ar' => 'بلسم مهدئ لالتهاب المفاصل وتيبسها',
                'description_ar' => '<p>بلسم لطيف وفعال مصمم خصيصاً لمن يعانون من التهاب المفاصل. يوفر الدفء والراحة للمفاصل المتيبسة.</p>',
            ],
        ];

        foreach ($translations as $translation) {
            DB::table('products')
                ->where('slug', $translation['slug'])
                ->update([
                    'short_description_ar' => $translation['short_description_ar'],
                    'description_ar' => $translation['description_ar'],
                ]);
        }
    }

    public function down(): void
    {
        DB::table('products')
            ->whereIn('slug', [
                'parfaite-massage-gel', 'skin-repair-serum', 'pain-relief-cream',
                'hydrating-face-cream', 'vitamin-d3', 'omega-3',
                'sports-recovery-gel', 'arthritis-relief-balm',
            ])
            ->update([
                'short_description_ar' => null,
                'description_ar' => null,
            ]);
    }
};
