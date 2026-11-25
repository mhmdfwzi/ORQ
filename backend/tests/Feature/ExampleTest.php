<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Artisan::call('migrate', ['--path' => [
            'database/migrations/0001_01_01_000000_create_users_table.php',
            'database/migrations/2026_01_06_000001_create_categories_table.php',
            'database/migrations/2026_01_06_000002_create_products_table.php',
            'database/migrations/2026_01_06_000008_create_reviews_table.php',
            'database/migrations/2026_01_06_000011_create_settings_tables.php',
            'database/migrations/2026_02_06_182700_add_offer_fields_to_products_table.php',
        ]]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
