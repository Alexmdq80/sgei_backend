<?php

use App\DTOs\Escuela\CreateEscuelaDTO;
use App\DTOs\Escuela\UpdateEscuelaDTO;

test('CreateEscuelaDTO builds correctly from array and converts to array', function () {
    $data = [
        'nombre' => 'Escuela Primaria N 1',
        'numero' => '1',
        'cue_anexo' => '060000100',
        'localidad_id' => 10,
        'clave_provincial' => 'EP1',
        'domicilio' => 'Calle Falsa 123',
    ];

    $dto = CreateEscuelaDTO::fromArray($data);

    expect($dto->nombre)->toBe('Escuela Primaria N 1');
    expect($dto->numero)->toBe('1');
    expect($dto->cueAnexo)->toBe('060000100');
    expect($dto->localidadId)->toBe(10);
    expect($dto->claveProvincial)->toBe('EP1');
    expect($dto->domicilio)->toBe('Calle Falsa 123');

    $array = $dto->toArray();
    expect($array['nombre'])->toBe('Escuela Primaria N 1');
    expect($array['localidad_id'])->toBe(10);
});

test('UpdateEscuelaDTO updates fields properly', function () {
    $data = [
        'nombre' => 'Escuela Primaria N 1 Refactorizada',
        'telefono' => '1122334455',
    ];

    $dto = UpdateEscuelaDTO::fromArray($data);

    expect($dto->nombre)->toBe('Escuela Primaria N 1 Refactorizada');
    expect($dto->telefono)->toBe('1122334455');
    expect($dto->cueAnexo)->toBeNull();

    $array = $dto->toArray();
    expect($array)->toHaveKey('nombre');
    expect($array)->toHaveKey('telefono');
    expect($array)->not()->toHaveKey('cue_anexo');
});
