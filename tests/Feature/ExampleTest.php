<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        
        // Karena halaman awal butuh login, statusnya harus 302 (Redirect)
        $response->assertStatus(302); 
    }
}