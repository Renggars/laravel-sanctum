<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
