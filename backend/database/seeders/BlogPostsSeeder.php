<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete all existing posts
        Post::truncate();

        // Ensure we have an admin user to assign as author
        $author = User::where('role', 'admin')->first() ?? User::first();
        $authorId = $author ? $author->id : 1;

        // 2. Create "Parfaite Moisturizing Cream" Post
        Post::create([
            'title' => 'Parfaite Moisturizing Cream: The Ideal Choice for Deep Hydration',
            'title_ar' => 'كريم بارفيت المرطب: الخيار الأمثل للترطيب العميق',
            'slug' => Str::slug('Parfaite Moisturizing Cream The Ideal Choice'),
            'excerpt' => 'Parfaite Moisturizing Cream is the ideal choice for smart, deep, and efficient hydration with active ingredients like Ceramide and Collagen.',
            'excerpt_ar' => 'كريم بارفيت هو الخيار الأمثل للترطيب الذكي والعميق والفعال بمكونات نشطة مثل السيراميد والكولاجين.',
            'content' => '
                <p>Parfaite Moisturizing Cream The ideal choice for smart, deep, and efficient hydration. It contains many active ingredients, which compatible with the skin\'s natural balance.</p>
                <p>These ingredients work synergistically with each other to restore moisture for 24+ hours. Parfaite cream is non-greasy, fast absorbing, & lightweight formula that won\'t clog pores.</p>
                <p>Your skin deserves Parfaite moisturizing cream.</p>
                <h3>It contains:</h3>
                <ul>
                    <li>Ceramide</li>
                    <li>Hydrolized Collagen</li>
                    <li>Hyalouronic acid</li>
                    <li>Caprelic acid</li>
                    <li>Panthenol</li>
                    <li>Glycerin</li>
                    <li>Vitamin E</li>
                </ul>
            ',
            'content_ar' => '
                <p>كريم بارفيت المرطب هو الخيار الأمثل للترطيب الذكي والعميق والفعال. يحتوي على العديد من المكونات النشطة المتوافقة مع التوازن الطبيعي للبشرة.</p>
                <p>تعمل هذه المكونات معًا لاستعادة الرطوبة لأكثر من 24 ساعة. تركيبة كريم بارفيت غير دهنية، سريعة الامتصاص، وخفيفة الوزن ولا تسد المسام.</p>
                <p>بشرتك تستحق كريم بارفيت المرطب.</p>
                <h3>يحتوي على:</h3>
                <ul>
                    <li>سيراميد</li>
                    <li>كولاجين متحلل</li>
                    <li>حمض الهيالورونيك</li>
                    <li>حمض الكابريليك</li>
                    <li>بانثينول</li>
                    <li>جلسرين</li>
                    <li>فيتامين هـ</li>
                </ul>
            ',
            'image' => 'products/pro1.jpeg', // Using existing image structure, might need adjustment if not in storage/products
            'author_id' => $authorId,
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
            'views_count' => rand(100, 500),
        ]);

        // 3. Create Second Post: "Radiant Glow Vitamin C Serum"
        Post::create([
            'title' => 'Radiant Glow Vitamin C Serum: Brighten Your Complexion',
            'title_ar' => 'سيروم فيتامين سي للإشراق: فتحي بشرتك',
            'slug' => Str::slug('Radiant Glow Vitamin C Serum'),
            'excerpt' => 'Discover the power of Vitamin C to brighten your skin tone and reduce the appearance of dark spots.',
            'excerpt_ar' => 'اكتشفي قوة فيتامين سي لتفتيح لون بشرتك وتقليل ظهور البقع الداكنة.',
            'content' => '
                <p>Our Radiant Glow Vitamin C Serum is designed to revitalize dull skin and reduce signs of fatigue. Formulated with high-potency Vitamin C and antioxidants.</p>
                <p>Regular use helps to even out skin tone, boost collagen production, and protect against environmental damage.</p>
                <h3>Key Benefits:</h3>
                <ul>
                    <li>Brightens skin tone</li>
                    <li>Reduces dark spots</li>
                    <li>Boosts radiance</li>
                    <li>Antioxidant protection</li>
                </ul>
            ',
            'content_ar' => '
                <p>تم تصميم سيروم فيتامين سي للإشراق لتنشيط البشرة الباهتة وتقليل علامات التعب. تركيبة غنية بفيتامين سي عالي الفعالية ومضادات الأكسدة.</p>
                <p>يساعد الاستخدام المنتظم على توحيد لون البشرة وتحفيز إنتاج الكولاجين والحماية من الأضرار البيئية.</p>
                <h3>الفوائد الرئيسية:</h3>
                <ul>
                    <li>تفتيح لون البشرة</li>
                    <li>تقليل البقع الداكنة</li>
                    <li>تعزيز النضارة</li>
                    <li>حماية مضادة للأكسدة</li>
                </ul>
            ',
            'image' => 'products/pro2.jpeg',
            'author_id' => $authorId,
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now()->subDays(1),
            'views_count' => rand(100, 500),
        ]);

        // 4. Create Third Post: "Ultimate Anti-Aging Night Cream"
        Post::create([
            'title' => 'Ultimate Anti-Aging Night Cream: Wake Up Refreshed',
            'title_ar' => 'كريم الليل المضاد للشيخوخة: استيقظي منتعشة',
            'slug' => Str::slug('Ultimate Anti-Aging Night Cream'),
            'excerpt' => 'Restore your skin while you sleep with our rich, nourishing night cream enriched with Retinol and Peptides.',
            'excerpt_ar' => 'استعيدي بشرتك أثناء النوم مع كريم الليل الغني والمغذي المعزز بالريتينول والببتيدات.',
            'content' => '
                <p>The Ultimate Anti-Aging Night Cream works overnight to repair and regenerate skin cells. Infused with Retinol and Peptides to smooth fine lines.</p>
                <p>Deeply hydrates and strengthens the skin barrier, leaving you with a youthful glow in the morning.</p>
                <h3>Active Ingredients:</h3>
                <ul>
                    <li>Retinol</li>
                    <li>Peptides</li>
                    <li>Shea Butter</li>
                    <li>Niacinamide</li>
                </ul>
            ',
            'content_ar' => '
                <p>يعمل كريم الليل المضاد للشيخوخة طوال الليل لإصلاح وتجديد خلايا البشرة. غني بالريتينول والببتيدات لتنعيم الخطوط الدقيقة.</p>
                <p>يرطب بعمق ويقوي حاجز البشرة، مما يمنحك إشراقة شبابية في الصباح.</p>
                <h3>المكونات النشطة:</h3>
                <ul>
                    <li>ريتينول</li>
                    <li>ببتيدات</li>
                    <li>زبدة الشيا</li>
                    <li>نياسيناميد</li>
                </ul>
            ',
            'image' => 'products/pro3.jpeg',
            'author_id' => $authorId,
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now()->subDays(2),
            'views_count' => rand(100, 500),
        ]);
        
        $this->command->info('Blog posts seeded successfully!');
    }
}
