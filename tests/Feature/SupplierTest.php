<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_supplier_list_can_open_the_second_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 11) as $number) {
            Supplier::create([
                'name' => sprintf('Supplier %02d', $number),
                'phone' => sprintf('081234567%04d', $number),
            ]);
        }

        $firstPageResponse = $this->actingAs($admin)->get(route('suppliers.index'));
        $firstPageResponse->assertOk()->assertSee('page=2', false);

        $secondPageResponse = $this->get(route('suppliers.index', ['page' => 2]));
        $secondPageResponse->assertOk()
            ->assertSee('Supplier 11')
            ->assertDontSee('Supplier 01');
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
}
