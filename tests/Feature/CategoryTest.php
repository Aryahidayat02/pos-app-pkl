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

    public function test_admin_can_search_and_paginate_categories_with_yajra(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 11) as $number) {
            Category::create(['name' => sprintf('Pagination Category %02d', $number)]);
        }

        $this->actingAs($admin)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('categories-table', false);

        $pageResponse = $this->getJson(
            route('categories.index', $this->dataTableParameters(10)),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $pageResponse->assertOk()
            ->assertJsonPath('recordsTotal', 11)
            ->assertJsonPath('recordsFiltered', 11)
            ->assertJsonPath('data.0.name', 'Pagination Category 11');

        $searchResponse = $this->getJson(
            route('categories.index', $this->dataTableParameters(0, 'Pagination Category 03')),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $searchResponse->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Pagination Category 03');
    }

    private function dataTableParameters(int $start, string $search = ''): array
    {
        return [
            'draw' => 1,
            'start' => $start,
            'length' => 10,
            'search' => ['value' => $search, 'regex' => false],
            'order' => [['column' => 1, 'dir' => 'asc']],
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'name', 'name' => 'name', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'description', 'name' => 'description', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'products_count', 'name' => 'products_count', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ];
    }
}
