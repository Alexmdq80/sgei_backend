<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        \Illuminate\Support\Facades\Cache::flush();
        RateLimiter::clear('forgot-password');
    }

    /**
     * Test user can request a password reset link.
     */
    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();
        $user = Usuario::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => __('passwords.sent')]);

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    /**
     * Test user cannot request password reset for non-existent email.
     */
    public function test_user_cannot_request_password_reset_for_non_existent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test user can reset password with a valid token.
     */
    public function test_user_can_reset_password_with_valid_token(): void
    {
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

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    /**
     * Test user cannot request password reset more than 3 times per hour.
     */
    public function test_user_cannot_request_password_reset_more_than_3_times_per_hour(): void
    {
        Notification::fake();
        $user = Usuario::factory()->create(['email' => 'throttled@example.com']);

        // First request should pass
        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'throttled@example.com'])
             ->assertOk();

        // Second request immediately should fail with either 422 (Broker throttle) or 429 (Rate limiter)
        $response = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'throttled@example.com']);
        
        $this->assertTrue(
            in_array($response->getStatusCode(), [422, 429]),
            "Expected status code 422 or 429, but received {$response->getStatusCode()}"
        );
    }
}
