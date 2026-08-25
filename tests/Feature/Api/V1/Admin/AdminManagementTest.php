<?php

use App\Models\Usuario;
use App\Models\EscuelaUsuario;
use Illuminate\Support\Facades\Artisan;

// Seeder previo para los roles y permisos necesarios
beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    Artisan::call('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    
    // Limpiar caché de permisos de Spatie para el proceso actual
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    // Administrador con rol superuser y permiso específico
    $this->admin = Usuario::factory()->create();
    $this->admin->assignRole('superuser');
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

test('la creación directa de usuarios en /api/v1/admin/usuarios no está disponible (405)', function () {
    $userData = [
        'nombre' => 'Admin User Created',
        'email' => 'admin_created@sgei.local',
        'password' => 'Sgei!2026_Admin',
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678'
    ];

    $this->actingAs($this->admin, 'sanctum')
         ->postJson('/api/v1/admin/usuarios', $userData)
         ->assertStatus(405);
});

test('el administrador puede ver el detalle de un usuario específico', function () {
    $user = Usuario::factory()->create(['nombre' => 'User Specific Detail']);

    $this->actingAs($this->admin, 'sanctum')
         ->getJson("/api/v1/admin/usuarios/{$user->id}")
         ->assertStatus(200)
         ->assertJsonPath('data.nombre', 'User Specific Detail');
});

// =========================================================================
// GESTIÓN DE VÍNCULOS (ESCUELA_USUARIO)
// =========================================================================

test('el administrador puede listar todos los vínculos institucionales', function () {
    $roleId = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    \App\Models\EscuelaPersona::factory()->count(5)->create(['verified_at' => now(), 'role_id' => $roleId]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/escuela-personas');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('el administrador superuser no puede actualizar el rol institucional de un vínculo escuela-usuario directamente', function () {
    $roleDirector = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    $roleSecretario = \Spatie\Permission\Models\Role::where('name', 'secretario')->first()->id;
    
    $persona = \App\Models\Persona::factory()->create();
    $link = \App\Models\EscuelaPersona::factory()->create([
        'persona_id' => $persona->id,
        'role_id' => $roleDirector,
        'verified_at' => now()
    ]);

    $this->actingAs($this->admin, 'sanctum')
         ->putJson("/api/v1/admin/escuela-personas/{$link->id}", [
              'role_id' => $roleSecretario
         ])
         ->assertStatus(403)
         ->assertJsonPath('error', 'Esta acción está reservada para el Superusuario.');
});

// =========================================================================
// PROTECCIÓN DE IDENTIDAD EN USUARIOS VINCULADOS
// =========================================================================

test('el cambio de DNI de un usuario vinculado a persona es bloqueado', function () {
    // 1. Setup usuario con persona vinculada
    $user = \App\Models\Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'email' => 'vinculado@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $persona = \App\Models\Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'usuario_id' => $user->id
    ]);
    \App\Models\Contacto::create(['persona_id' => $persona->id, 'email' => 'vinculado@example.com']);

    // 2. Intentar cambiar el DNI del usuario
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->putJson("/api/v1/admin/usuarios/{$user->id}", [
                        'nombre' => $user->nombre,
                        'documento_tipo_id' => 1,
                        'documento_numero' => '99999999', // Cambiado
                        'email' => 'vinculado@example.com'
                     ]);

    // 3. Verificar que se bloquea y el vínculo se mantiene
    $response->assertStatus(422);
    $this->assertNotNull($persona->fresh()->usuario_id, 'El vínculo debe mantenerse');
    $this->assertEquals('12345678', $user->fresh()->documento_numero, 'El DNI no debe haber cambiado');
});

test('el cambio de email de un usuario vinculado a persona es bloqueado', function () {
    // 1. Setup usuario con persona vinculada
    $user = \App\Models\Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '87654321',
        'email' => 'original@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $persona = \App\Models\Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '87654321',
        'usuario_id' => $user->id
    ]);
    \App\Models\Contacto::create(['persona_id' => $persona->id, 'email' => 'original@example.com']);

    // 2. Intentar cambiar el email del usuario
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->putJson("/api/v1/admin/usuarios/{$user->id}", [
                        'nombre' => $user->nombre,
                        'documento_tipo_id' => 1,
                        'documento_numero' => '87654321',
                        'email' => 'nuevo@example.com' // Cambiado
                     ]);

    // 3. Verificar que se bloquea y el vínculo se mantiene
    $response->assertStatus(422);
    $this->assertNotNull($persona->fresh()->usuario_id, 'El vínculo debe mantenerse');
    $this->assertEquals('original@example.com', $user->fresh()->email, 'El email no debe haber cambiado');
});

test('el cambio de nombre de un usuario vinculado a persona es permitido', function () {
    // 1. Setup usuario con persona vinculada
    $user = \App\Models\Usuario::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11223344',
        'email' => 'permitido@example.com',
        'email_verified_at' => now(),
        'estado' => 'activo'
    ]);

    $persona = \App\Models\Persona::factory()->create([
        'documento_tipo_id' => 1,
        'documento_numero' => '11223344',
        'usuario_id' => $user->id
    ]);
    \App\Models\Contacto::create(['persona_id' => $persona->id, 'email' => 'permitido@example.com']);

    // 2. Cambiar solo el nombre
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->putJson("/api/v1/admin/usuarios/{$user->id}", [
                        'nombre' => 'Nuevo Nombre',
                        'documento_tipo_id' => 1,
                        'documento_numero' => '11223344',
                        'email' => 'permitido@example.com'
                     ]);

    // 3. Verificar que se permite y el vínculo se mantiene
    $response->assertOk();
    $this->assertNotNull($persona->fresh()->usuario_id, 'El vínculo debe mantenerse');
    $this->assertEquals('Nuevo Nombre', $user->fresh()->nombre, 'El nombre debe haber cambiado');
});
