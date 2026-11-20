<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OnlineOrderService;
use Database\Seeders\ORQProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            '2026_01_06_000006_create_addresses_table',
            '2026_01_06_000007_create_orders_table',
            '2026_01_06_000011_create_settings_tables',
            '2026_01_07_000001_add_images_to_products_table',
            '2026_02_06_182700_add_offer_fields_to_products_table',
            '2026_11_11_224006_create_notifications_table',
            '2026_11_12_000001_add_perfume_fields_to_products_table',
            '2026_11_12_000002_create_product_variants_table',
            '2026_11_12_000003_add_guest_checkout_to_orders',
            '2026_11_18_000001_add_whatsapp_confirmation_to_orders',
            '2026_11_20_000001_add_paymob_checkout_to_orders',
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

        $this->postJson('/api/v1/orders', $data)->assertCreated()
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
