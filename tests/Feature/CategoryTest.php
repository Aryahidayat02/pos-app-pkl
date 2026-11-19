<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_menambah_kategori_dan_duplikat_ditolak()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Minuman',
            'description' => 'Aneka Minuman',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['name' => 'Minuman']);

        $responseDup = $this->actingAs($admin)->post('/categories', [
            'name' => 'Minuman',
        ]);
        $responseDup->assertSessionHasErrors('name');
    }

    public function test_kategori_yang_punya_produk_tidak_bisa_dihapus()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategori = Category::factory()->create();
        Product::factory()->create(['category_id' => $kategori->id]);

        $response = $this->actingAs($admin)->delete("/categories/{$kategori->id}");

        $this->assertDatabaseHas('categories', ['id' => $kategori->id]);
    }

    public function test_admin_bisa_membuka_halaman_kedua_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 11) as $number) {
            Category::create(['name' => sprintf('Pagination Category %02d', $number)]);
        }

        $firstPageResponse = $this->actingAs($admin)->get(route('categories.index'));
        $firstPageResponse->assertOk()->assertSee('page=2', false);

        $secondPageResponse = $this->get(route('categories.index', ['page' => 2]));
        $secondPageResponse->assertOk()
            ->assertSee('Pagination Category 11')
            ->assertDontSee('Pagination Category 01');
    }
}
