<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminReporting;
use App\Services\OnlineOrderService;
use App\Support\ReportPeriod;
use Database\Seeders\ORQProductSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class StorefrontCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK = '/api/v1/payments/paymob/webhook';
    private const INTENTION = 'https://accept.paymob.com/v1/intention/';

    // These are the checkout tables. Older, unrelated migrations use MySQL-only SQL.
    protected function migrateFreshUsing(): array
    {
        $names = [
            '0001_01_01_000000_create_users_table',
            '2026_01_06_000001_create_categories_table',
            '2026_01_06_000002_create_products_table',
            '2026_01_06_000003_create_product_images_table',
            '2026_01_06_000006_create_addresses_table',
            '2026_01_06_000007_create_orders_table',
            '2026_01_06_000008_create_reviews_table',
            '2026_01_06_000011_create_settings_tables',
            '2026_01_07_000001_add_images_to_products_table',
            '2026_01_07_000003_add_tracking_to_orders_table',
            '2026_02_15_180000_create_destinations_table',
            '2026_02_15_180631_add_destination_columns_to_users_and_addresses',
            '2026_02_06_182700_add_offer_fields_to_products_table',
            '2026_02_19_220000_create_jobs_listing_table',
            '2026_02_19_220100_create_job_applications_table',
            '2026_02_20_115200_add_arabic_description_to_products',
            '2026_11_11_224006_create_notifications_table',
            '2026_11_12_000001_add_perfume_fields_to_products_table',
            '2026_11_12_000002_create_product_variants_table',
            '2026_11_12_000003_add_guest_checkout_to_orders',
            '2026_11_18_000001_add_whatsapp_confirmation_to_orders',
            '2026_11_20_000001_add_paymob_checkout_to_orders',
            '2026_11_25_000001_add_growth_reporting',
        ];

        return ['--path' => array_map(fn (string $name) => 'database/migrations/' . $name . '.php', $names)];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ORQProductSeeder::class);
        config([
            'app.key' => 'base64:' . base64_encode(str_repeat('k', 32)),
            'services.paymob.enabled' => true,
            'services.paymob.secret_key' => 'sk_test_checkout',
            'services.paymob.public_key' => 'pk_test_checkout',
            'services.paymob.integration_id' => '456',
            'services.paymob.hmac_secret' => 'local-test-hmac',
            'services.whatsapp.access_token' => null,
            'services.ultramsg.token' => null,
        ]);
        Http::preventStrayRequests();
    }

    public function test_card_checkout_uses_server_prices_and_is_confirmed_once_by_paymob(): void
    {
        Http::fake([self::INTENTION => Http::response([
            'intention_order_id' => 654321,
            'client_secret' => 'test-client-token',
        ], 201)]);
        $admin = User::factory()->create(['role' => 'admin', 'phone' => '01000000001']);
        $data = $this->orderData();
        $variant = Product::where('slug', 'chaos')->firstOrFail()->variants()->where('size', '50ml')->firstOrFail();
        $amount = (int) round((float) $variant->current_price * 100);
        $stock = $variant->stock_quantity;

        $this->getJson('/api/v1/site')->assertJsonPath('data.online_payment_available', true);

        $first = $this->postJson('/api/v1/orders', $data)
            ->assertCreated()
            ->assertJsonPath('data.paymentMethod', 'card')
            ->assertJsonPath('data.checkoutUrl', 'https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test_checkout&clientSecret=test-client-token');

        $this->postJson('/api/v1/orders', $data + ['attribution' => ['session_id' => Str::uuid()->toString()]])->assertCreated()
            ->assertJsonPath('data.orderNumber', $first->json('data.orderNumber'));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === self::INTENTION
            && $request->hasHeader('Authorization', 'Token sk_test_checkout')
            && $request['amount'] === $amount
            && $request['currency'] === 'EGP'
            && $request['billing_data']['email'] === $data['billing_email']);
        $this->postJson('/api/v1/orders', array_replace($data, ['billing_email' => 'different@example.com']))->assertConflict();
        $this->assertSame($stock - 1, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('notifications', 0);

        $statusUrl = '/api/v1/payments/paymob/status/' . $data['checkout_key'];
        $this->get('/api/v1/payments/paymob/return/' . $data['checkout_key'] . '?success=true')
            ->assertRedirect(rtrim(config('app.frontend_url'), '/') . '/payment/result?token=' . $data['checkout_key']);
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'pending');

        [$transaction, $signature] = $this->paymentCallback($amount);
        $this->postJson(self::WEBHOOK, $transaction)->assertForbidden();
        [$differentAmount, $differentSignature] = $this->paymentCallback($amount + 1);
        $this->postJson(self::WEBHOOK . '?hmac=' . $differentSignature, $differentAmount)->assertNoContent();
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'pending');

        $this->postJson(self::WEBHOOK . '?hmac=' . $signature, $transaction)->assertNoContent();
        $this->postJson(self::WEBHOOK . '?hmac=' . $signature, $transaction)->assertNoContent();
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'paid')->assertJsonMissingPath('data.checkoutUrl');
        $this->assertDatabaseHas('orders', [
            'payment_status' => 'paid', 'status' => 'processing', 'paymob_transaction_id' => '987',
            'whatsapp_confirmation_requested' => false,
        ]);
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame($stock - 1, $variant->fresh()->stock_quantity);
    }

    public function test_failed_initialization_rolls_back_and_expired_payment_releases_stock_once(): void
    {
        $variant = Product::where('slug', 'chaos')->firstOrFail()->variants()->where('size', '50ml')->firstOrFail();
        $stock = $variant->stock_quantity;
        $data = $this->orderData();
        Http::fake([self::INTENTION => Http::sequence()
            ->pushStatus(502)
            ->push(['intention_order_id' => 654321, 'client_secret' => 'test-client-token'], 201)]);

        $this->postJson('/api/v1/orders', $data)->assertServiceUnavailable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame($stock, $variant->fresh()->stock_quantity);

        $this->postJson('/api/v1/orders', $data)->assertCreated();
        $order = Order::sole();
        $amount = (int) round((float) $order->total * 100);
        [$failed, $failedSignature] = $this->paymentCallback($amount, false);
        $this->postJson(self::WEBHOOK . '?hmac=' . $failedSignature, $failed)->assertNoContent();
        $statusUrl = '/api/v1/payments/paymob/status/' . $data['checkout_key'];
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'declined');
        $this->assertSame($stock - 1, $variant->fresh()->stock_quantity);

        $this->travel(46)->minutes();
        app(OnlineOrderService::class)->releaseExpired();
        app(OnlineOrderService::class)->releaseExpired();
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'expired');
        $this->assertSame($stock, $variant->fresh()->stock_quantity);

        [$paid, $paidSignature] = $this->paymentCallback($amount);
        $this->postJson(self::WEBHOOK . '?hmac=' . $paidSignature, $paid)->assertNoContent();
        $this->getJson($statusUrl)->assertJsonPath('data.status', 'review');
        $this->assertSame($stock, $variant->fresh()->stock_quantity);
    }

    public function test_cash_on_delivery_and_contact_enquiries_work_without_gateway_keys(): void
    {
        config(['services.paymob.enabled' => false]);
        $admin = User::factory()->create(['role' => 'admin', 'phone' => '01000000001']);
        $data = $this->orderData();

        $this->postJson('/api/v1/orders', $data)->assertServiceUnavailable();
        unset($data['payment_method'], $data['billing_email'], $data['checkout_key']);
        $this->postJson('/api/v1/orders', $data)->assertCreated()->assertJsonPath('data.paymentMethod', 'cod');

        $this->postJson('/api/v1/contact', [
            'name' => 'Customer Example',
            'email' => 'customer@example.com',
            'message' => 'Please check my test order.',
        ])->assertNoContent();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(2, $admin->notifications()->count());
    }

    public function test_catalogue_migration_archives_100ml_and_updates_live_copy(): void
    {
        $product = Product::where('slug', 'motamared')->firstOrFail();
        $legacyVariant = $product->variants()->where('size', '100ml')->firstOrFail();
        DB::table('products')->where('id', $product->id)->update(['sku' => 'ORQ-ROYAL-OUD', 'description' => 'Old copy']);
        DB::table('product_variants')->where('id', $legacyVariant->id)->update(['is_active' => true]);
        DB::table('settings')->updateOrInsert(['key' => 'site_settings'], ['value' => json_encode([
            'commerce' => ['shipping_cost' => 35],
            'footer' => ['copyright' => '© ORQ Haute Parfumerie'],
        ]), 'group' => 'general']);

        $order = $this->orderData();
        $order['payment_method'] = 'cod';
        $order['items'][0]['slug'] = 'royal-oud';
        $order['items'][0]['size'] = '100ml';
        $this->postJson('/api/v1/orders', $order)->assertUnprocessable()->assertJsonValidationErrors('items');

        $migration = require database_path('migrations/2026_11_21_000001_update_orq_storefront_catalog.php');
        $migration->up();

        $this->assertFalse($legacyVariant->fresh()->is_active);
        $this->assertSame($legacyVariant->stock_quantity, $legacyVariant->fresh()->stock_quantity);
        $this->assertNotSame('Old copy', $product->fresh()->description);
        $this->getJson('/api/v1/products/royal-oud')->assertOk()
            ->assertJsonPath('data.sku', 'ORQ-MOTAMARED')
            ->assertJsonCount(1, 'data.variants')
            ->assertJsonPath('data.variants.0.size', '50ml');
        $this->getJson('/api/v1/site')->assertOk()
            ->assertJsonPath('data.footer.copyright', '© ORQ')
            ->assertJsonPath('data.commerce.shipping_cost', 35);

        $this->actingAs(User::factory()->create(['role' => 'admin', 'phone' => '01000000001']));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(VariantsRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])->assertCanNotSeeTableRecords([$legacyVariant]);
    }

    public function test_canonical_product_ids_keep_past_orders_and_old_cart_links_valid(): void
    {
        $aliases = ['royal-oud' => 'motamared', 'oud-ambassador' => 'ghoroor', 'oud-madawi' => 'kaseh'];
        $products = Product::whereIn('slug', array_values($aliases))->get()->keyBy('slug');
        $orderData = $this->orderData();
        $orderData['payment_method'] = 'cod';
        $orderData['items'] = array_map(fn ($slug) => ['slug' => $slug, 'size' => '50ml', 'quantity' => 1], array_values($aliases));
        $this->postJson('/api/v1/orders', $orderData)->assertCreated();
        $pastItems = Order::firstOrFail()->items()->orderBy('id')->get(['id', 'product_id', 'product_variant_id', 'product_sku'])->toArray();

        foreach ($aliases as $old => $new) {
            DB::table('products')->where('id', $products[$new]->id)->update([
                'slug' => $old,
                'images' => json_encode(["/media/orq/{$old}.webp", 'products/uploaded.webp']),
                'original_inspiration' => 'old copy kept in private archive',
            ]);
        }

        $rename = require database_path('migrations/2026_11_22_000001_canonicalize_orq_product_slugs.php');
        $archive = require database_path('migrations/2026_11_22_000002_archive_orq_product_inspiration.php');
        $rename->up();
        $archive->up();
        $archive->up();

        foreach ($aliases as $old => $new) {
            $this->assertSame($products[$new]->id, Product::where('slug', $new)->value('id'));
            $this->assertNull($products[$new]->fresh()->original_inspiration);
            $this->assertDatabaseMissing('products', ['slug' => $old]);
            $this->getJson('/api/v1/products/' . $new)->assertOk()->assertJsonPath('data.slug', $new)
                ->assertJsonPath('data.gallery.0', "/media/orq/{$new}.webp")
                ->assertJsonPath('data.gallery.1', asset('storage/products/uploaded.webp'))
                ->assertJsonMissingPath('data.originalInspiration')->assertJsonMissingPath('data.brandFeel');
            $this->getJson('/api/v1/products/' . $old)->assertOk()->assertJsonPath('data.slug', $new);
        }
        $this->assertSame($pastItems, Order::firstOrFail()->items()->orderBy('id')->get(['id', 'product_id', 'product_variant_id', 'product_sku'])->toArray());

        $orderData['items'][0]['slug'] = 'royal-oud';
        $orderData['items'][] = ['slug' => 'motamared', 'size' => '50ml', 'quantity' => 1];
        $this->postJson('/api/v1/orders', $orderData)->assertCreated();
        $this->assertDatabaseHas('order_items', [
            'order_id' => Order::latest('id')->firstOrFail()->id,
            'product_id' => $products['motamared']->id,
            'quantity' => 2,
        ]);

        $archive->down();
        $rename->down();
        $this->assertSame('old copy kept in private archive', $products['motamared']->fresh()->original_inspiration);
        $this->assertSame('royal-oud', $products['motamared']->fresh()->slug);
    }

    public function test_customers_only_see_orders_belonging_to_their_storefront_account(): void
    {
        $api = '/api/v1/account/orders';
        $this->getJson($api)->assertUnauthorized();
        $data = array_replace($this->orderData(), ['payment_method' => 'cod']);
        $guest = $this->postJson('/api/v1/orders', $data)->assertCreated()->json('data.orderNumber');
        $first = User::factory()->create(['phone' => '01000000008', 'status' => 'active', 'role' => 'customer']);
        $second = User::factory()->create(['phone' => '01000000009', 'status' => 'active', 'role' => 'customer']);
        $this->actingAs($first, 'storefront');
        $number = $this->postJson($api, $data + ['user_id' => $second->id])->assertCreated()->json('data.orderNumber');
        $order = Order::where('order_number', $number)->sole();
        $this->assertSame($first->id, $order->user_id);
        $order->update(['tracking_number' => 'SHIP-TEST', 'shipping_carrier' => 'ORQ Delivery']);
        $order->updateStatus('shipped');
        $this->getJson($api)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.number', $number)
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->getJson($api . '/' . $number)->assertOk()->assertJsonPath('data.status', 'shipped')
            ->assertJsonPath('data.trackingNumber', 'SHIP-TEST')->assertJsonPath('data.timeline.3.completed', true)
            ->assertJsonPath('data.paymentMethod', 'cod')->assertJsonMissingPath('data.payment_checkout_key');
        $this->getJson($api . '/' . $guest)->assertNotFound();
        $this->actingAs($second, 'storefront');
        $this->getJson($api . '/' . $number)->assertNotFound();
        $this->getJson($api)->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_card_checkout_retries_keep_the_original_customer_owner(): void
    {
        Http::fake([self::INTENTION => Http::response(['intention_order_id' => 123456, 'client_secret' => 'test-client-token'], 201)]);
        $customer = User::factory()->create(['phone' => '01000000008', 'status' => 'active', 'role' => 'customer']);
        $this->actingAs($customer, 'storefront');
        $api = '/api/v1/account/orders';
        $data = $this->orderData();
        $order = $this->postJson($api, $data)->assertCreated()->json('data.orderNumber');
        $this->postJson($api, $data)->assertCreated()->assertJsonPath('data.orderNumber', $order);
        $this->assertSame($customer->id, Order::sole()->user_id);
        $this->postJson('/api/v1/orders', $data)->assertConflict();
        Http::assertSentCount(1);
    }

    public function test_an_admin_can_link_a_guest_order_and_update_tracking_in_the_panel(): void
    {
        $data = array_replace($this->orderData(), ['payment_method' => 'cod']);
        $number = $this->postJson('/api/v1/orders', $data)->assertCreated()->json('data.orderNumber');
        $order = Order::where('order_number', $number)->sole();
        $customer = User::factory()->create(['phone' => '01000000008', 'role' => 'customer', 'status' => 'active']);
        $admin = User::factory()->create(['phone' => '01000000009', 'role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin, 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])->fillForm([
            'user_id' => $customer->id, 'status' => 'shipped',
            'shipping_carrier' => 'ORQ Delivery', 'tracking_number' => 'TEST-TRACKING',
        ])->call('save')->assertHasNoFormErrors();

        $this->assertSame($customer->id, $order->fresh()->user_id);
        $this->assertNotNull($order->fresh()->shipped_at);
        $this->actingAs($customer, 'storefront');
        $this->getJson('/api/v1/account/orders/' . $number)->assertOk()
            ->assertJsonPath('data.trackingNumber', 'TEST-TRACKING');
    }

    public function test_realized_sales_campaign_costs_and_funnel_use_checkout_data(): void
    {
        $this->travelTo(now('Africa/Cairo')->setDate(2026, 11, 25)->setTime(16, 0));
        config(['services.paymob.enabled' => false]);
        Notification::fake();
        $session = Str::uuid()->toString();
        foreach (['visit', 'visit', 'cart', 'checkout'] as $stage) {
            $this->postJson('/api/v1/funnel', ['stage' => $stage, 'session_id' => $session])->assertNoContent();
        }
        $this->assertDatabaseCount('storefront_funnel_events', 3);
        $this->postJson('/api/v1/funnel', ['stage' => 'sale', 'session_id' => $session])->assertUnprocessable();

        $checkout = array_replace($this->orderData(), [
            'payment_method' => 'cod',
            'whatsapp_confirmation_requested' => false,
            'attribution' => ['session_id' => $session, 'utm_source' => 'Instagram', 'utm_medium' => 'Paid_Social', 'utm_campaign' => 'Winter Launch'],
        ]);
        $orderNumber = $this->postJson('/api/v1/orders', $checkout)->assertCreated()
            ->assertJsonMissingPath('data.utm_source')->json('data.orderNumber');
        $sale = Order::where('order_number', $orderNumber)->firstOrFail();
        $this->assertSame('winter launch', $sale->utm_campaign);
        $this->assertSame($session, $sale->analytics_session_id);
        $sale->updateStatus('delivered');
        $sale->forceFill(['created_at' => '2026-11-24 22:00:00'])->save();

        foreach ([
            ['pending', 'pending'],
            ['cancelled', 'paid'],
            ['returned', 'paid'],
            ['delivered', 'refunded'],
        ] as [$status, $payment]) {
            $record = $sale->replicate();
            $record->fill(['order_number' => Order::generateOrderNumber(), 'status' => $status, 'payment_status' => $payment])->save();
        }
        $outside = $sale->replicate();
        $outside->forceFill(['order_number' => Order::generateOrderNumber(), 'created_at' => '2026-11-24 21:59:59'])->save();

        DB::table('campaign_spends')->insert([
            ['day' => '2026-11-25', 'utm_source' => 'instagram', 'utm_medium' => 'paid_social', 'utm_campaign' => 'winter launch', 'amount' => 200],
            ['day' => '2026-11-25', 'utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'launch', 'amount' => 100],
        ]);
        $period = ReportPeriod::fromFilters(['from' => '2026-11-25', 'to' => '2026-11-25']);
        $reports = app(AdminReporting::class);
        $totals = $reports->totals($period);
        $this->assertSame(5, $totals['orders']);
        $this->assertSame(2, $totals['lost']);
        $this->assertSame(1, $totals['sales_count']);
        $this->assertSame((float) $sale->total, $totals['sales']);
        $this->assertSame(['visit' => 1, 'cart' => 1, 'checkout' => 1, 'sale' => 1], $reports->funnel($period));
        $this->assertSame((float) $sale->total, $reports->trend($period)[0]['value']);
        $this->assertSame('القاهرة', $reports->regions($period)->first()->label);
        $this->assertSame(1, (int) $reports->products($period)->first()->units);
        $this->assertCount(2, $reports->campaigns($period));
        $campaign = $reports->campaigns($period, 'instagram')->sole();
        $this->assertSame(200.0, (float) $campaign->spend);
        $this->assertSame(1, (int) $campaign->sales_count);
        $this->assertSame((float) $sale->total, (float) $campaign->sales);
    }

    public function test_admin_pages_and_campaign_export_are_protected_and_branded(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '01000000991']);
        $this->actingAs($customer)->get('/admin/reports/campaigns.csv')->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin', 'phone' => '01000000992', 'status' => 'active']);
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('نظرة عامة')->assertSee('dir="rtl"', false)->assertDontSee('Filament v');
        $this->get('/admin/reports')->assertOk()->assertSee('مسار الشراء');
        $this->get('/admin/marketing')->assertOk()->assertSee('حاسبة ميزانية الإعلان');
        $csv = $this->get('/admin/reports/campaigns.csv')->assertOk()->assertDownload()->streamedContent();
        $this->assertStringStartsWith("\xef\xbb\xbf", $csv);
        $this->assertStringContainsString('العائد على الإنفاق', $csv);
    }

    private function orderData(): array
    {
        return [
            'name' => 'Customer Example',
            'phone' => '01012345678',
            'governorate' => 'القاهرة',
            'address' => 'Test address, Cairo',
            'payment_method' => 'card',
            'billing_email' => 'customer@example.com',
            'checkout_key' => Str::uuid()->toString(),
            'whatsapp_confirmation_requested' => true,
            'items' => [['slug' => 'chaos', 'size' => '50ml', 'quantity' => 1, 'price' => 1]],
        ];
    }

    private function paymentCallback(int $amount, bool $success = true): array
    {
        $transaction = [
            'type' => 'TRANSACTION',
            'obj' => [
                'amount_cents' => $amount,
                'created_at' => '2026-11-20T12:15:00',
                'currency' => 'EGP',
                'error_occured' => false,
                'has_parent_transaction' => false,
                'id' => 987,
                'integration_id' => 456,
                'is_3d_secure' => true,
                'is_auth' => false,
                'is_capture' => false,
                'is_refunded' => false,
                'is_standalone_payment' => true,
                'is_voided' => false,
                'order' => ['id' => 654321],
                'owner' => 123,
                'pending' => false,
                'source_data' => ['pan' => '1111', 'sub_type' => 'MasterCard', 'type' => 'card'],
                'success' => $success,
            ],
        ];
        $values = $amount . '2026-11-20T12:15:00EGPfalsefalse987456truefalsefalsefalsetruefalse654321123false1111MasterCardcard'
            . ($success ? 'true' : 'false');

        return [$transaction, hash_hmac('sha512', $values, 'local-test-hmac')];
    }
}
