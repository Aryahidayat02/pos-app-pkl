<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_supplier_table_can_search_and_paginate_with_yajra(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 11) as $number) {
            Supplier::create([
                'name' => sprintf('Supplier %02d', $number),
                'phone' => sprintf('081234567%04d', $number),
                'address' => sprintf('Warehouse %02d', $number),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('suppliers.index'))
            ->assertOk()
            ->assertSee('suppliers-table', false);

        $pageResponse = $this->getJson(
            route('suppliers.index', $this->dataTableParameters(10)),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $pageResponse->assertOk()
            ->assertJsonPath('recordsTotal', 11)
            ->assertJsonPath('recordsFiltered', 11)
            ->assertJsonPath('data.0.name', 'Supplier 11');

        $searchResponse = $this->getJson(
            route('suppliers.index', $this->dataTableParameters(0, '0812345670003')),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $searchResponse->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Supplier 03');
    }

    public function test_supplier_table_shows_total_quantity_received_from_each_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Quantity Supplier', 'phone' => '0812345678901']);
        $category = Category::create(['name' => 'Quantity Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'QUANTITY-SUPPLIER-01',
            'name' => 'Supplied Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 12,
            'min_stock' => 1,
        ]);
        $purchase = Purchase::create([
            'purchase_number' => 'PB-QUANTITY-0001',
            'supplier_id' => $supplier->id,
            'user_id' => $admin->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 7000,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 7,
            'purchase_price' => 1000,
            'subtotal' => 7000,
        ]);

        $response = $this->actingAs($admin)->getJson(
            route('suppliers.index', $this->dataTableParameters(0)),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Quantity Supplier')
            ->assertJsonPath('data.0.supplied_quantity', 7);
    }

    public function test_supplier_phone_is_required(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Supplier Without Phone',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseMissing('suppliers', ['name' => 'Supplier Without Phone']);
    }

    public function test_supplier_phone_rejects_letters_and_more_than_13_digits(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['0812abc', '08123456789012'] as $phone) {
            $response = $this->actingAs($admin)->post(route('suppliers.store'), [
                'name' => 'Invalid Phone Supplier',
                'phone' => $phone,
            ]);

            $response->assertSessionHasErrors('phone');
        }
    }

    public function test_supplier_accepts_a_phone_with_13_digits(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Valid Phone Supplier',
            'phone' => '0812345678901',
        ]);

        $response->assertRedirect(route('suppliers.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('suppliers', ['phone' => '0812345678901']);
    }

    public function test_supplier_rejects_duplicate_names_and_phone_numbers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Supplier::create(['name' => 'Existing Unique Supplier', 'phone' => '0812345678901']);
        Supplier::create(['name' => 'Another Unique Supplier', 'phone' => '0812345678902']);

        $duplicateNameResponse = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Existing Unique Supplier',
            'phone' => '0812345678903',
        ]);

        $duplicateNameResponse->assertSessionHasErrors('name');

        $duplicatePhoneResponse = $this->post(route('suppliers.store'), [
            'name' => 'New Unique Supplier',
            'phone' => '0812345678901',
        ]);

        $duplicatePhoneResponse->assertSessionHasErrors('phone');
        $this->assertDatabaseMissing('suppliers', ['name' => 'New Unique Supplier']);
    }

    public function test_supplier_cannot_clear_phone_when_updating(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create([
            'name' => 'Existing Supplier',
            'phone' => '0812345678901',
        ]);

        $response = $this->actingAs($admin)->put(route('suppliers.update', $supplier), [
            'name' => $supplier->name,
            'phone' => '',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'phone' => '0812345678901']);
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
                ['data' => 'contact_person', 'name' => 'contact_person', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'supplied_quantity', 'name' => 'supplied_quantity', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'phone', 'name' => 'phone', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'address', 'name' => 'address', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ];
    }
}
