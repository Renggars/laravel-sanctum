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

    public function testCreateTodoSuccess()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', [
                'title' => 'New Todo',
                'description' => 'This is a new todo',
                'is_completed' => false,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Todo',
                    'description' => 'This is a new todo',
                    'is_completed' => false,
                ]
            ]);
    }


    public function testCreateTodoWithMissingTitle()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Data tanpa title
        $data = [
            'description' => 'This is a new todo',
            'is_completed' => false,
        ];

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field is required.']
                ]
            ]);
    }

    public function testCreateTodoWithShortTitle()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Data dengan title yang terlalu pendek
        $data = [
            'title' => 'ab', // Short title
            'description' => 'This is a new todo',
            'is_completed' => false,
        ];

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field must be at least 3 characters.']
                ]
            ]);
    }

    public function testCreateTodoWithLongTitle()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Data dengan title yang terlalu panjang
        $data = [
            'title' => str_repeat('a', 101), // Long title
            'description' => 'This is a new todo',
            'is_completed' => false,
        ];

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field must not be greater than 100 characters.']
                ]
            ]);
    }

    public function testCreateTodoWithMissingDescription()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Data tanpa description
        $data = [
            'title' => 'New Todo',
            'is_completed' => false,
        ];

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'description' => ['The description field is required.']
                ]
            ]);
    }

    public function testCreateTodoWithInvalidCompletionValue()
    {
        $this->seed(UserSeeder::class);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Data dengan nilai is_completed yang tidak valid
        $data = [
            'title' => 'New Todo',
            'description' => 'This is a new todo',
            'is_completed' => 'invalid_value', // Invalid value
        ];

        // Request POST untuk membuat todo baru
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/todos', $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'is_completed' => ['The is completed field must be true or false.']
                ]
            ]);
    }

    public function testUpdateTodoSuccess()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo yang ada
        $todo = Todolist::first();

        // Data untuk diupdate
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'is_completed' => true
        ];

        // Request PATCH untuk memperbarui todo
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/todos/' . $todo->id, $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                    'is_completed' => true,
                ]
            ]);
    }

    public function testUpdateTodoWithInvalidTitle()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Data dengan title yang terlalu pendek
        $data = [
            'title' => 'ab', // Too short
        ];

        // Request PATCH untuk memperbarui todo
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/todos/' . $todo->id, $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'title' => ['The title field must be at least 3 characters.']
                ]
            ]);
    }

    public function testUpdateTodoWithInvalidDescription()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Data dengan description yang terlalu pendek
        $data = [
            'description' => 'ab', // Too short
        ];

        // Request PATCH untuk memperbarui todo
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/todos/' . $todo->id, $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'description' => ['The description field must be at least 3 characters.']
                ]
            ]);
    }

    public function testUpdateTodoWithInvalidCompletionValue()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Data dengan is_completed yang tidak valid
        $data = [
            'is_completed' => 'not_boolean', // Invalid boolean value
        ];

        // Request PATCH untuk memperbarui todo
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/todos/' . $todo->id, $data);

        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'is_completed' => ['The is completed field must be true or false.']
                ]
            ]);
    }

    public function testDeleteTodoSuccess()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Ambil salah satu todo untuk dihapus
        $todo = Todolist::first();

        // Request DELETE untuk menghapus todo
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/todos/' . $todo->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todolist deleted successfully',
            ]);

        // Pastikan todo telah dihapus dari database
        $this->assertDatabaseMissing('todolists', [
            'id' => $todo->id,
        ]);
    }

    public function testDeleteTodoNotFound()
    {
        $this->seed([UserSeeder::class, TodolistSeeder::class]);

        // Ambil pengguna dan buat token otentikasi
        $user = User::where('email', 'tes@example.com')->first();
        $token = $user->createToken('TestToken')->plainTextToken;

        // ID Todo yang tidak ada di database
        $nonExistentId = 999;

        // Request DELETE untuk ID yang tidak ditemukan
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/todos/' . $nonExistentId);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Todolist not found',
            ]);
    }

    public function testDeleteTodoWithoutToken()
    {
        $this->seed(TodolistSeeder::class);

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Request DELETE tanpa token
        $response = $this->deleteJson('/api/todos/' . $todo->id);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function testDeleteTodoWithInvalidToken()
    {
        $this->seed(TodolistSeeder::class);

        // Ambil salah satu todo
        $todo = Todolist::first();

        // Request DELETE dengan token yang tidak valid
        $response = $this->withHeader('Authorization', 'Bearer invalid_token')
            ->deleteJson('/api/todos/' . $todo->id);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
