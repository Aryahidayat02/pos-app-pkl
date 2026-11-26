<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_operational_store_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('settings.update'), [
            'store_name' => 'Toko Nusantara',
            'store_address' => 'Jl. Melati 12',
            'store_phone' => '081234567890',
            'receipt_footer' => 'Terima kasih!',
            'default_payment_method' => 'qris',
            'show_logo_on_receipt' => '1',
            'low_stock_alert' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'Toko Nusantara']);
        $this->assertDatabaseHas('settings', ['key' => 'default_payment_method', 'value' => 'qris']);
        $this->assertDatabaseHas('settings', ['key' => 'show_logo_on_receipt', 'value' => '1']);
        $this->assertDatabaseHas('settings', ['key' => 'low_stock_alert', 'value' => '1']);
    }

    public function test_optional_setting_switches_can_be_disabled(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('settings.update'), [
            'store_name' => 'Toko Utama',
            'store_address' => 'Jl. Utama 1',
            'store_phone' => '081200000000',
            'receipt_footer' => '',
            'default_payment_method' => 'tunai',
        ]);

        $response->assertRedirect();

        $this->assertSame('0', Setting::get('show_logo_on_receipt'));
        $this->assertSame('0', Setting::get('low_stock_alert'));
    }
}
