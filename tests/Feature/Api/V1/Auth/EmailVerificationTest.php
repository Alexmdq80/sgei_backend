<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Usuario;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    }

    public function testUserIsCreatedWithVerificationTokenAndNotificationIsSent(): void
    {
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

        $response = $this->postJson('/api/v1/usuarios', $userData);

        $response->assertStatus(201);
        
        $user = Usuario::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->verification_token);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo(
            $user,
            VerifyEmailNotification::class
        );
    }

    public function testUserCanVerifyEmailWithValidToken(): void
    {
        $user = Usuario::factory()->unverified()->create([
            'verification_token' => 'valid-token'
        ]);

        $response = $this->getJson("/api/v1/auth/verify?token=valid-token&email={$user->email}");

        $response->assertOk()
                 ->assertJson(['message' => 'Correo electrónico verificado con éxito.']);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->verification_token);
    }

    public function testUserCannotVerifyEmailWithInvalidToken(): void
    {
        $user = Usuario::factory()->unverified()->create([
            'verification_token' => 'valid-token'
        ]);

        $response = $this->getJson("/api/v1/auth/verify?token=invalid-token&email={$user->email}");

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Token de verificación inválido o expirado.']);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function testUnverifiedUserCannotLogin(): void
    {
        $password = 'Password123!';
        $user = Usuario::factory()->unverified()->create([
            'password' => bcrypt($password)
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Debes verificar tu correo electrónico antes de iniciar sesión.',
                     'code' => 401
                 ]);
    }

    public function testUserCanResendVerificationEmail(): void
    {
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
    }
}
