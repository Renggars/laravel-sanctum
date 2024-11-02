<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
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


    public function testRegisterWithMissingName()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['name']);
    }

    public function testRegisterWithMissingEmail()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);
    }

    public function testRegisterWithInvalidEmail()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);
    }

    public function testRegisterWithDuplicateEmail()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);
    }

    public function testRegisterWithMissingPassword()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['password']);
    }

    public function testRegisterWithPasswordLessThan8Characters()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['password']);
    }

    public function testRegisterWithPasswordMoreThan100Characters()
    {
        $longPassword = str_repeat('a', 101);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $longPassword,
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['password']);
    }
}
