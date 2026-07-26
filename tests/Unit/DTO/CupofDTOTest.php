<?php

use App\DTOs\Cupof\CreateCupofDTO;

test('CreateCupofDTO builds correctly from array and converts to array', function () {
    $data = [
        'codigo_cupof' => 'CPF-10020',
        'escuela_id' => 5,
        'escalafon_id' => 1,
        'puesto_tipo_id' => 2,
        'nombre_cargo' => 'profesor',
        'cantidad' => 10,
    ];

    $dto = CreateCupofDTO::fromArray($data);

    expect($dto->codigoCupof)->toBe('CPF-10020');
    expect($dto->escuelaId)->toBe(5);
    expect($dto->escalafonId)->toBe(1);
    expect($dto->puestoTipoId)->toBe(2);
    expect($dto->nombreCargo)->toBe('profesor');
    expect($dto->cantidad)->toBe(10);

    $array = $dto->toArray();
    expect($array['codigo_cupof'])->toBe('CPF-10020');
    expect($array['escuela_id'])->toBe(5);
    expect($array['escalafon_id'])->toBe(1);
    expect($array['puesto_tipo_id'])->toBe(2);
});
