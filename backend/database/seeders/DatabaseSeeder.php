<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@medicamo.com',
            'phone' => '+1 123 456 7890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Create Sample Customer
        $customer = User::create([
            'name' => 'John Smith',
            'email' => 'customer@medicamo.com',
            'phone' => '+1 987 654 3210',
            'password' => Hash::make('123456'),
            'role' => 'customer',
            'status' => 'active',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        $customer2 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '+1 555 123 4567',
            'password' => Hash::make('123456'),
            'role' => 'customer',
            'status' => 'active',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'Muscle Care',
                'name_ar' => 'العناية بالعضلات',
                'slug' => 'muscle-care',
                'description' => 'Products for muscle and joint pain relief',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Skincare',
                'name_ar' => 'العناية بالبشرة',
                'slug' => 'skincare',
                'description' => 'Products for skin health and beauty',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Topical Pain Relief',
                'name_ar' => 'مسكنات موضعية',
                'slug' => 'pain-relief',
                'description' => 'Creams and gels for topical pain relief',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Supplements',
                'name_ar' => 'المكملات الغذائية',
                'slug' => 'supplements',
                'description' => 'Vitamins and dietary supplements',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create Sample Products (matching PHP design)
        $products = [
            [
                'category_id' => 1,
                'name' => 'Parfaite Massage Gel',
                'name_ar' => 'جيل مساج بارفيت',
                'slug' => 'parfaite-massage-gel',
                'sku' => 'MED-MUS-001',
                'short_description' => 'Therapeutic massage gel for muscle and joint pain relief',
                'description' => '<p>Our premium Parfaite Massage Gel is specially formulated to provide effective relief for muscle and joint pain. Made with natural ingredients that penetrate deep into the tissue.</p><ul><li>Fast-acting formula</li><li>Long-lasting relief</li><li>Natural ingredients</li></ul>',
                'price' => 25.00,
                'sale_price' => 19.99,
                'stock_quantity' => 50,
                'manufacturer' => 'Medicamo',
                'size' => '100ml',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Parfaite Massage Gel - Medicamo',
                'meta_description' => 'Therapeutic massage gel for muscle and joint pain relief. Natural ingredients for lasting comfort.',
            ],
            [
                'category_id' => 2,
                'name' => 'Skin Repair Serum',
                'name_ar' => 'سيروم إصلاح البشرة',
                'slug' => 'skin-repair-serum',
                'sku' => 'MED-SKIN-001',
                'short_description' => 'Advanced serum for skin repair and cell regeneration',
                'description' => '<p>Our advanced Skin Repair Serum uses cutting-edge technology to promote cell regeneration and skin repair. Perfect for daily use to maintain youthful, healthy skin.</p>',
                'price' => 45.00,
                'sale_price' => 35.00,
                'stock_quantity' => 30,
                'manufacturer' => 'Medicamo',
                'size' => '30ml',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Pain Relief Cream',
                'name_ar' => 'كريم مسكن للألم',
                'slug' => 'pain-relief-cream',
                'sku' => 'MED-PAIN-001',
                'short_description' => 'Effective cream for muscle and joint pain relief',
                'description' => '<p>Fast-acting pain relief cream that provides immediate comfort. Ideal for arthritis, muscle strains, and joint pain.</p>',
                'price' => 18.00,
                'sale_price' => 14.50,
                'stock_quantity' => 40,
                'manufacturer' => 'Medicamo',
                'size' => '75ml',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Hydrating Face Cream',
                'name_ar' => 'كريم مرطب للوجه',
                'slug' => 'hydrating-face-cream',
                'sku' => 'MED-SKIN-002',
                'short_description' => 'Deep hydration for dry and sensitive skin',
                'description' => '<p>Intensive hydrating cream designed for dry and sensitive skin. Provides 24-hour moisture protection.</p>',
                'price' => 32.00,
                'sale_price' => null,
                'stock_quantity' => 25,
                'manufacturer' => 'Medicamo',
                'size' => '50ml',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Vitamin D3 Capsules',
                'name_ar' => 'كبسولات فيتامين د3',
                'slug' => 'vitamin-d3',
                'sku' => 'MED-SUPP-001',
                'short_description' => 'Essential vitamin D3 for bone health and immunity',
                'description' => '<p>High-quality Vitamin D3 supplement to support bone health, immune function, and overall wellness. 1000 IU per capsule.</p>',
                'price' => 24.00,
                'sale_price' => null,
                'stock_quantity' => 100,
                'manufacturer' => 'Medicamo',
                'size' => '60 capsules',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Omega-3 Fish Oil',
                'name_ar' => 'زيت السمك أوميغا 3',
                'slug' => 'omega-3',
                'sku' => 'MED-SUPP-002',
                'short_description' => 'Pure fish oil for heart and brain health',
                'description' => '<p>Premium Omega-3 fish oil supplement for cardiovascular and cognitive health. Made from wild-caught fish.</p>',
                'price' => 28.00,
                'sale_price' => 24.00,
                'stock_quantity' => 80,
                'manufacturer' => 'Medicamo',
                'size' => '90 capsules',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Sports Recovery Gel',
                'name_ar' => 'جل استشفاء رياضي',
                'slug' => 'sports-recovery-gel',
                'sku' => 'MED-MUS-002',
                'short_description' => 'Professional-grade recovery gel for athletes',
                'description' => '<p>Specially formulated for athletes and active individuals. Helps speed up recovery after intense workouts.</p>',
                'price' => 35.00,
                'sale_price' => null,
                'stock_quantity' => 35,
                'manufacturer' => 'Medicamo',
                'size' => '150ml',
                'status' => 'published',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Arthritis Relief Balm',
                'name_ar' => 'بلسم لعلاج التهاب المفاصل',
                'slug' => 'arthritis-relief-balm',
                'sku' => 'MED-PAIN-002',
                'short_description' => 'Soothing balm for arthritis and joint stiffness',
                'description' => '<p>Gentle yet effective balm designed specifically for arthritis sufferers. Provides warmth and comfort to stiff joints.</p>',
                'price' => 22.00,
                'sale_price' => 18.50,
                'stock_quantity' => 45,
                'manufacturer' => 'Medicamo',
                'size' => '100ml',
                'status' => 'published',
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create Sample Reviews (approved, show on homepage)
        $reviews = [
            [
                'user_id' => $customer->id,
                'product_id' => 1,
                'rating' => 5,
                'title' => 'Amazing Product!',
                'comment' => 'Amazing experience! The products are authentic and the delivery was super fast. I recommend Medicamo to everyone!',
                'status' => 'approved',
                'show_on_homepage' => true,
                'approved_at' => now(),
            ],
            [
                'user_id' => $customer2->id,
                'product_id' => 2,
                'rating' => 5,
                'title' => 'Best Pharmacy Company!',
                'comment' => 'Best pharmaceutical company! Excellent prices and professional service. Thanks to the hardworking team!',
                'status' => 'approved',
                'show_on_homepage' => true,
                'approved_at' => now(),
            ],
            [
                'user_id' => $customer->id,
                'product_id' => 3,
                'rating' => 5,
                'title' => 'Same Day Delivery!',
                'comment' => 'Easy to use website and diverse products. I received my order the same day! Excellent service.',
                'status' => 'approved',
                'show_on_homepage' => true,
                'approved_at' => now(),
            ],
        ];

        foreach ($reviews as $reviewData) {
            Review::create($reviewData);
        }

        // Create Default Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Medicamo', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Pharmaceutical Production Company', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+1 123 456 7890', 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => 'info@medicamo.com', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'New York, USA', 'group' => 'contact'],
            ['key' => 'whatsapp_number', 'value' => '+11234567890', 'group' => 'contact'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/medicamo', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/medicamo', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }

        // Run Blog Post Seeder
        $this->call(ParfaitePostSeeder::class);

        // Create Sample Static Pages
        \App\Models\Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<h1>About Medicamo</h1><p>Medicamo is a leading pharmaceutical production company specializing in providing the best pharmaceutical and cosmetic products...</p>',
            'is_active' => true,
        ]);

        \App\Models\Page::create([
            'title' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'content' => '<h1>Privacy Policy</h1><p>We respect your privacy and are committed to protecting your personal data...</p>',
            'is_active' => true,
        ]);

        echo "✅ Database seeded successfully!\n";
        echo "📧 Admin: admin@medicamo.com\n";
        echo "🔐 Password: admin123\n";
    }
}
