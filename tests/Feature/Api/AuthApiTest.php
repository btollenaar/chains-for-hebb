<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token(): void
    {
        $customer = Customer::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $customer->email,
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    public function test_invalid_credentials_rejected(): void
    {
        $customer = Customer::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $customer->email,
            'password' => 'wrong-password',
            'device_name' => 'test',
        ]);

        $response->assertUnprocessable();
    }

    public function test_login_requires_device_name(): void
    {
        $customer = Customer::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['device_name']);
    }

    public function test_login_requires_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/auth/user');

        $response->assertOk()
            ->assertJsonPath('id', $customer->id)
            ->assertJsonPath('name', $customer->name)
            ->assertJsonPath('email', $customer->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $customer = Customer::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // Login to get a token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => $customer->email,
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $token = $loginResponse->json('token');

        // Logout using the token
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        // Verify token was deleted from the database
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/v1/auth/user');
        $response->assertUnauthorized();
    }

    public function test_nonexistent_email_rejected(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertUnprocessable();
    }
}
