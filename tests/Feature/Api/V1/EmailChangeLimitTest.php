<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->user = Usuario::factory()->create([
        'email' => 'original@example.com',
        'email_correction_attempts' => 0,
    ]);
    $this->actingAs($this->user, 'sanctum');
});

test('user can change email up to limit', function () {
    Notification::fake();

    for ($i = 1; $i <= 3; $i++) {
        $newEmail = "change{$i}@example.com";
        $response = $this->putJson('/api/v1/auth/profile', [
            'nombre' => 'User',
            'email' => $newEmail,
        ]);

        $response->assertOk();
        $this->user->refresh();
        expect($this->user->email)->toBe($newEmail);
        expect($this->user->email_correction_attempts)->toBe($i);
        expect($this->user->email_verified_at)->toBeNull();
        
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
    expect($this->user->email)->toBe('change3@example.com');
    expect($this->user->email_correction_attempts)->toBe(3);
});

test('administrator can bypass email change limit', function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $admin = Usuario::factory()->create(['es_administrador' => true]);
    $admin->assignRole('superuser');
    
    $targetUser = Usuario::factory()->create([
        'email' => 'limit@example.com',
        'email_correction_attempts' => 3, // Already at limit
    ]);

    $this->actingAs($admin, 'sanctum');

    $newEmail = 'admin-correction@example.com';
    $response = $this->putJson("/api/v1/admin/usuarios/{$targetUser->id}", [
        'nombre' => 'Corrected User',
        'email' => $newEmail,
    ]);

    $response->assertOk();
    $targetUser->refresh();
    expect($targetUser->email)->toBe($newEmail);
    expect($targetUser->email_correction_attempts)->toBe(4);
});

test('changing only name does not increment attempts', function () {
    $response = $this->putJson('/api/v1/auth/profile', [
        'nombre' => 'New Name',
        'email' => $this->user->email, // Same email
    ]);

    $response->assertOk();
    $this->user->refresh();
    expect($this->user->nombre)->toBe('New Name');
    expect($this->user->email_correction_attempts)->toBe(0);
});
