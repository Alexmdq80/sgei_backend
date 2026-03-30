<?php

namespace Tests\Feature\Api\V1;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;

class EmailChangeLimitTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
        $this->user = Usuario::factory()->create([
            'email' => 'original@example.com',
            'email_correction_attempts' => 0,
        ]);
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Test user can change email up to 3 times.
     */
    public function test_user_can_change_email_up_to_limit(): void
    {
        Notification::fake();

        for ($i = 1; $i <= 3; $i++) {
            $newEmail = "change{$i}@example.com";
            $response = $this->putJson('/api/v1/auth/profile', [
                'nombre' => 'User',
                'email' => $newEmail,
            ]);

            $response->assertOk();
            $this->user->refresh();
            $this->assertEquals($newEmail, $this->user->email);
            $this->assertEquals($i, $this->user->email_correction_attempts);
            $this->assertNull($this->user->email_verified_at);
            
            Notification::assertSentTo($this->user, VerifyEmailNotification::class);
        }

        // 4th change should fail
        $response = $this->putJson('/api/v1/auth/profile', [
            'nombre' => 'User',
            'email' => 'too-many@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
                 
        $this->user->refresh();
        $this->assertEquals('change3@example.com', $this->user->email);
        $this->assertEquals(3, $this->user->email_correction_attempts);
    }

    /**
     * Test an administrator can bypass the email change limit.
     */
    public function test_administrator_can_bypass_email_change_limit(): void
    {
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $admin = Usuario::factory()->create(['es_administrador' => true]);
        $admin->assignRole('superuser');
        
        $targetUser = Usuario::factory()->create([
            'email' => 'limit@example.com',
            'email_correction_attempts' => 3, // Already at limit
        ]);

        $this->actingAs($admin, 'sanctum');

        $newEmail = 'admin-correction@example.com';
        $response = $this->putJson("/api/v1/usuarios/{$targetUser->id}", [
            'nombre' => 'Corrected User',
            'email' => $newEmail,
        ]);

        $response->assertOk();
        $targetUser->refresh();
        $this->assertEquals($newEmail, $targetUser->email);
        $this->assertEquals(4, $targetUser->email_correction_attempts);
    }

    /**
     * Test changing only name does not increment email correction attempts.
     */
    public function test_changing_only_name_does_not_increment_attempts(): void
    {
        $response = $this->putJson('/api/v1/auth/profile', [
            'nombre' => 'New Name',
            'email' => $this->user->email, // Same email
        ]);

        $response->assertOk();
        $this->user->refresh();
        $this->assertEquals('New Name', $this->user->nombre);
        $this->assertEquals(0, $this->user->email_correction_attempts);
    }
}
