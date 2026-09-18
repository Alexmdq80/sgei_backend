<?php

use App\Models\Calle;
use App\Models\Localidad;
use App\Models\LocalidadCensal;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'DocumentoTipoSeeder']);
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->admin = Usuario::factory()->create(['es_administrador' => true]);
    $this->admin->assignRole('superuser');
});

test('buscar calles por q respeta estrictamente localidad_id', function () {
    $lc1 = LocalidadCensal::create(['nombre' => 'TANDIL CENSAL']);
    $lc2 = LocalidadCensal::create(['nombre' => 'OLAVARRIA CENSAL']);

    Calle::create(['nombre' => 'AV. RIVADAVIA', 'localidad_censal_id' => $lc1->id]);
    // Misma calle en OTRA localidad censal: DEBE quedar excluida
    Calle::create(['nombre' => 'AV. RIVADAVIA', 'localidad_censal_id' => $lc2->id]);

    $localidad = Localidad::factory()->create([
        'nombre' => 'TANDIL',
        'localidad_censal_id' => $lc1->id,
    ]);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/admin/calles?q=RIVADAVIA&localidad_id={$localidad->id}");

    $response->assertOk();

    $items = $response->json('data');

    expect($items)->toHaveCount(1)
        ->and($items[0]['localidad_censal_id'])->toBe($lc1->id)
        ->and($items[0]['nombre'])->toBe('AV. RIVADAVIA');
});
