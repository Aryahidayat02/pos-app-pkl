<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_list_can_open_the_second_page(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 11) as $number) {
            Customer::create([
                'name' => sprintf('Customer %02d', $number),
                'phone' => sprintf('081234567%04d', $number),
            ]);
        }

        $firstPageResponse = $this->actingAs($user)->get(route('customers.index'));
        $firstPageResponse->assertOk()->assertSee('page=2', false);

        $secondPageResponse = $this->get(route('customers.index', ['page' => 2]));
        $secondPageResponse->assertOk()
            ->assertSee('Customer 11')
            ->assertDontSee('Customer 01');
    }

    public function test_customer_phone_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Customer Without Phone',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseMissing('customers', ['name' => 'Customer Without Phone']);
    }

    public function test_customer_phone_rejects_letters_and_more_than_13_digits(): void
    {
        $user = User::factory()->create();

        foreach (['0812abc', '08123456789012'] as $phone) {
            $response = $this->actingAs($user)->post(route('customers.store'), [
                'name' => 'Invalid Phone Customer',
                'phone' => $phone,
            ]);

            $response->assertSessionHasErrors('phone');
        }
    }

    public function test_customer_accepts_a_phone_with_13_digits(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Valid Phone Customer',
            'phone' => '0812345678901',
        ]);

        $response->assertRedirect(route('customers.index'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('customers', ['phone' => '0812345678901']);
    }

    public function test_customer_cannot_clear_phone_when_updating(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Existing Customer',
            'phone' => '0812345678901',
        ]);

        $response = $this->actingAs($user)->put(route('customers.update', $customer), [
            'name' => $customer->name,
            'phone' => '',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'phone' => '0812345678901']);
    }
}
