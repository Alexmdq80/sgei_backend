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
    EscuelaUsuario::factory()->count(5)->create(['verified_at' => now(), 'role_id' => $roleId]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/admin/escuela-usuarios');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('el administrador superuser no puede actualizar el rol institucional de un vínculo escuela-usuario directamente', function () {
    $roleDirector = \Spatie\Permission\Models\Role::where('name', 'director')->first()->id;
    $roleSecretario = \Spatie\Permission\Models\Role::where('name', 'secretario')->first()->id;
    
    $user = Usuario::factory()->create();
    $link = EscuelaUsuario::factory()->create([
        'usuario_id' => $user->id,
        'role_id' => $roleDirector,
        'verified_at' => now()
    ]);

    $this->actingAs($this->admin, 'sanctum')
         ->putJson("/api/v1/admin/escuela-usuarios/{$link->id}", [
              'role_id' => $roleSecretario
         ])
         ->assertStatus(403)
         ->assertJsonPath('error', 'Acceso Denegado: Como Superusuario, no puedes asignar roles institucionales directamente. Esta acción está reservada para el Jefe Distrital o el Equipo de Conducción.');
});
