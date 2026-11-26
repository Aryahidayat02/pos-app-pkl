<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_rejects_duplicate_skus_and_barcodes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Unique Product Category']);
        Product::create([
            'category_id' => $category->id,
            'sku' => 'EXISTING-SKU',
            'barcode' => '1234567890123',
            'name' => 'Existing Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $duplicateSkuResponse = $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'EXISTING-SKU',
            'barcode' => '1234567890124',
            'name' => 'Duplicate SKU Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 1,
            'min_stock' => 0,
        ]);

        $duplicateSkuResponse->assertSessionHasErrors('sku');

        $duplicateBarcodeResponse = $this->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'NEW-UNIQUE-SKU',
            'barcode' => '1234567890123',
            'name' => 'Duplicate Barcode Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 1,
            'min_stock' => 0,
        ]);

        $duplicateBarcodeResponse->assertSessionHasErrors('barcode');
        $this->assertDatabaseMissing('products', ['sku' => 'NEW-UNIQUE-SKU']);
        $this->assertDatabaseMissing('products', ['name' => 'Duplicate SKU Product']);
    }

    public function test_edit_product_form_displays_sku_and_barcode_separately(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Edit Product Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'EDIT-FORM-01',
            'barcode' => '1234567890123',
            'name' => 'Product With Barcode',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('products.edit', $product));

        $response->assertOk()
            ->assertSee('for="product-sku"', false)
            ->assertSee('name="sku"', false)
            ->assertSee('value="'.$product->sku.'"', false)
            ->assertSee('for="product-barcode"', false)
            ->assertSee('name="barcode"', false)
            ->assertSee('value="'.$product->barcode.'"', false)
            ->assertDontSee('SKU / Barcode', false);
    }

    public function test_edit_product_form_shows_the_model_category_in_the_search_input(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $selectedCategory = Category::create(['name' => 'Selected Category']);
        $otherCategory = Category::create(['name' => 'Other Category']);
        $product = Product::create([
            'category_id' => $selectedCategory->id,
            'sku' => 'EDIT-CATEGORY-01',
            'name' => 'Editable Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('products.edit', $product));

        $response->assertOk()
            ->assertSee('id="category-search"', false)
            ->assertSee('list="product-categories"', false)
            ->assertSee('value="'.$selectedCategory->name.'"', false)
            ->assertSee('name="category_id" data-category-search-value value="'.$selectedCategory->id.'"', false)
            ->assertSee('<option value="'.$selectedCategory->name.'" data-category-id="'.$selectedCategory->id.'">', false)
            ->assertSee('<option value="'.$otherCategory->name.'" data-category-id="'.$otherCategory->id.'">', false);
    }

    public function test_product_create_form_receives_a_new_product_model(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::create(['name' => 'Create Form Category']);

        $response = $this->actingAs($admin)->get(route('products.create'));

        $response->assertOk()
            ->assertSee('Tambah Produk')
            ->assertSee('id="category-search"', false)
            ->assertSee('name="category_id" data-category-search-value', false)
            ->assertSee('data-product-image-preview', false)
            ->assertSee('data-product-image-input', false)
            ->assertSee('list="product-unit-options"', false)
            ->assertSee('product-purchase-price', false)
            ->assertSee('product-selling-price', false);
    }

    public function test_product_store_rejects_a_selling_price_below_purchase_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Price Validation Category']);

        $response = $this->actingAs($admin)->from(route('products.create'))->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'INVALID-PRICE-01',
            'name' => 'Invalid Price Product',
            'unit' => 'pcs',
            'purchase_price' => 2000,
            'selling_price' => 1999,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response->assertSessionHasErrors('selling_price');
        $this->assertDatabaseMissing('products', ['sku' => 'INVALID-PRICE-01']);
    }

    public function test_product_update_rejects_a_selling_price_below_purchase_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Edit Price Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'EDIT-PRICE-01',
            'name' => 'Valid Price Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('products.edit', $product))
            ->put(route('products.update', $product), [
                'category_id' => $category->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'unit' => $product->unit,
                'purchase_price' => 2000,
                'selling_price' => 1999,
                'min_stock' => $product->min_stock,
            ]);

        $response->assertSessionHasErrors('selling_price');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'selling_price' => 2000]);
    }

    public function test_new_products_appear_first_in_the_default_server_side_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Newest Products Category']);

        foreach ([
            ['OLD-PRODUCT-01', 'Older Product', now()->subDay()],
            ['NEW-PRODUCT-01', 'Newest Product', now()],
        ] as [$sku, $name, $createdAt]) {
            Product::create([
                'category_id' => $category->id,
                'sku' => $sku,
                'name' => $name,
                'unit' => 'pcs',
                'purchase_price' => 1000,
                'selling_price' => 2000,
                'stock' => 5,
                'min_stock' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $response = $this->actingAs($admin)->getJson(route('products.index', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => '', 'regex' => false],
            'order' => [],
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'products.id', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'image_display', 'name' => 'image', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'sku', 'name' => 'products.sku', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'barcode', 'name' => 'products.barcode', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'name', 'name' => 'products.name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'category_name', 'name' => 'category_name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'selling_price', 'name' => 'products.selling_price', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'stock_display', 'name' => 'products.stock', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'status_display', 'name' => 'products.is_active', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk()
            ->assertJsonPath('data.0.sku', 'NEW-PRODUCT-01');
    }
}
