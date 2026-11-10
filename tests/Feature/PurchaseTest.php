<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

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
                ['product_id' => $produk->id, 'quantity' => 5, 'purchase_price' => 1000]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(15, $produk->fresh()->stock);
    }
}