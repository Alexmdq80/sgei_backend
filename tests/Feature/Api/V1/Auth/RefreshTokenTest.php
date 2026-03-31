<?php

use App\Models\Usuario;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
});

test('user receives a refresh token on login', function () {
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
});

test('user can refresh access token with a valid refresh token', function () {
    $user = Usuario::factory()->create(['email_verified_at' => now()]);
    $oldToken = 'valid-refresh-token';
    
    $refreshToken = RefreshToken::create([
        'usuario_id' => $user->id,
        'token' => $oldToken,
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->postJson('/api/v1/auth/refresh', [
        'refresh_token' => $oldToken,
    ]);

    $response->assertOk()
             ->assertJsonStructure(['token', 'refresh_token']);
    
    $newToken = $response->json('refresh_token');
    $this->assertNotEquals($oldToken, $newToken);
    
    // Old token should be deleted (soft deleted)
    $this->assertSoftDeleted('refresh_tokens', ['token' => $oldToken]);
    
    // New token should exist
    $this->assertDatabaseHas('refresh_tokens', [
        'usuario_id' => $user->id,
        'token' => $newToken,
    ]);
});

test('user can logout and revoke refresh token', function () {
    $user = Usuario::factory()->create(['email_verified_at' => now()]);
    $token = $user->createToken('auth-token')->plainTextToken;
    
    $refreshToken = RefreshToken::create([
        'usuario_id' => $user->id,
        'token' => 'to-be-revoked',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/auth/logout', [
            'refresh_token' => 'to-be-revoked',
        ]);

    $response->assertOk();
    
    // Refresh token should be deleted
    $this->assertSoftDeleted('refresh_tokens', ['token' => 'to-be-revoked']);
    
    // Sanctum token should also be deleted
    $this->assertCount(0, $user->tokens);
});

test('refresh fails with invalid or expired token', function () {
    $response = $this->postJson('/api/v1/auth/refresh', [
        'refresh_token' => 'invalid-token',
    ]);

    $response->assertStatus(401);
});
