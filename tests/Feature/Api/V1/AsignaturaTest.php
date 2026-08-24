<?php

use App\Models\Asignatura;
use App\Models\AnioPlan;
use App\Models\Usuario;
use Tests\ProvidesRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, ProvidesRoles::class);

beforeEach(function () {
    $this->seedRoles();

    // Super administrador: puede gestionar asignaturas
    $this->admin = Usuario::factory()->create(['estado' => 'activo']);
    $this->admin->assignRole('superuser');

    // Usuario sin permisos curriculares
    $this->profesor = Usuario::factory()->create(['estado' => 'activo']);
    $this->profesor->assignRole('profesor');

    $this->anioPlan = AnioPlan::factory()->create();
});

test('el superuser puede listar asignaturas de un año de plan', function () {
    Asignatura::factory()->count(3)->create(['anio_plan_id' => $this->anioPlan->id]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/anio-plan/{$this->anioPlan->id}/asignaturas");

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('el superuser puede crear una asignatura', function () {
    $data = [
        'nombre' => 'Matemática Aplicada',
        'anio_plan_id' => $this->anioPlan->id,
        'horas_semanales' => 4,
        'codigo' => 'MAT-01',
        'orden' => 1
    ];

    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/asignaturas', $data);

    $response->assertStatus(201)
        ->assertJsonFragment(['nombre' => 'Matemática Aplicada']);

    $this->assertDatabaseHas('asignaturas', ['nombre' => 'Matemática Aplicada']);
});

test('el superuser puede actualizar una asignatura', function () {
    $asignatura = Asignatura::factory()->create(['anio_plan_id' => $this->anioPlan->id]);

    $data = [
        'nombre' => 'Nombre Editado',
        'anio_plan_id' => $this->anioPlan->id,
        'horas_semanales' => 6,
    ];

    $response = $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/asignaturas/{$asignatura->id}", $data);

    $response->assertStatus(200)
        ->assertJsonFragment(['nombre' => 'Nombre Editado', 'horas_semanales' => 6]);
});

test('el superuser puede eliminar una asignatura', function () {
    $asignatura = Asignatura::factory()->create(['anio_plan_id' => $this->anioPlan->id]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/asignaturas/{$asignatura->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('asignaturas', ['id' => $asignatura->id]);
});

test('un usuario sin permisos no puede gestionar asignaturas', function () {
    $response = $this->actingAs($this->profesor, 'sanctum')
        ->postJson('/api/v1/asignaturas', [
            'nombre' => 'Física',
            'anio_plan_id' => $this->anioPlan->id,
            'horas_semanales' => 4
        ]);

    $response->assertStatus(403);
});

test('la carga horaria debe estar entre 0 y 40 horas', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/asignaturas', [
            'nombre' => 'Invalida',
            'anio_plan_id' => $this->anioPlan->id,
            'horas_semanales' => 50 // Excede el máximo
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['horas_semanales']);
});

