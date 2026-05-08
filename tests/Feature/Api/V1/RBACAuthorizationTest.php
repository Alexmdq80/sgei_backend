<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure roles and permissions are seeded
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    
    // Limpiar caché de permisos
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    // Create a default DocumentoTipo for the tests
    $this->docTipo = \App\Models\DocumentoTipo::factory()->create(['nombre' => 'DNI']);

    // Create test users and assign roles
    $this->superUser = Usuario::factory()->create([
        'email' => 'superuser@test.com',
        'es_administrador' => true,
        'documento_tipo_id' => $this->docTipo->id,
    ]);
    $this->superUser->assignRole('superuser');

    $this->adminUser = Usuario::factory()->create([
        'email' => 'director@test.com',
        'es_administrador' => true,
        'documento_tipo_id' => $this->docTipo->id,
    ]);
    $this->adminUser->assignRole('director');

    $this->regularUser = Usuario::factory()->create([
        'password' => Hash::make('Sgei!2026_Test'),
        'documento_tipo_id' => $this->docTipo->id,
    ]);
});

// ... (rest of the tests)

test('superuser can create user', function () {
    $this->actingAs($this->superUser, 'sanctum');
    $userData = [
        'nombre' => 'Creator',
        'documento_tipo_id' => $this->docTipo->id,
        'documento_numero' => '10101010',
        'email' => 'creator@example.com',
        'password' => 'Sgei!2026_Test',
    ];
    $response = $this->postJson('/api/v1/admin/usuarios', $userData);
    $response->assertStatus(201);
});

test('superuser can update user', function () {
    $userToUpdate = Usuario::factory()->create(['documento_tipo_id' => $this->docTipo->id]);
    $this->actingAs($this->superUser, 'sanctum');
    $updatedData = [
        'nombre' => 'Updated Admin',
        'documento_tipo_id' => $this->docTipo->id,
        'documento_numero' => '20202020',
        'email' => 'updated.admin@example.com',
    ];
    $response = $this->putJson('/api/v1/admin/usuarios/' . $userToUpdate->id, $updatedData);
    $response->assertOk();
});

test('superuser can delete user', function () {
    $userToDelete = Usuario::factory()->create();
    $this->actingAs($this->superUser, 'sanctum');
    $response = $this->deleteJson('/api/v1/admin/usuarios/' . $userToDelete->id);
    $response->assertOk();
    $this->assertSoftDeleted('usuarios', ['id' => $userToDelete->id]);
});
