<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testLogoutSuccess()
    {
        $user = User::factory()->create();

        // Mendapatkan token autentikasi untuk user yang sudah dibuat
        $token = $user->createToken('TestToken')->plainTextToken;

        // Mengirimkan permintaan logout dengan header Authorization
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        // Memastikan status respons dan pesan yang diharapkan
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);

        // Memastikan token akses dihapus
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'token' => hash('sha256', explode('|', $token)[1]),
        ]);
    }

    public function testLogoutWithoutToken()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testLogoutWithInvalidToken()
    {
        $user = User::factory()->create();
        $invalidToken = 'Bearer InvalidToken';

        $response = $this->withHeader('Authorization', $invalidToken)
            ->postJson('/api/logout');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
