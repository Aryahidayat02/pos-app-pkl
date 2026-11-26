<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_png_is_stored_as_a_500_pixel_png_cover(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Image Test Category']);

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'IMAGE-PNG-01',
            'name' => 'PNG Cover Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
            'image' => UploadedFile::fake()->image('product.png', 900, 600, 'png'),
        ]);

        $response->assertRedirect(route('products.index'))
            ->assertSessionHasNoErrors();

        $imagePath = Product::query()->where('sku', 'IMAGE-PNG-01')->value('image');
        $this->assertIsString($imagePath);
        $this->assertStringEndsWith('.png', $imagePath);
        $this->assertStoredCover($imagePath, 'image/png');
    }

    public function test_profile_jpeg_is_stored_as_a_500_pixel_jpeg_cover(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile.index'))
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => UploadedFile::fake()->image('profile.jpg', 800, 1200, 'jpeg'),
            ]);

        $response->assertRedirect(route('profile.index'))
            ->assertSessionHasNoErrors();

        $photoPath = $user->fresh()->photo;
        $this->assertIsString($photoPath);
        $this->assertStringEndsWith('.jpg', $photoPath);
        $this->assertStoredCover($photoPath, 'image/jpeg');
    }

    public function test_store_logo_is_processed_by_the_shared_image_service(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->from(route('settings.index'))
            ->post(route('settings.update'), [
                'store_name' => 'Image Test Store',
                'store_address' => '1 Example Street',
                'store_phone' => '08123456789',
                'receipt_footer' => 'Thank you',
                'logo' => UploadedFile::fake()->image('store.png', 700, 500, 'png'),
            ]);

        $response->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors();

        $logoPath = Setting::query()->where('key', 'store_logo')->value('value');
        $this->assertIsString($logoPath);
        $this->assertStringEndsWith('.png', $logoPath);
        $this->assertStoredCover($logoPath, 'image/png');
    }

    public function test_product_rejects_an_image_with_a_webp_filename(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Rejected Image Category']);

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'IMAGE-WEBP-01',
            'name' => 'Rejected WebP Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
            'image' => UploadedFile::fake()->image('product.webp', 500, 500),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('products', ['sku' => 'IMAGE-WEBP-01']);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_deleting_a_product_also_deletes_its_stored_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Delete Image Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'DELETE-IMAGE-01',
            'name' => 'Product With Image',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
            'image' => UploadedFile::fake()->image('delete-product.png', 500, 500)->store('products', 'public'),
        ]);

        $imagePath = $product->image;
        Storage::disk('public')->assertExists($imagePath);

        $response = $this->actingAs($admin)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        Storage::disk('public')->assertMissing($imagePath);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    private function assertStoredCover(string $path, string $mimeType): void
    {
        Storage::disk('public')->assertExists($path);

        $image = getimagesize(Storage::disk('public')->path($path));

        $this->assertIsArray($image);
        $this->assertSame(500, $image[0]);
        $this->assertSame(500, $image[1]);
        $this->assertSame($mimeType, $image['mime']);
    }
}
