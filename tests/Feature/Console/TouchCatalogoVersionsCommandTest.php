<?php

declare(strict_types=1);

use App\Models\CatalogoVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('refresca la versión de todos los catálogos versionados', function () {
    $antes = CatalogoVersion::pluck('version', 'tabla');

    $this->artisan('catalogos:touch-versions')->assertSuccessful();

    $despues = CatalogoVersion::pluck('version', 'tabla');

    expect($despues)->toHaveCount(9);

    foreach ($antes as $tabla => $version) {
        expect($despues[$tabla])->not->toBe($version);
    }
});

test('respeta la opción --tabla y no toca el resto', function () {
    $antes = CatalogoVersion::pluck('version', 'tabla');

    $this->artisan('catalogos:touch-versions', ['--tabla' => ['provincias']])->assertSuccessful();

    $despues = CatalogoVersion::pluck('version', 'tabla');

    expect($despues['provincias'])->not->toBe($antes['provincias'])
        ->and($despues['nacions'])->toBe($antes['nacions']);
});

test('con --dry-run no modifica las versiones almacenadas', function () {
    $antes = CatalogoVersion::pluck('version', 'tabla')->all();

    $this->artisan('catalogos:touch-versions', ['--dry-run' => true])->assertSuccessful();

    expect(CatalogoVersion::pluck('version', 'tabla')->all())->toBe($antes);
});
