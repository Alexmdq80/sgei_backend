<?php

use App\Models\Usuario;
use App\Models\EscuelaUsuario;
use Illuminate\Support\Facades\Artisan;

// Seeder previo para los roles y permisos necesarios
beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    Artisan::call('db:seed', ['--class' => 'RolEscolarSeeder']);
    Artisan::call('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    
    // Administrador con permiso específico de sistema.usuarios
    $this->admin = Usuario::factory()->create();
    $this->admin->givePermissionTo('sistema.usuarios');

    // Usuario sin permisos administrativos
    $this->unauthorizedUser = Usuario::factory()->create();
});

// =========================================================================
// SEGURIDAD Y PERMISOS
// =========================================================================

test('usuarios no autorizados no pueden acceder a las rutas de administración', function () {
    // No autenticado
    $this->getJson('/api/v1/admin/usuarios')->assertStatus(401);

    // Autenticado pero sin el permiso 'sistema.usuarios'
    $this->actingAs($this->unauthorizedUser, 'sanctum')
         ->getJson('/api/v1/admin/usuarios')
         ->assertStatus(403);
});

// =========================================================================
// CRUD DE USUARIOS (ADMIN)
// =========================================================================

test('el administrador puede listar todos los usuarios del sistema', function () {
    Usuario::factory()->count(3)->create();

    $this->actingAs($this->admin, 'sanctum')
         ->getJson('/api/v1/admin/usuarios')
         ->assertStatus(200)
         ->assertJsonStructure(['data', 'meta', 'links']);
});

test('el administrador puede crear un nuevo usuario mediante la API', function () {
    $userData = [
        'nombre' => 'Admin User Created',
        'email' => 'admin_created@sgei.local',
        'password' => 'Sgei!2026_Admin',
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ];

    $this->actingAs($this->admin, 'sanctum')
         ->postJson('/api/v1/admin/usuarios', $userData)
         ->assertStatus(201)
         ->assertJsonPath('message', 'Usuario creado con éxito.');

    $this->assertDatabaseHas('usuarios', ['email' => 'admin_created@sgei.local']);
});

// =========================================================================
// GESTIÓN DE SOLICITUDES DE UNIÓN (ESCUELA_USUARIO)
// =========================================================================

test('el administrador puede listar solicitudes de unión pendientes', function () {
    $roleId = \App\Models\RolEscolar::where('nombre', 'Director')->first()->id;
    EscuelaUsuario::factory()->count(3)->create(['verified_at' => null, 'rol_escolar_id' => $roleId]);
    EscuelaUsuario::factory()->count(2)->create(['verified_at' => now(), 'rol_escolar_id' => $roleId]);

    $this->actingAs($this->admin, 'sanctum')
         ->getJson('/api/v1/admin/escuelas/pending')
         ->assertStatus(200)
         ->assertJsonCount(3, 'data');
});

test('el administrador puede aprobar una solicitud de unión escolar', function () {
    $roleId = \App\Models\RolEscolar::where('nombre', 'Director')->first()->id;
    $user = Usuario::factory()->create(['estado' => 'espera_aprobacion']);
    $request = EscuelaUsuario::factory()->create([
        'usuario_id' => $user->id,
        'rol_escolar_id' => $roleId,
        'verified_at' => null
    ]);

    $this->actingAs($this->admin, 'sanctum')
         ->postJson("/api/v1/admin/escuelas/requests/{$request->id}/approve")
         ->assertStatus(200)
         ->assertJsonPath('message', 'Solicitud aprobada con éxito.');

    expect(EscuelaUsuario::find($request->id)->verified_at)->not->toBeNull();
    expect($user->fresh()->estado)->toBe('activo');
});

test('el administrador puede rechazar una solicitud de unión escolar', function () {
    $roleId = \App\Models\RolEscolar::where('nombre', 'Director')->first()->id;
    $user = Usuario::factory()->create(['estado' => 'espera_aprobacion']);
    $request = EscuelaUsuario::factory()->create([
        'usuario_id' => $user->id,
        'rol_escolar_id' => $roleId,
        'verified_at' => null
    ]);

    $this->actingAs($this->admin, 'sanctum')
         ->postJson("/api/v1/admin/escuelas/requests/{$request->id}/reject", [
             'motivo' => 'Documentación incompleta o errónea'
         ])
         ->assertStatus(200)
         ->assertJsonPath('message', 'Solicitud rechazada y eliminada.');

    // Soft delete (si aplica) o borrado real
    $this->assertSoftDeleted('escuela_usuario', ['id' => $request->id]);
    
    expect($user->fresh()->estado)->toBe('email_verificado');
    expect($user->fresh()->motivo_rechazo)->toBe('Documentación incompleta o errónea');
});
