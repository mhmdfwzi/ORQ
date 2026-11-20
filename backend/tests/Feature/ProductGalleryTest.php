<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductGalleryTest extends TestCase
{
    use DatabaseMigrations;

    protected function migrateFreshUsing(): array
    {
        return ['--path' => array_map(fn (string $name) => "database/migrations/{$name}.php", [
            '0001_01_01_000000_create_users_table',
            '2026_01_06_000001_create_categories_table',
            '2026_01_06_000002_create_products_table',
            '2026_01_06_000003_create_product_images_table',
            '2026_01_07_000001_add_images_to_products_table',
            '2026_02_06_182700_add_offer_fields_to_products_table',
            '2026_02_20_115200_add_arabic_description_to_products',
            '2026_11_12_000001_add_perfume_fields_to_products_table',
            '2026_11_12_000002_create_product_variants_table',
        ])];
    }

    public function test_admin_can_preserve_and_replace_product_images(): void
    {
        config(['app.frontend_url' => 'http://storefront.test/']);
        Http::fake();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'phone' => '01000000001']));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $seedImage = '/media/orq/chaos.webp';
        $product = Product::create([
            'category_id' => Category::create(['name' => 'Perfumes', 'slug' => 'perfumes'])->id,
            'name' => 'Chaos',
            'slug' => 'chaos',
            'sku' => 'CHAOS',
            'description' => 'Chaos perfume',
            'price' => 1200,
            'status' => 'published',
            'images' => [$seedImage],
        ]);

        $edit = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $this->assertSame([$seedImage], array_values($edit->get('data.images')));
        $edit->call('save')->assertHasNoFormErrors();
        $this->assertSame([$seedImage], $product->fresh()->images);

        $product->primaryImage()->create(['image_path' => 'products/old.jpg', 'is_primary' => true]);
        $edit = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $edit->set('data.images', [
            UploadedFile::fake()->image('cover.jpg'),
            UploadedFile::fake()->image('detail.jpg'),
        ])->call('save')->assertHasNoFormErrors();

        [$cover, $detail] = $product->fresh()->images;
        Storage::disk('public')->assertExists([$cover, $detail]);
        $this->getJson('/api/v1/products/chaos')->assertOk()
            ->assertJsonPath('data.image', asset('storage/' . $cover))
            ->assertJsonPath('data.gallery', [asset('storage/' . $cover), asset('storage/' . $detail)]);

        $edit = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $uploadKey = $edit->instance()->form->getFlatFields()['images']->getKey();
        $fileKeys = array_reverse(array_keys($edit->get('data.images')));
        $edit->call('callSchemaComponentMethod', $uploadKey, 'reorderUploadedFiles', ['fileKeys' => $fileKeys])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame([$detail, $cover], $product->fresh()->images);
        $this->getJson('/api/v1/products/chaos')
            ->assertJsonPath('data.image', asset('storage/' . $detail))
            ->assertJsonPath('data.gallery', [asset('storage/' . $detail), asset('storage/' . $cover)]);
        Http::assertSent(fn ($request) => $request->url() === 'http://storefront.test/api/revalidate');
    }
}
