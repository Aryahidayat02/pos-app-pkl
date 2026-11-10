<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login()
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }

    public function test_kasir_tidak_bisa_membuka_halaman_admin()
    {
        $kasir = User::factory()->create(['role' => 'kasir']);
        $response = $this->actingAs($kasir)->get('/users');
        $response->assertStatus(403);
    }
}