<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
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
            'discount' => 0,
            'cart_data' => json_encode([
                ['id' => $this->product->id, 'qty' => 2],
            ]),
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
            'discount' => 0,
            'cart_data' => json_encode([
                ['id' => $this->product->id, 'qty' => 15],
            ]),
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
            'discount' => 0,
            'cart_data' => json_encode([
                ['id' => $this->product->id, 'qty' => 2],
            ]),
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    public function test_detail_transaksi_dapat_menampilkan_tanggal_sebagai_datetime()
    {
        $sale = Sale::create([
            'invoice_number' => 'INV-TEST-0001',
            'user_id' => $this->user->id,
            'customer_id' => null,
            'sale_date' => '2026-11-18 03:07:15',
            'subtotal' => 20000,
            'discount' => 0,
            'total' => 20000,
            'payment_method' => 'tunai',
            'paid_amount' => 25000,
            'change_amount' => 5000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get('/sales/'.$sale->id);

        $response->assertOk();
        $response->assertSee('INV-TEST-0001');
    }
}
