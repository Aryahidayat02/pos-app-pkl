<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
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
            'description' => 'Aneka Minuman'
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
}