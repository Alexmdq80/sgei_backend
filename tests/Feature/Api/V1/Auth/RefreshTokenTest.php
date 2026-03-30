<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Usuario;
use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    }

    /**
     * Test user receives a refresh token on login.
     */
    public function test_user_receives_refresh_token_on_login(): void
    {
        $password = 'Sgei!2026_Test';
        $user = Usuario::factory()->create([
            'email' => 'login@test.com',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@test.com',
            'password' => $password,
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'refresh_token', 'user']);
        
        $this->assertDatabaseHas('refresh_tokens', [
            'usuario_id' => $user->id,
            'token' => $response->json('refresh_token'),
        ]);
    }

    /**
     * Test user can refresh access token with a valid refresh token.
     */
    public function test_user_can_refresh_access_token(): void
    {
        $user = Usuario::factory()->create(['email_verified_at' => now()]);
        $refreshToken = RefreshToken::create([
            'usuario_id' => $user->id,
            'token' => 'valid-refresh-token',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => 'valid-refresh-token',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'refresh_token']);
        
        $this->assertNotEquals($user->tokens()->first()->token, $response->json('token'));
    }

    /**
     * Test refresh fails with invalid or expired token.
     */
    public function test_refresh_fails_with_invalid_token(): void
    {
        $response = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => 'invalid-token',
        ]);

        $response->assertStatus(401);
    }
}
