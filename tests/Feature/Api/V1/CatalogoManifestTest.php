<?php

declare(strict_types=1);

use App\Models\Calle;
use App\Models\CatalogoVersion;
use App\Models\Departamento;
use App\Models\DocumentoSituacion;
use App\Models\DocumentoTipo;
use App\Models\Genero;
use App\Models\Localidad;
use App\Models\Nacion;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\Sexo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('el manifiesto devuelve todas las claves de catálogos con su versión', function () {
    $response = $this->getJson('/api/v1/catalogos/manifest');

    $clavesEsperadas = [
        'naciones',
        'provincias',
        'regiones',
        'departamentos',
        'localidades',
        'documento_tipos',
        'documento_situacions',
        'sexos',
        'generos',
        'calles',
    ];

    $response->assertStatus(200)
        ->assertJsonStructure($clavesEsperadas);

    $payload = $response->json();

    // Contrato estable con el frontend: mismas claves y en el mismo orden.
    expect(array_keys($payload))->toBe($clavesEsperadas)
        ->and($payload)->toHaveCount(10);

    foreach ($payload as $clave => $version) {
        expect($version)->toBeString()->not->toBeEmpty("La versión de {$clave} está vacía");
    }

    // La migración deja la tabla poblada con los 9 catálogos.
    expect(CatalogoVersion::query()->count())->toBe(10);
});

test('el manifiesto se resuelve en una única consulta a catalogo_versiones', function () {
    DB::enableQueryLog();

    $this->getJson('/api/v1/catalogos/manifest')->assertStatus(200);

    $consultas = collect(DB::getQueryLog())->pluck('query');
    DB::disableQueryLog();

    $sobreVersiones = $consultas->filter(
        static fn (string $query): bool => str_contains($query, 'catalogo_versions')
    );

    expect($sobreVersiones)->toHaveCount(1);
});

test('crear y eliminar una provincia actualiza la versión de provincias en el manifiesto', function () {
    $inicial = $this->getJson('/api/v1/catalogos/manifest')->json();

    $provincia = Provincia::factory()->create(); // evento `saved` => touchVersion('provincias')

    $trasCrear = $this->getJson('/api/v1/catalogos/manifest')->json();

    expect($trasCrear['provincias'])->not->toBe($inicial['provincias'])
        ->and($trasCrear['naciones'])->toBe($inicial['naciones']); // aislamiento por tabla

    $provincia->delete(); // soft delete => evento `deleted`

    $trasSoftDelete = $this->getJson('/api/v1/catalogos/manifest')->json();

    expect($trasSoftDelete['provincias'])->not->toBe($trasCrear['provincias']);

    $provincia->forceDelete(); // hard delete => también dispara `deleted`

    $trasHardDelete = $this->getJson('/api/v1/catalogos/manifest')->json();

    expect($trasHardDelete['provincias'])->not->toBe($trasSoftDelete['provincias']);
});

test('el trait versiona automáticamente cada catálogo maestro y no toca los demás', function () {
    $casos = [
        [
            'crear' => static fn () => Nacion::create(['nombre' => 'NACION DE PRUEBA']),
            'afectadas' => ['naciones'],
        ],
        [
            'crear' => static fn () => Region::create(['numero' => '999']),
            'afectadas' => ['regiones'],
        ],
        [
            'crear' => static fn () => Departamento::factory()->create(),
            'afectadas' => ['departamentos', 'provincias'], // la factory crea una Provincia anidada
        ],
        [
            'crear' => static fn () => Localidad::factory()->create(),
            'afectadas' => ['localidades', 'departamentos', 'provincias'], // cadena de factories
        ],
        [
            'crear' => static fn () => DocumentoTipo::factory()->create(),
            'afectadas' => ['documento_tipos'],
        ],
        [
            'crear' => static fn () => DocumentoSituacion::create(['nombre' => 'SITUACION DE PRUEBA']),
            'afectadas' => ['documento_situacions'],
        ],
        [
            'crear' => static fn () => Sexo::create(['nombre' => 'SEXO DE PRUEBA']),
            'afectadas' => ['sexos'],
        ],
        [
            'crear' => static fn () => Genero::create(['nombre' => 'GENERO DE PRUEBA', 'orden' => 99]),
            'afectadas' => ['generos'],
        ],
        [
            'crear' => static fn () => Calle::create(['nombre' => 'CALLE DE PRUEBA']),
            'afectadas' => ['calles'],
        ],
    ];

    foreach ($casos as $caso) {
        $antes = $this->getJson('/api/v1/catalogos/manifest')->json();

        ($caso['crear'])();

        $despues = $this->getJson('/api/v1/catalogos/manifest')->json();

        foreach ($antes as $clave => $version) {
            $deberiaCambiar = in_array($clave, $caso['afectadas'], true);

            expect($despues[$clave] === $version)
                ->toBe(! $deberiaCambiar, "La versión de '{$clave}' no se comportó como se esperaba");
        }
    }
});
