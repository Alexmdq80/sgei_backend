<?php

use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\EscuelaUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seeders necesarios
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    
    // Crear administrador con permisos
    $this->admin = Usuario::factory()->create();
    $this->admin->givePermissionTo('sistema.usuarios');

    // Crear usuario normal sin permisos
    $this->standardUser = Usuario::factory()->create();
});

test('unauthorized users cannot access pending requests', function () {
    // No autenticado
    $this->getJson('/api/v1/admin/escuelas/pending')
         ->assertStatus(401);

    // Autenticado sin permiso
    $this->actingAs($this->standardUser, 'sanctum')
         ->getJson('/api/v1/admin/escuelas/pending')
         ->assertStatus(403);
});

test('admin can list pending requests', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    EscuelaUsuario::factory()->count(3)->create(['verified_at' => null, 'role_id' => $roleId]);
    EscuelaUsuario::factory()->count(2)->create(['verified_at' => now(), 'role_id' => $roleId]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/escuelas/pending');

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
});

test('admin can approve a request', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    $user = Usuario::factory()->create(['estado' => 'espera_aprobacion']);
    $request = EscuelaUsuario::factory()->create([
        'usuario_id' => $user->id,
        'role_id' => $roleId,
        'verified_at' => null
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/escuelas/requests/{$request->id}/approve");

    $response->assertStatus(200)
             ->assertJsonPath('message', 'Solicitud aprobada con éxito.');

    $this->assertDatabaseHas('escuela_usuario', [
        'id' => $request->id,
        'verified_at' => now()->toDateTimeString()
    ]);

    $this->assertDatabaseHas('usuarios', [
        'id' => $user->id,
        'estado' => 'activo'
    ]);
});

test('admin can reject a request', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    $user = Usuario::factory()->create(['estado' => 'espera_aprobacion']);
    $request = EscuelaUsuario::factory()->create([
        'usuario_id' => $user->id,
        'role_id' => $roleId,
        'verified_at' => null
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/escuelas/requests/{$request->id}/reject", [
                         'motivo' => 'Documentación incompleta'
                     ]);

    $response->assertStatus(200)
             ->assertJsonPath('message', 'Solicitud rechazada y eliminada.');

    // Verificar que la solicitud se eliminó (SoftDelete)
    $this->assertSoftDeleted('escuela_usuario', ['id' => $request->id]);

    // Verificar que el usuario volvió al estado previo y tiene el motivo
    $this->assertDatabaseHas('usuarios', [
        'id' => $user->id,
        'estado' => 'email_verificado',
        'motivo_rechazo' => 'Documentación incompleta'
    ]);
});
