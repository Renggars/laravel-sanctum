<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\TodolistSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodolistTest extends TestCase
{
    use RefreshDatabase;

    public function testGetTodosSuccess()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Gunakan token dalam header Authorization
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'is_completed',
                    ]
                ]
            ]);
    }

    public function testGetTodosWithoutToken()
    {
        // Jalankan seeder
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Kirim request tanpa token
        $response = $this->getJson('/api/todos');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function testGetTodosWithInvalidToken()
    {
        // Jalankan seeder
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Kirim request dengan token yang tidak valid
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->getJson('/api/todos');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
