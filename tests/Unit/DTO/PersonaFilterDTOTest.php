<?php

use App\DTOs\Persona\PersonaFilterDTO;
use Illuminate\Http\Request;

test('PersonaFilterDTO instantiates correctly from array with all jurisdictional and demographic fields', function () {
    $data = [
        'search' => 'Perez',
        'only_agents' => true,
        'escuela_id' => 10,
        'provincia_id' => 6,
        'departamento_id' => 12,
        'localidad_id' => 45,
        'nacionalidad_nacion_id' => 1,
        'sexo_id' => 2,
        'genero_id' => 1,
        'documento_tipo_id' => 1,
        'has_user' => true,
        'per_page' => 25,
    ];

    $dto = PersonaFilterDTO::fromArray($data);

    expect($dto->search)->toBe('Perez');
    expect($dto->onlyAgents)->toBeTrue();
    expect($dto->escuelaId)->toBe(10);
    expect($dto->provinciaId)->toBe(6);
    expect($dto->departamentoId)->toBe(12);
    expect($dto->localidadId)->toBe(45);
    expect($dto->nacionalidadNacionId)->toBe(1);
    expect($dto->sexoId)->toBe(2);
    expect($dto->generoId)->toBe(1);
    expect($dto->documentoTipoId)->toBe(1);
    expect($dto->hasUser)->toBeTrue();
    expect($dto->perPage)->toBe(25);
});

test('PersonaFilterDTO instantiates correctly from Request', function () {
    $request = Request::create('/api/v1/admin/personas', 'GET', [
        'search' => 'Gomez',
        'provincia_id' => '3',
        'has_user' => '0',
    ]);

    $dto = PersonaFilterDTO::fromRequest($request);

    expect($dto->search)->toBe('Gomez');
    expect($dto->provinciaId)->toBe(3);
    expect($dto->hasUser)->toBeFalse();
    expect($dto->onlyAgents)->toBeNull();
    expect($dto->perPage)->toBeNull();
});
