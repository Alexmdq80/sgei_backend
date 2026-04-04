<?php

use App\Models\Usuario;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
});

test('user is created with verification token and notification is sent', function () {
    Notification::fake();

    $userData = [
        'nombre' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
    ];

    // We need to be an admin to create a user
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');
    $this->actingAs($admin, 'sanctum');

    $response = $this->postJson('/api/v1/admin/usuarios', $userData);

    $response->assertStatus(201);
    
    $user = Usuario::where('email', 'test@example.com')->first();
    expect($user->verification_token)->not->toBeNull();
    expect($user->email_verified_at)->toBeNull();

    Notification::assertSentTo(
        $user,
        VerifyEmailNotification::class
    );
});

test('user can verify email with valid token', function () {
    $user = Usuario::factory()->unverified()->create([
        'verification_token' => 'valid-token',
        'verification_token_created_at' => now(),
    ]);

    $response = $this->getJson("/api/v1/auth/verify?token=valid-token&email={$user->email}");

    $response->assertOk()
             ->assertJson(['message' => 'Correo electrónico verificado con éxito.']);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    expect($user->fresh()->verification_token)->toBeNull();
});

test('user cannot verify email with invalid token', function () {
    $user = Usuario::factory()->unverified()->create([
        'verification_token' => 'valid-token',
        'verification_token_created_at' => now(),
    ]);

    $response = $this->getJson("/api/v1/auth/verify?token=invalid-token&email={$user->email}");

    $response->assertStatus(400)
             ->assertJson(['message' => 'Token de verificación inválido.']);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('user cannot verify email with expired token', function () {
    $user = Usuario::factory()->unverified()->create([
        'verification_token' => 'expired-token',
        'verification_token_created_at' => now()->subHours(25), // Expired
    ]);

    $response = $this->getJson("/api/v1/auth/verify?token=expired-token&email={$user->email}");

    $response->assertStatus(400)
             ->assertJson(['message' => 'El enlace de verificación ha expirado. Por favor, solicita uno nuevo.']);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('user cannot resend verification email more than 3 times per hour', function () {
    \Illuminate\Support\Facades\RateLimiter::clear('resend-verification');
    Notification::fake();

    $user = Usuario::factory()->unverified()->create();
    $this->actingAs($user, 'sanctum');

    // First 3 pass
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/api/v1/auth/verify/resend')->assertOk();
    }

    // 4th fails
    $response = $this->postJson('/api/v1/auth/verify/resend');
    $response->assertStatus(429);
});

test('unverified user can login', function () {
    $password = 'Password123!';
    $user = Usuario::factory()->unverified()->create([
        'password' => bcrypt($password)
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => $password
    ]);

    $response->assertOk()
             ->assertJsonStructure([
                 'user' => ['id', 'email', 'email_verified_at']
             ])
             ->assertJsonPath('user.email_verified_at', null);
});

test('user can resend verification email', function () {
    Notification::fake();

    $user = Usuario::factory()->unverified()->create();
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/v1/auth/verify/resend');

    $response->assertOk()
             ->assertJson(['message' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.']);

    Notification::assertSentTo(
        $user,
        VerifyEmailNotification::class
    );
});
