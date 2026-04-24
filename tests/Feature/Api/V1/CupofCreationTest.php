<?php

use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\Cupof;
use Laravel\Sanctum\Sanctum;
use Tests\ProvidesRoles;

uses(Tests\TestCase::class, ProvidesRoles::class);

test('admin can create a cupof', function () {
    $this->seedRoles();
    $admin = Usuario::factory()->create([
        'email_verified_at' => now(),
    ]);
    $admin->givePermissionTo('sistema.usuarios');
    Sanctum::actingAs($admin);

    $escuela = Escuela::factory()->create();

    $data = [
        'codigo_cupof' => 'TEST-CUPOF-001',
        'escuela_id' => $escuela->id,
        'escalafon' => 'docente',
        'tipo_puesto' => 'cargo',
        'nombre_cargo' => 'Director',
        'cantidad' => 1,
    ];

    $response = $this->postJson('/api/v1/admin/cupofs', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cupofs', [
        'codigo_cupof' => 'TEST-CUPOF-001',
        'escuela_id' => $escuela->id,
        'created_by' => $admin->id,
    ]);
});
