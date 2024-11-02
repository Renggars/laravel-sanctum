<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Todolist;
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

    public function testGetTodoByIdSuccess()
    {
        // Jalankan seeder untuk Todolist dan User
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Request GET untuk mendapatkan todo berdasarkan ID
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos/' . $todo->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'description' => $todo->description,
                    'is_completed' => $todo->is_completed,
                ]
            ]);
    }

    public function testGetTodoByIdNotFound()
    {
        // Jalankan seeder untuk Todolist dan User
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Request GET untuk ID yang tidak ada
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/todos/999'); // Asumsikan ID 999 tidak ada di database

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Todolist not found',
            ]);
    }

    public function testGetTodoByIdWithoutToken()
    {
        // Jalankan seeder untuk Todolist
        $this->seed(TodolistSeeder::class);

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Request GET untuk mendapatkan todo tanpa token
        $response = $this->getJson('/api/todos/' . $todo->id);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function testGetTodoByIdWithInvalidToken()
    {
        // Jalankan seeder untuk Todolist
        $this->seed(TodolistSeeder::class);

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Request GET untuk mendapatkan todo dengan token yang tidak valid
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->getJson('/api/todos/' . $todo->id);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
