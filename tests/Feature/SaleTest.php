<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'kasir']);
        $kategori = Category::create(['name' => 'Snack']);
        $this->product = Product::create([
            'category_id' => $kategori->id,
            'sku' => 'SNK01', 'name' => 'Chitato', 'unit' => 'pcs',
            'purchase_price' => 5000, 'selling_price' => 10000,
            'stock' => 10, 'min_stock' => 2,
        ]);
    }

    public function test_transaksi_berhasil_mengurangi_stok_dan_menyimpan_total()
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 25000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(8, $this->product->fresh()->stock);
        $this->assertDatabaseHas('sales', ['total' => 20000]);
    }

    public function test_transaksi_melebihi_stok_ditolak()
    {
        // Karena di controller pakai try-catch, error-nya masuk ke session 'error'
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 100000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 15]
            ]
        ]);

        // Cek apakah ada error di session atau redirect balik
        $response->assertSessionHas('error');
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    public function test_transaksi_uang_kurang_ditolak()
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 5000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2]
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(10, $this->product->fresh()->stock);
    }
}