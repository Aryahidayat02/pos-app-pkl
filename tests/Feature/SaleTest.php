<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
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

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_transaksi_berhasil_mengurangi_stok_dan_menyimpan_total()
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 25000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2],
            ],
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
                ['product_id' => $this->product->id, 'quantity' => 15],
            ],
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
                ['product_id' => $this->product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    public function test_duplicate_products_in_a_sale_are_rejected_without_changing_stock(): void
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 20000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 1],
                ['product_id' => $this->product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('items.1.product_id');
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $this->product->fresh()->stock);
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

    public function test_waktu_transaksi_disimpan_dalam_wib(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 19, 10, 30, 15, 'Asia/Jakarta'));

        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 20000,
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));

        $sale = Sale::latest('id')->firstOrFail();
        $this->assertSame('2026-11-19 10:30:15', $sale->sale_date->format('Y-m-d H:i:s'));
        $this->assertSame($sale->sale_date->format('Y-m-d H:i:s'), $sale->created_at->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Jakarta', $sale->sale_date->timezoneName);
    }

    public function test_sale_applies_percentage_and_nominal_discounts_per_item_and_saves_note(): void
    {
        $secondProduct = Product::create([
            'category_id' => $this->product->category_id,
            'sku' => 'SNK02',
            'name' => 'Another Snack',
            'unit' => 'pcs',
            'purchase_price' => 5000,
            'selling_price' => 10000,
            'stock' => 10,
            'min_stock' => 2,
        ]);

        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 34000,
            'note' => 'Pesanan dibungkus terpisah',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'discount_type' => 'percent',
                    'discount_value' => 10,
                ],
                [
                    'product_id' => $secondProduct->id,
                    'quantity' => 2,
                    'discount_type' => 'amount',
                    'discount_value' => 4000,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales.index'))
            ->assertSessionHasNoErrors();

        $sale = Sale::query()->with('items')->latest('id')->firstOrFail();
        $this->assertSame('40000.00', $sale->subtotal);
        $this->assertSame('6000.00', $sale->discount);
        $this->assertSame('34000.00', $sale->total);
        $this->assertSame('Pesanan dibungkus terpisah', $sale->note);
        $this->assertSame('percent', $sale->items[0]->discount_type);
        $this->assertSame('2000.00', $sale->items[0]->discount);
        $this->assertSame('amount', $sale->items[1]->discount_type);
        $this->assertSame('4000.00', $sale->items[1]->discount);
        $this->assertSame(8, $this->product->fresh()->stock);
        $this->assertSame(8, $secondProduct->fresh()->stock);
    }

    public function test_sale_rejects_a_discount_larger_than_the_item_subtotal(): void
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 1,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'discount_type' => 'amount',
                    'discount_value' => 10001,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.discount_value');
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $this->product->fresh()->stock);
    }

    public function test_sale_rejects_percentage_discounts_above_one_hundred(): void
    {
        $response = $this->actingAs($this->user)->post('/kasir', [
            'payment_method' => 'tunai',
            'paid_amount' => 1,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'discount_type' => 'percent',
                    'discount_value' => 100.01,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.discount_value');
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $this->product->fresh()->stock);
    }
}
