<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_stok_masuk_tampil_di_modal_halaman_riwayat(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'PT Supplier Test', 'phone' => '081234']);
        $kategori = Category::create(['name' => 'Minuman']);
        $produk = Product::create([
            'category_id' => $kategori->id,
            'sku' => 'MODAL01', 'name' => 'Teh Modal Test', 'unit' => 'pcs',
            'purchase_price' => 1000, 'selling_price' => 2000,
            'stock' => 10, 'min_stock' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/purchases');

        $response->assertOk()
            ->assertSee('stockInModal')
            ->assertSee('data-bs-target="#stockInModal"', false)
            ->assertSee('id="stockInSupplierSearch"', false)
            ->assertSee('data-search-kind="supplier"', false)
            ->assertSee('data-search-kind="product"', false)
            ->assertDontSee('<select name="supplier_id"', false)
            ->assertDontSee('class="form-select stock-product-select"', false)
            ->assertSee($supplier->name)
            ->assertSee($produk->name);
    }

    public function test_stok_masuk_menambah_stok_produk()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'PT Supp', 'phone' => '081234']);
        $kategori = Category::create(['name' => 'Minuman']);
        $produk = Product::create([
            'category_id' => $kategori->id,
            'sku' => 'TST01', 'name' => 'Teh Test', 'unit' => 'pcs',
            'purchase_price' => 1000, 'selling_price' => 2000,
            'stock' => 10, 'min_stock' => 2,
        ]);

        $response = $this->actingAs($admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'purchase_date' => date('Y-m-d'),
            'items' => [
                ['product_id' => $produk->id, 'quantity' => 5, 'purchase_price' => 1000],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(15, $produk->fresh()->stock);
    }

    public function test_duplicate_products_in_a_purchase_are_rejected_without_changing_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Duplicate Test Supplier', 'phone' => '08123456789']);
        $category = Category::create(['name' => 'Duplicate Purchase Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'DUP-PURCHASE-01',
            'name' => 'Duplicate Purchase Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'min_stock' => 2,
        ]);

        $response = $this->actingAs($admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'purchase_date' => date('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2, 'purchase_price' => 1000],
                ['product_id' => $product->id, 'quantity' => 3, 'purchase_price' => 1000],
            ],
        ]);

        $response->assertSessionHasErrors('items.1.product_id');
        $this->assertDatabaseCount('purchases', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }
}
