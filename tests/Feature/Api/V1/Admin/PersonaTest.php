<?php

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\AccountInvitationNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Contacto;
use App\Models\Provincia;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    
    // Usuario admin con rol superuser
    $this->admin = Usuario::factory()->create(['es_administrador' => true]);
    $this->admin->assignRole('superuser');
    
    // Usuario sin permisos
    $this->usuarioSinPermisos = Usuario::factory()->create();
});

//_Test 1 - No autenticado no puede listar (GET /personas)__

test('no autenticado no puede listar personas', function () {
    $response = $this->getJson('/api/v1/admin/personas');
    $response->assertStatus(401);
});

//__Test 2 - No autenticado no puede crear (POST /personas)__

test('no autenticado no puede crear una persona', function () {
    $response = $this->postJson('/api/v1/admin/personas', []);
    $response->assertStatus(401);
});

//__Test 3 - No autenticado no puede ver detalle (GET /personas/{id})__

test('no autenticado no puede ver detalle de persona', function () {
    $response = $this->getJson('/api/v1/admin/personas/1');
    $response->assertStatus(401);
});

//__Test 4 - No autenticado no puede eliminar (DELETE /personas/{id})__

test('no autenticado no puede eliminar persona', function () {
    $response = $this->deleteJson('/api/v1/admin/personas/1');
    $response->assertStatus(401);
});

//__Test 5 - Usuario sin permisos no puede listar__

test('usuario autenticado sin permisos no puede listar personas', function () {
    $response = $this->actingAs($this->usuarioSinPermisos, 'sanctum')
                     ->getJson('/api/v1/admin/personas');
    $response->assertStatus(403);
});

//__Test 6 - Usuario sin permisos no puede crear__

test('usuario autenticado sin permisos no puede crear una persona', function () {
    $response = $this->actingAs($this->usuarioSinPermisos, 'sanctum')
                     ->postJson('/api/v1/admin/personas', [
                         'apellido' => 'Perez',
                         'nombre' => 'Juan',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '12345678',
                     ]);
    $response->assertStatus(403);
});

 // =========================================================================
// GRUPO B: TESTS DEL INDEX (LISTADO)
// =========================================================================

// Test 7 - Admin puede listar personas paginadas

test('admin puede listar personas paginadas', function () {
    Persona::factory()->count(3)->create();

    $response = $this->actingAs($this->admin, 'sanctum')
                    ->getJson('/api/v1/admin/personas');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next']
            ]);
});

// Test 8 - Admin puede buscar por nombre

test('admin puede buscar personas por nombre', function () {
    Persona::factory()->create(['nombre' => 'JUAN', 'apellido' => 'PEREZ']);
    Persona::factory()->create(['nombre' => 'PEDRO', 'apellido' => 'GOMEZ']);

    $response = $this->actingAs($this->admin, 'sanctum')
                    ->getJson('/api/v1/admin/personas?search=JUAN');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nombre'))->toBe('JUAN');
});

// ### Test 9 - Admin puede buscar por apellido

test('admin puede buscar personas por apellido', function () {
    Persona::factory()->create(['apellido' => 'PEREZ', 'nombre' => 'JUAN']);
    Persona::factory()->create(['apellido' => 'GOMEZ', 'nombre' => 'PEDRO']);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/personas?search=PEREZ');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
});

### Test 10 - Admin puede buscar por documento

test('admin puede buscar personas por documento', function () {
    Persona::factory()->create(['documento_numero' => '12345678']);
    Persona::factory()->create(['documento_numero' => '87654321']);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/personas?search=12345678');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
});

//### Test 11 - Búsqueda sin resultados devuelve lista vacía

test('busqueda sin resultados devuelve lista vacia', function () {
    Persona::factory()->create(['nombre' => 'JUAN']);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/personas?search=XXXXX');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(0);
});

// =========================================================================
// GRUPO C: TESTS DEL CRUD (SHOW, STORE, UPDATE, DESTROY)
// =========================================================================

test('admin puede ver detalle de una persona', function () {
    $persona = Persona::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson("/api/v1/admin/personas/{$persona->id}");

    $response->assertStatus(200)
             ->assertJsonStructure(['data' => ['id', 'nombre', 'apellido', 'documento_numero']]);
});

// ### Test 13 - Devuelve 404 si la persona no existe

test('devuelve 404 al ver detalle de persona inexistente', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->getJson('/api/v1/admin/personas/99999');

    $response->assertStatus(404);
});

// ### Test 14 - Admin puede crear una persona (store)

test('admin puede crear una persona con datos válidos', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson('/api/v1/admin/personas', [
                         'apellido' => 'PEREZ',
                         'nombre' => 'JUAN',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '12345678',
                     ]);

    $response->assertStatus(201)
             ->assertJsonPath('message', 'Persona registrada con éxito en el padrón.')
             ->assertJsonStructure(['data' => ['id', 'nombre', 'apellido']]);
});

// Nota: El Request del controlador convierte nombre y apellido a mayúsculas automáticamente, por eso van en mayúscula.

