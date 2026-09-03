<?php

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    // Usuario admin con rol superuser
    $this->admin = Usuario::factory()->create(['es_administrador' => true]);
    $this->admin->assignRole('superuser');
});

// Rechazo por números
test('rechaza numeros en el nombre', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/personas', [
            'apellido' => 'PEREZ',
            'nombre' => 'JUAN2',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345670',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('nombre');
});

test('rechaza numeros en el apellido', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/personas', [
            'apellido' => 'PEREZ1',
            'nombre' => 'JUAN',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345671',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('apellido');
});

// Rechazo por caracteres especiales
test('rechaza caracteres especiales en nombre alternativo', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/personas', [
            'apellido' => 'PEREZ',
            'nombre' => 'JUAN',
            'nombre_alternativo' => 'JUAN@PEREZ',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345672',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('nombre_alternativo');
});

// Aceptación de letras con tilde, ñ, ü, apóstrofes y guiones
test('acepta letras acentuadas, ñ, ü, apostrofes y guiones', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/personas', [
            'apellido' => 'O\'BRIEN',
            'nombre' => 'CÉSAR ÑANDÚ',
            'nombre_alternativo' => 'MARÍA-JOSÉ',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345673',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'nombre', 'apellido']]);
});

// Normalización a mayúsculas con acentos (nombre_alternativo)
test('normaliza nombre alternativo a mayusculas', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/personas', [
            'apellido' => 'PEREZ',
            'nombre' => 'JUAN',
            'nombre_alternativo' => 'maría sol ñandú',
            'documento_tipo_id' => 1,
            'documento_numero' => '12345674',
        ]);

    $response->assertStatus(201);

    $persona = Persona::where('documento_numero', '12345674')->firstOrFail();
    expect($persona->nombre_alternativo)->toBe('MARÍA SOL ÑANDÚ');
});

// Validación en actualización
test('rechaza numeros en el apellido al actualizar', function () {
    $persona = Persona::factory()->create(['nombre' => 'JUAN', 'apellido' => 'PEREZ']);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/admin/personas/{$persona->id}", [
            'apellido' => 'PEREZ123',
            'nombre' => 'JUAN',
            'documento_tipo_id' => $persona->documento_tipo_id,
            'documento_numero' => $persona->getRawOriginal('documento_numero'),
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('apellido');
});
