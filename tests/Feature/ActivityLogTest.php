<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_product_creation_is_recorded_with_submitted_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Audit Category']);

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'category_id' => $category->id,
            'sku' => 'AUDIT-PRODUCT-01',
            'name' => 'Product To Audit',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response->assertRedirect(route('products.index'));
        $product = Product::query()->where('sku', 'AUDIT-PRODUCT-01')->firstOrFail();
        $activityLog = ActivityLog::query()->where('model_id', $product->id)->firstOrFail();

        $this->assertSame($admin->id, $activityLog->user_id);
        $this->assertSame('create', $activityLog->action);
        $this->assertSame('Product To Audit', $activityLog->details['after']['name']);
    }

    public function test_admin_product_update_records_before_and_after_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Audit Update Category']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'AUDIT-UPDATE-01',
            'name' => 'Before Name',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 5,
            'min_stock' => 1,
        ]);

        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'category_id' => $category->id,
            'sku' => $product->sku,
            'name' => 'After Name',
            'unit' => $product->unit,
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'min_stock' => 1,
        ]);

        $response->assertRedirect(route('products.index'));
        $activityLog = ActivityLog::query()->where('model_id', $product->id)->latest('id')->firstOrFail();

        $this->assertSame('update', $activityLog->action);
        $this->assertSame('Before Name', $activityLog->details['before']['name']);
        $this->assertSame('After Name', $activityLog->details['after']['name']);
    }

    public function test_admin_user_creation_does_not_store_password_in_activity_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Audited Cashier',
            'email' => 'audited-cashier@example.test',
            'role' => 'kasir',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('users.index'));
        $activityLog = ActivityLog::query()->where('model', 'User')->latest('id')->firstOrFail();

        $this->assertArrayNotHasKey('password', $activityLog->details['after']);
        $this->assertSame('Audited Cashier', $activityLog->details['after']['name']);
    }

    public function test_activity_detail_page_escapes_admin_entered_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activityLog = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'update',
            'model' => 'Product',
            'model_id' => 5,
            'description' => '<img src=x onerror=alert(1)>',
            'details' => [
                'before' => [],
                'after' => ['name' => '<img src=x onerror=alert(1)>'],
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('logs.show', $activityLog));

        $response->assertOk()
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);

        $modalResponse = $this->actingAs($admin)->get(route('logs.show', $activityLog), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $modalResponse->assertOk()
            ->assertViewIs('logs.partials.detail-content')
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_cashier_cannot_view_activity_details(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $activityLog = ActivityLog::create([
            'action' => 'create',
            'model' => 'Product',
            'description' => 'A product was created.',
        ]);

        $response = $this->actingAs($cashier)->get(route('logs.show', $activityLog));

        $response->assertForbidden();
    }
}
