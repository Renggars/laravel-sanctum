<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'tes@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token'
            ]);

        $this->assertIsString($response['access_token']);
    }
}
