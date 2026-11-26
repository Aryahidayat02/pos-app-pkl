<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ServerSideTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_are_searched_by_the_server_side_table(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Table Test Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TABLE-PRODUCT-01',
            'name' => 'Server Product',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response = $this->actingAs($admin)->getDataTableJson(
            'products.index',
            [
                ['DT_RowIndex', 'products.id', false, false],
                ['image_display', 'image', false, false],
                ['sku', 'products.sku', true, true],
                ['barcode', 'products.barcode', true, true],
                ['name', 'products.name', true, true],
                ['category_name', 'category_name', true, true],
                ['selling_price', 'products.selling_price', false, true],
                ['stock_display', 'products.stock', false, true],
                ['status_display', 'products.is_active', false, true],
                ['actions', 'actions', false, false],
            ],
            'TABLE-PRODUCT-01',
        );

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.sku', 'TABLE-PRODUCT-01');

        $this->assertStringContainsString(
            'data-form-modal-url="'.route('products.edit', $product).'"',
            $response->json('data.0.actions'),
        );
    }

    public function test_products_are_sorted_by_name_on_the_server_side_table(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Table Sort Category']);

        foreach ([
            ['TABLE-SORT-Z', 'Zebra Product'],
            ['TABLE-SORT-A', 'Apple Product'],
        ] as [$sku, $name]) {
            Product::create([
                'category_id' => $category->id,
                'sku' => $sku,
                'name' => $name,
                'unit' => 'pcs',
                'purchase_price' => 1000,
                'selling_price' => 2000,
                'stock' => 5,
                'min_stock' => 1,
            ]);
        }

        $response = $this->actingAs($admin)->getDataTableJson(
            'products.index',
            [
                ['DT_RowIndex', 'products.id', false, false],
                ['image_display', 'image', false, false],
                ['sku', 'products.sku', true, true],
                ['barcode', 'products.barcode', true, true],
                ['name', 'products.name', true, true],
                ['category_name', 'category_name', true, true],
                ['selling_price', 'products.selling_price', false, true],
                ['stock_display', 'products.stock', false, true],
                ['status_display', 'products.is_active', false, true],
                ['actions', 'actions', false, false],
            ],
            '',
            [['column' => 4, 'dir' => 'asc']],
        );

        $response->assertOk()
            ->assertJsonPath('data.0.sku', 'TABLE-SORT-A')
            ->assertJsonPath('data.1.sku', 'TABLE-SORT-Z');
    }

    public function test_cashier_sees_only_own_sales_in_the_server_side_table(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $otherCashier = User::factory()->create(['role' => 'kasir']);
        $this->createSale($cashier, 'INV-TABLE-OWN');
        $this->createSale($otherCashier, 'INV-TABLE-OTHER');

        $response = $this->actingAs($cashier)->getDataTableJson(
            'sales.index',
            [
                ['DT_RowIndex', 'sales.id', false, false],
                ['invoice_number', 'sales.invoice_number', true, true],
                ['sale_date', 'sales.sale_date', false, true],
                ['cashier_name', 'cashier_name', true, true],
                ['customer_name', 'customer_name', true, true],
                ['total', 'sales.total', false, true],
                ['status_display', 'sales.status', false, true],
                ['actions', 'actions', false, false],
            ],
        );

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.invoice_number', 'INV-TABLE-OWN');
    }

    public function test_purchases_are_searched_by_supplier_on_the_server(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $supplier = Supplier::create(['name' => 'Unique Table Supplier', 'phone' => '081234567801']);
        Purchase::create([
            'purchase_number' => 'PB-TABLE-0001',
            'supplier_id' => $supplier->id,
            'user_id' => $admin->id,
            'purchase_date' => '2026-11-20',
            'total_amount' => 12500,
        ]);

        $response = $this->actingAs($admin)->getDataTableJson(
            'purchases.index',
            [
                ['DT_RowIndex', 'purchases.id', false, false],
                ['purchase_number', 'purchases.purchase_number', true, true],
                ['purchase_date', 'purchases.purchase_date', false, true],
                ['supplier_name', 'supplier_name', true, true],
                ['total_amount', 'purchases.total_amount', false, true],
                ['staff_name', 'staff_name', true, true],
                ['actions', 'actions', false, false],
            ],
            'Unique Table Supplier',
        );

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.purchase_number', 'PB-TABLE-0001');
    }

    public function test_users_are_paginated_by_the_server_side_table(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Table User']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Cashier Table User']);

        $response = $this->actingAs($admin)->getDataTableJson(
            'users.index',
            [
                ['DT_RowIndex', 'users.id', false, false],
                ['name', 'users.name', true, true],
                ['email', 'users.email', true, true],
                ['role', 'users.role', true, true],
                ['status_display', 'users.is_active', false, true],
                ['actions', 'actions', false, false],
            ],
            'Cashier Table User',
        );

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 2)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Cashier Table User');

        $this->assertStringContainsString(
            'data-form-modal-url="'.route('users.edit', $cashier).'"',
            $response->json('data.0.actions'),
        );
    }

    public function test_activity_logs_are_searched_by_the_server_side_table(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activityLog = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'model' => 'Product',
            'model_id' => 1,
            'description' => 'Unique server-side audit description',
        ]);

        $response = $this->actingAs($admin)->getDataTableJson(
            'logs.index',
            [
                ['DT_RowIndex', 'activity_logs.id', false, false],
                ['created_at', 'activity_logs.created_at', false, true],
                ['user_name', 'user_name', true, true],
                ['action_display', 'activity_logs.action', false, true],
                ['description', 'activity_logs.description', true, true],
                ['details_action', 'details_action', false, false],
            ],
            'Unique server-side audit description',
        );

        $response->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.description', 'Unique server-side audit description');

        $detailButton = $response->json('data.0.details_action');

        $this->assertStringContainsString('data-bs-target="#activityLogDetailModal"', $detailButton);
        $this->assertStringContainsString(route('logs.show', $activityLog), $detailButton);
    }

    private function getDataTableJson(
        string $routeName,
        array $columns,
        string $search = '',
        ?array $order = null,
    ): TestResponse {
        return $this->getJson(route($routeName, [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => $search, 'regex' => false],
            'order' => $order ?? [],
            'columns' => array_map(function (array $column): array {
                return [
                    'data' => $column[0],
                    'name' => $column[1],
                    'searchable' => $column[2] ? 'true' : 'false',
                    'orderable' => $column[3] ? 'true' : 'false',
                ];
            }, $columns),
        ]), ['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function createSale(User $cashier, string $invoiceNumber): Sale
    {
        return Sale::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $cashier->id,
            'customer_id' => null,
            'sale_date' => '2026-11-20 08:00:00',
            'subtotal' => 2000,
            'discount' => 0,
            'total' => 2000,
            'payment_method' => 'tunai',
            'paid_amount' => 2000,
            'change_amount' => 0,
            'status' => 'paid',
        ]);
    }
}