// ### Test 15 - Crear persona con email crea contacto

test('admin puede crear persona con email y se crea contacto', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson('/api/v1/admin/personas', [
                         'apellido' => 'GOMEZ',
                         'nombre' => 'PEDRO',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '87654321',
                         'email' => 'pedro@example.com',
                     ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('contactos', ['email' => 'pedro@example.com']);
});


// ### Test 16 - Admin puede actualizar una persona (update)

test('admin puede actualizar datos básicos de una persona', function () {
    $persona = Persona::factory()->create(['nombre' => 'JUAN']);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->putJson("/api/v1/admin/personas/{$persona->id}", [
                         'apellido' => $persona->apellido,
                         'nombre' => 'CARLOS',
                         'documento_tipo_id' => $persona->documento_tipo_id,
                         'documento_numero' => $persona->getRawOriginal('documento_numero'),
                     ]);

    $response->assertStatus(200)
             ->assertJsonPath('message', 'Registro de persona actualizado con éxito.');
    
    expect($persona->fresh()->nombre)->toBe('CARLOS');
});

// ### Test 17 - Admin puede eliminar (soft delete) una persona

test('admin puede eliminar una persona', function () {
    $persona = Persona::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->deleteJson("/api/v1/admin/personas/{$persona->id}");

    $response->assertStatus(200)
             ->assertJsonPath('message', 'Registro de persona eliminado con éxito.');
    
    $this->assertSoftDeleted($persona);
});

// ### Test 18 - Validación: documento duplicado al crear

test('no puede crear persona con documento duplicado', function () {
    Persona::factory()->create(['documento_numero' => '12345678']);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson('/api/v1/admin/personas', [
                         'apellido' => 'PEREZ',
                         'nombre' => 'JUAN',
                         'documento_tipo_id' => 1,
                         'documento_numero' => '12345678',
                     ]);

    $response->assertStatus(422);
});

// =========================================================================
// GRUPO D: RESEND ACTIVATION Y ROLES
// =========================================================================

test('superuser puede reenviar activacion', function () {
    $persona = Persona::factory()->create();
    $user = Usuario::factory()->unverified()->create();
    $persona->update(['usuario_id' => $user->id]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/resend-activation");

    $response->assertStatus(200);
});

// Importante: El usuario debe estar sin verificar para que el reenvío sea válido.

// ### Test 20 - Persona sin usuario devuelve error

test('resend activacion falla si persona no tiene usuario vinculado', function () {
    $persona = Persona::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/resend-activation");

    $response->assertStatus(422);
});

// ### Test 21 - Solo superuser/jefes pueden reenviar activación

test('usuario sin permisos no puede reenviar activacion', function () {
    $persona = Persona::factory()->create();
    $user = Usuario::factory()->unverified()->create();
    $persona->update(['usuario_id' => $user->id]);

    $response = $this->actingAs($this->usuarioSinPermisos, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/resend-activation");

    $response->assertStatus(403);
});

// ### Test 22 - Superuser puede asignar Jefe Provincial

test('superuser puede asignar jefe provincial a una persona', function () {

    Notification::fake();
    
    $persona = Persona::factory()->create();
    $persona->contacto()->create(['email' => 'test@example.com']);

    // Crear provincia
    $provincia = Provincia::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/jefe-provincial", [
                         'provincia_id' => $provincia->id
                     ]);

    $response->assertStatus(200);

    // Opcional: verificar que se envió la notificación
    $user = $persona->fresh()->usuario;
    Notification::assertSentTo($user, AccountInvitationNotification::class);
});


// Nota: Para este test necesitás que exista la factory de Provincia. Si no existe, primero creala como `\App\Models\Provincia::factory()`. Verificá en la terminal si funciona.

//### Test 23 - Usuario sin permisos no puede asignar Jefe Provincial

test('usuario sin permisos no puede asignar jefe provincial', function () {


    $persona = Persona::factory()->create();

    $response = $this->actingAs($this->usuarioSinPermisos, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/jefe-provincial", [
                         'provincia_id' => 1
                     ]);

    $response->assertStatus(403);
});

// ### Test 24 - Superuser puede asignar Supervisor Curricular

test('superuser puede asignar supervisor curricular', function () {
    $persona = Persona::factory()->create();

    // Crear contacto con email (requerido por ensureUserExists)

    Contacto::create([
        'persona_id' => $persona->id,
        'email' => 'supervisor@example.com'
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->postJson("/api/v1/admin/personas/{$persona->id}/supervisor");

    $response->assertStatus(200);
});

// ### Test 25 - Superuser puede remover un rol

test('superuser puede remover un rol de una persona', function () {
    $persona = Persona::factory()->create();
    $user = Usuario::factory()->create();
    $persona->update(['usuario_id' => $user->id]);
    $user->assignRole('jefe_provincial');

    $response = $this->actingAs($this->admin, 'sanctum')
                     ->deleteJson("/api/v1/admin/personas/{$persona->id}/roles/jefe_provincial");

    $response->assertStatus(200);
});



