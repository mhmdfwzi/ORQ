<?php

namespace Tests\Feature;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class StorefrontGoogleAccountTest extends TestCase
{
    use RefreshDatabase;

    private const API = '/api/v1/account';
    private const STORE = 'https://storefront.test';
    private const CALLBACK = self::STORE . self::API . '/google/callback';

    protected function beforeRefreshingDatabase(): void
    {
        RefreshDatabaseState::$migrated = false;
    }

    protected function afterRefreshingDatabase(): void
    {
        $this->beforeApplicationDestroyed(static function (): void {
            RefreshDatabaseState::$migrated = false;
        });
    }

    protected function migrateFreshUsing(): array
    {
        return ['--path' => array_map(fn (string $name) => "database/migrations/{$name}.php", [
            '0001_01_01_000000_create_users_table',
            '2026_01_09_000001_add_social_ids_to_users_table',
            '2026_11_21_000002_enable_google_customer_accounts',
        ])];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:' . base64_encode(str_repeat('k', 32)),
            'app.frontend_url' => self::STORE,
            'services.google.client_id' => 'google-web-test',
            'services.google.client_secret' => 'test-only-secret',
            'services.google.redirect' => self::CALLBACK,
        ]);
    }

    public function test_sign_in_starts_a_stateful_pkce_flow(): void
    {
        $response = $this->get(self::API . '/google')->assertRedirect();
        $redirect = $response->headers->get('Location');
        $this->assertSame('accounts.google.com', parse_url($redirect, PHP_URL_HOST));
        parse_str(parse_url($redirect, PHP_URL_QUERY), $params);
        $this->assertSame(self::CALLBACK, $params['redirect_uri']);
        $this->assertSame('openid profile email', $params['scope']);
        $this->assertSame('S256', $params['code_challenge_method']);
        $response->assertSessionHas('state', $params['state'])->assertSessionHas('code_verifier');
    }

    public function test_callback_creates_a_customer_and_signout_only_ends_the_storefront_session(): void
    {
        $admin = User::factory()->create(['phone' => '01000000001', 'role' => 'admin']);
        $this->actingAs($admin, 'web');
        $history = [];
        $this->stubGoogle($history, ['sub' => 'google-subject-one', 'name' => 'Test Shopper', 'email' => 'shopper@example.test']);

        $this->completeGoogleSignIn()->assertRedirect(self::STORE . '/account')->assertSessionMissing('state');
        $customer = User::where('google_id', 'google-subject-one')->sole();
        $this->assertTrue($customer->isCustomer());
        $this->assertNull($customer->phone);
        $this->assertNotNull($customer->email_verified_at);
        $this->assertAuthenticatedAs($customer, 'storefront');
        $this->assertAuthenticatedAs($admin, 'web');
        parse_str((string) $history[0]['request']->getBody(), $exchange);
        $this->assertSame('test-code-verifier', $exchange['code_verifier']);
        $this->assertSame(self::CALLBACK, $exchange['redirect_uri']);

        $this->getJson(self::API . '/session')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('customer.email', 'shopper@example.test');
        $this->postJson(self::API . '/logout')->assertNoContent();
        $this->assertGuest('storefront');
        $this->assertAuthenticatedAs($admin, 'web');
        $this->getJson(self::API . '/session')->assertJsonPath('customer', null)->assertJsonPath('csrfToken', null);
    }

    public function test_rejects_invalid_state_and_email_conflicts_and_keeps_the_same_linked_identity(): void
    {
        $history = [];
        $this->stubGoogle($history, ['sub' => 'google-subject-two', 'email' => 'existing@example.test']);
        $this->completeGoogleSignIn('wrong-state')->assertRedirect(self::STORE . '/account?google=failed');
        $this->assertCount(0, $history);
        $this->assertGuest('storefront');

        $existing = User::factory()->create(['phone' => '01000000002', 'email' => 'Existing@example.test', 'status' => 'active']);
        Socialite::forgetDrivers();
        $this->completeGoogleSignIn()->assertRedirect(self::STORE . '/account?google=existing');
        $this->assertNull($existing->fresh()->google_id);
        $this->assertGuest('storefront');

        $existing->update(['google_id' => 'google-subject-two']);
        $this->stubGoogle($history, ['sub' => 'google-subject-two', 'email' => 'changed@example.test']);
        $this->completeGoogleSignIn()->assertRedirect(self::STORE . '/account');
        $this->assertAuthenticatedAs($existing, 'storefront');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_unverified_email_and_cancelled_google_consent_do_not_sign_in(): void
    {
        $history = [];
        $this->stubGoogle($history, ['sub' => 'google-unverified', 'email' => 'unverified@example.test', 'email_verified' => false]);
        $this->completeGoogleSignIn()->assertRedirect(self::STORE . '/account?google=failed');
        $this->get(self::API . '/google/callback?error=access_denied')->assertRedirect(self::STORE . '/account?google=cancelled');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest('storefront');
    }

    private function completeGoogleSignIn(string $state = 'test-state'): TestResponse
    {
        return $this->withSession(['state' => 'test-state', 'code_verifier' => 'test-code-verifier'])
            ->get(self::API . '/google/callback?code=test-code&state=' . $state);
    }

    private function stubGoogle(array &$history, array $identity): void
    {
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode(['access_token' => 'mock-access', 'scope' => 'openid profile email'])),
            new Response(200, [], json_encode($identity + ['email_verified' => true])),
        ]));
        $stack->push(Middleware::history($history));
        $client = new Client(['handler' => $stack]);

        Socialite::forgetDrivers();
        Socialite::extend('google', fn ($app) => (new GoogleProvider(
            $app['request'], 'google-web-test', 'test-only-secret', self::CALLBACK,
        ))->setHttpClient($client));
    }
}
