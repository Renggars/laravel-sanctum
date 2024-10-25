<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'fXp9B@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'updated_at',
                    'created_at'
                ],
                'access_token'
            ])
            ->assertJson([
                'user' => [
                    'name' => 'Test User',
                    'email' => 'fXp9B@example.com',
                ],
            ]);

        $this->assertIsString($response['access_token']);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $response = $this->postJson('/api/login', [
            'email' => 'tes',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token'
            ]);

        $this->assertIsString($response['access_token']);
    }
}
