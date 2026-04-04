<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure roles and permissions are seeded
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    // Create test users and assign roles
    $this->superUser = Usuario::factory()->create([
        'email' => 'superuser@test.com',
        'es_administrador' => true,
    ]);
    $this->superUser->assignRole('superuser');

    $this->adminUser = Usuario::factory()->create([
        'email' => 'director@test.com',
        'es_administrador' => true,
    ]);
    $this->adminUser->assignRole('director');

    $this->regularUser = Usuario::factory()->create(['password' => Hash::make('Sgei!2026_Test')]);
});

// --- Profile Controller Tests (Autogestión) ---

test('authenticated user can access their own profile', function () {
    $this->actingAs($this->regularUser, 'sanctum');
    $response = $this->getJson('/api/v1/auth/me');
    $response->assertOk()
             ->assertJson(['user' => ['id' => $this->regularUser->id]]);
});

test('unauthenticated user cannot access profile routes', function () {
    $response = $this->getJson('/api/v1/auth/me');
    $response->assertStatus(401); // Unauthorized
});

// --- UsuarioController (Admin CRUD) Tests ---

test('super user can manage users', function () {
    $this->actingAs($this->superUser, 'sanctum');
    $response = $this->getJson('/api/v1/admin/usuarios');
    $response->assertOk(); // Should be able to list users
});

test('admin user can manage users', function () {
    $this->actingAs($this->adminUser, 'sanctum');
    $response = $this->getJson('/api/v1/admin/usuarios');
    $response->assertOk(); // Should be able to list users
});

test('regular user cannot manage users', function () {
    $this->actingAs($this->regularUser, 'sanctum');
    $response = $this->getJson('/api/v1/admin/usuarios');
    $response->assertStatus(403); // Forbidden
});

test('admin user can create user', function () {
    $this->actingAs($this->adminUser, 'sanctum');
    $userData = [
        'nombre' => 'Creator',
        'documento_tipo_id' => 1,
        'documento_numero' => '10101010',
        'email' => 'creator@example.com',
        'password' => 'Sgei!2026_Test',
    ];
    $response = $this->postJson('/api/v1/admin/usuarios', $userData);
    $response->assertStatus(201);
});

test('admin user can update user', function () {
    $userToUpdate = Usuario::factory()->create();
    $this->actingAs($this->adminUser, 'sanctum');
    $updatedData = [
        'nombre' => 'Updated Admin',
        'documento_tipo_id' => 1,
        'documento_numero' => '20202020',
        'email' => 'updated.admin@example.com',
    ];
    $response = $this->putJson('/api/v1/admin/usuarios/' . $userToUpdate->id, $updatedData);
    $response->assertOk();
});

test('admin user can delete user', function () {
    $userToDelete = Usuario::factory()->create();
    $this->actingAs($this->adminUser, 'sanctum');
    $response = $this->deleteJson('/api/v1/admin/usuarios/' . $userToDelete->id);
    $response->assertOk();
    $this->assertSoftDeleted('usuarios', ['id' => $userToDelete->id]);
});
