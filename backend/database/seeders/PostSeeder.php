<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user or first user
        $author = User::where('role', 'admin')->first() ?? User::first();
        
        if (!$author) {
            $this->command->info('No users found. Please create a user first.');
            return;
        }

        $posts = [
            [
                'title' => 'Essential Vitamins for Immune System Support',
                'title_ar' => 'الفيتامينات الأساسية لدعم جهاز المناعة',
                'slug' => 'essential-vitamins-immune-system',
                'excerpt' => 'Discover the key vitamins that help strengthen your immune system and keep you healthy throughout the year.',
                'excerpt_ar' => 'اكتشف الفيتامينات الرئيسية التي تساعد على تقوية جهاز المناعة والحفاظ على صحتك طوال العام.',
                'content' => '
<h2>Why Vitamins Matter for Immunity</h2>
<p>Your immune system is your body\'s defense against infections and diseases. Proper nutrition, especially vitamins, plays a crucial role in maintaining a strong immune response.</p>

<h3>Vitamin C - The Immunity Champion</h3>
<p>Vitamin C is perhaps the most well-known immune booster. It helps stimulate the production of white blood cells, which are essential for fighting infections. Found naturally in citrus fruits, berries, and leafy greens, Vitamin C is a must-have for daily health.</p>

<h3>Vitamin D - The Sunshine Vitamin</h3>
<p>Often called the "sunshine vitamin," Vitamin D is crucial for immune function. Research shows that Vitamin D deficiency is linked to increased susceptibility to infections. Supplements are especially important during winter months.</p>

<h3>Vitamin E - The Antioxidant Powerhouse</h3>
<p>Vitamin E is a powerful antioxidant that helps the body fight off infection. It\'s found in nuts, seeds, and spinach, and plays a vital role in maintaining healthy immune function.</p>

<h3>B Vitamins - Energy for Immunity</h3>
<p>B vitamins, particularly B6, B9 (folate), and B12, are essential for producing immune cells. They help regulate the immune response and ensure your body can fight off pathogens effectively.</p>

<h2>Tips for Optimal Vitamin Intake</h2>
<ul>
    <li>Eat a balanced diet rich in fruits and vegetables</li>
    <li>Consider supplements during winter or if you have dietary restrictions</li>
    <li>Consult with a healthcare professional before starting any supplement regimen</li>
    <li>Stay hydrated to help vitamins absorb properly</li>
</ul>

<blockquote>
"A healthy immune system starts with proper nutrition. Make vitamins a priority in your daily routine."
</blockquote>

<p>At Medicamo, we offer a wide range of high-quality vitamin supplements to support your immune health. Browse our <a href="/products">products</a> to find what\'s right for you.</p>
',
                'is_featured' => true,
                'status' => 'published',
                'views_count' => 245,
            ],
            [
                'title' => 'Complete Guide to Skincare Products',
                'title_ar' => 'الدليل الكامل لمنتجات العناية بالبشرة',
                'slug' => 'complete-guide-skincare-products',
                'excerpt' => 'Learn how to choose the right skincare products for your skin type and build an effective daily routine.',
                'excerpt_ar' => 'تعلم كيفية اختيار منتجات العناية بالبشرة المناسبة لنوع بشرتك وبناء روتين يومي فعال.',
                'content' => '
<h2>Understanding Your Skin Type</h2>
<p>Before selecting skincare products, it\'s essential to understand your skin type. The four main types are:</p>
<ul>
    <li><strong>Oily Skin:</strong> Characterized by excess sebum production and enlarged pores</li>
    <li><strong>Dry Skin:</strong> Often feels tight and may show flaking or rough patches</li>
    <li><strong>Combination Skin:</strong> Oily in some areas (usually T-zone) and dry in others</li>
    <li><strong>Sensitive Skin:</strong> Easily irritated, prone to redness and reactions</li>
</ul>

<h3>Building Your Skincare Routine</h3>
<p>A good skincare routine doesn\'t need to be complicated. Here\'s a simple but effective approach:</p>

<h4>Morning Routine</h4>
<ol>
    <li><strong>Cleanser:</strong> Start with a gentle cleanser to remove overnight oil buildup</li>
    <li><strong>Toner:</strong> Balance your skin\'s pH and prep for other products</li>
    <li><strong>Serum:</strong> Target specific concerns like dark spots or fine lines</li>
    <li><strong>Moisturizer:</strong> Hydrate and protect your skin barrier</li>
    <li><strong>Sunscreen:</strong> The most important step - protect against UV damage</li>
</ol>

<h4>Evening Routine</h4>
<ol>
    <li><strong>Double Cleanse:</strong> Remove makeup and sunscreen, then cleanse again</li>
    <li><strong>Exfoliate:</strong> 2-3 times per week to remove dead skin cells</li>
    <li><strong>Treatment:</strong> Apply retinol or other treatment products</li>
    <li><strong>Night Cream:</strong> Heavier moisturizer for overnight repair</li>
</ol>

<h3>Key Ingredients to Look For</h3>
<p>When shopping for skincare products, look for these proven ingredients:</p>
<ul>
    <li><strong>Hyaluronic Acid:</strong> Intense hydration</li>
    <li><strong>Niacinamide:</strong> Reduces pores and brightens skin</li>
    <li><strong>Retinol:</strong> Anti-aging powerhouse</li>
    <li><strong>Vitamin C:</strong> Brightening and antioxidant protection</li>
    <li><strong>Salicylic Acid:</strong> Clears pores and fights acne</li>
</ul>

<blockquote>
"The best skincare routine is the one you\'ll actually follow consistently."
</blockquote>

<p>Explore our <a href="/products">dermatological products</a> range for pharmacy-grade skincare solutions.</p>
',
                'is_featured' => true,
                'status' => 'published',
                'views_count' => 189,
            ],
            [
                'title' => 'Natural Remedies vs. Modern Medicine',
                'title_ar' => 'العلاجات الطبيعية مقابل الطب الحديث',
                'slug' => 'natural-remedies-vs-modern-medicine',
                'excerpt' => 'Exploring the balance between traditional natural remedies and modern pharmaceutical treatments for optimal health.',
                'excerpt_ar' => 'استكشاف التوازن بين العلاجات الطبيعية التقليدية والعلاجات الصيدلانية الحديثة للصحة المثلى.',
                'content' => '
<h2>The Best of Both Worlds</h2>
<p>In today\'s healthcare landscape, there\'s often a debate between natural remedies and modern medicine. The truth is, both have their place in maintaining optimal health.</p>

<h3>When Natural Remedies Work Best</h3>
<p>Natural remedies can be effective for:</p>
<ul>
    <li><strong>Prevention:</strong> Herbs and supplements can help prevent illness before it starts</li>
    <li><strong>Minor Ailments:</strong> Conditions like mild headaches, digestive issues, or sleep problems</li>
    <li><strong>Chronic Wellness:</strong> Supporting overall health and immune function</li>
    <li><strong>Complementary Care:</strong> Working alongside conventional treatments</li>
</ul>

<h3>Popular Natural Remedies</h3>
<h4>Herbal Solutions</h4>
<ul>
    <li><strong>Echinacea:</strong> Traditional immune support</li>
    <li><strong>Ginger:</strong> Digestive aid and anti-nausea</li>
    <li><strong>Turmeric:</strong> Anti-inflammatory properties</li>
    <li><strong>Chamomile:</strong> Calming and sleep support</li>
</ul>

<h3>When Modern Medicine is Essential</h3>
<p>Modern pharmaceutical treatments are crucial for:</p>
<ul>
    <li><strong>Serious Infections:</strong> Bacterial infections requiring antibiotics</li>
    <li><strong>Chronic Diseases:</strong> Diabetes, heart disease, hypertension</li>
    <li><strong>Emergency Care:</strong> Acute conditions requiring immediate intervention</li>
    <li><strong>Mental Health:</strong> Depression, anxiety, and other psychiatric conditions</li>
</ul>

<h3>Finding Balance</h3>
<p>The key is working with healthcare professionals to find the right balance for your individual needs. Always:</p>
<ol>
    <li>Consult your doctor before starting any new supplement</li>
    <li>Inform your healthcare provider about all supplements you take</li>
    <li>Be aware of potential interactions between natural and pharmaceutical products</li>
    <li>Don\'t replace prescribed medications with supplements without medical guidance</li>
</ol>

<blockquote>
"The wisest approach combines the best of traditional wisdom with modern scientific advances."
</blockquote>

<p>At Medicamo, we believe in providing both natural supplements and pharmaceutical products. Browse our <a href="/products">complete range</a> to find what suits your health needs.</p>
',
                'is_featured' => false,
                'status' => 'published',
                'views_count' => 156,
            ],
        ];

        foreach ($posts as $postData) {
            Post::create([
                ...$postData,
                'author_id' => $author->id,
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Created 3 blog posts successfully!');
    }
}
