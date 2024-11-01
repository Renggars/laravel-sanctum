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

    public function testLoginWithMissingEmail()
    {
        $this->seed(UserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
            ]);
    }

    public function testLoginWithMissingPassword()
    {
        $this->seed(UserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'tes@example.com',
            'password' => '',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
    }
}
