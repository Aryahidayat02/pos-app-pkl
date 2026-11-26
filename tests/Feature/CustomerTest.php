<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_table_can_search_and_paginate_with_yajra(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 11) as $number) {
            Customer::create([
                'name' => sprintf('Customer %02d', $number),
                'phone' => sprintf('081234567%04d', $number),
                'email' => sprintf('customer%02d@example.test', $number),
            ]);
        }

        $this->actingAs($user)
            ->get(route('customers.index'))
            ->assertOk()
            ->assertSee('customers-table', false);

        $pageResponse = $this->getJson(
            route('customers.index', $this->dataTableParameters(10)),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $pageResponse->assertOk()
            ->assertJsonPath('recordsTotal', 11)
            ->assertJsonPath('recordsFiltered', 11)
            ->assertJsonPath('data.0.name', 'Customer 11');

        $searchResponse = $this->getJson(
            route('customers.index', $this->dataTableParameters(0, '0812345670003')),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $searchResponse->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Customer 03');
    }

    public function test_customer_profile_edit_opens_from_the_current_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'name' => 'Customer Edit Modal',
            'phone' => '0812345678901',
        ]);

        $this->actingAs($admin)
            ->get(route('customers.show', $customer))
            ->assertOk()
            ->assertSee('data-form-modal-url="'.route('customers.edit', $customer).'"', false)
            ->assertSee('id="masterDataFormModal"', false);
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

    public function test_customer_rejects_duplicate_phone_numbers_and_email_addresses(): void
    {
        $user = User::factory()->create();
        Customer::create([
            'name' => 'Existing Customer One',
            'phone' => '0812345678901',
            'email' => 'customer-one@example.test',
        ]);
        Customer::create([
            'name' => 'Existing Customer Two',
            'phone' => '0812345678902',
            'email' => 'customer-two@example.test',
        ]);

        $duplicatePhoneResponse = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'New Customer One',
            'phone' => '0812345678901',
            'email' => 'new-customer@example.test',
        ]);

        $duplicatePhoneResponse->assertSessionHasErrors('phone');

        $duplicateEmailResponse = $this->post(route('customers.store'), [
            'name' => 'New Customer Two',
            'phone' => '0812345678903',
            'email' => 'customer-one@example.test',
        ]);

        $duplicateEmailResponse->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('customers', ['name' => 'New Customer Two']);
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
                ['data' => 'phone', 'name' => 'phone', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'email', 'name' => 'email', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'sales_count', 'name' => 'sales_count', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
        ];
    }
}
