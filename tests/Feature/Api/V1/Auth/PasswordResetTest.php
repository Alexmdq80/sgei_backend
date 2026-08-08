<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    \Illuminate\Support\Facades\Cache::flush();
    RateLimiter::clear('forgot-password');
});

test('user can request a password reset link', function () {
    Notification::fake();
    $user = Usuario::factory()->create(['email' => 'user@example.com']);

    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'user@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => __('passwords.sent')]);

    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
});

test('user cannot request password reset for non-existent email', function () {
    $response = $this->postJson('/api/v1/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422);
});

test('user can reset password with a valid token', function () {
    $user = Usuario::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('Sgei!2026_Old'),
    ]);

    $token = Password::broker()->createToken($user);

    $newPassword = 'Sgei!2026_NewPass';
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'token' => $token,
        'email' => 'user@example.com',
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => __('passwords.reset')]);

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

test('user cannot request password reset more than 3 times per hour', function () {
    Notification::fake();
    $user = Usuario::factory()->create(['email' => 'throttled@example.com']);

    // First request should pass
    $this->postJson('/api/v1/auth/forgot-password', ['email' => 'throttled@example.com'])
        ->assertOk();

    // Second request immediately should fail with either 422 (Broker throttle) or 429 (Rate limiter)
    $response = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'throttled@example.com']);

    expect(in_array($response->getStatusCode(), [422, 429]))->toBeTrue();
});

test('password reset auto-verifies email and updates estado for unverified user', function () {
    $user = Usuario::factory()->unverified()->create([
        'email' => 'unverified@example.com',
        'estado' => 'email_pendiente',
    ]);

    $token = Password::broker()->createToken($user);

    $newPassword = 'Sgei!2026_NewPass';
    $response = $this->postJson('/api/v1/auth/reset-password', [
        'token' => $token,
        'email' => 'unverified@example.com',
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => __('passwords.reset')]);

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull()
        ->and($user->verification_token)->toBeNull()
        ->and($user->estado)->toBe('email_verificado');
});

