<?php

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserProfileDTO;

test('CreateUserDTO builds correctly from array and converts to array', function () {
    $data = [
        'nombre' => 'Juan Perez',
        'email' => 'juan@ejemplo.com',
        'documento_tipo_id' => 1,
        'documento_numero' => '12345678',
        'password' => 'secret123',
        'es_administrador' => true,
    ];

    $dto = CreateUserDTO::fromArray($data);

    expect($dto->nombre)->toBe('Juan Perez');
    expect($dto->email)->toBe('juan@ejemplo.com');
    expect($dto->documentoTipoId)->toBe(1);
    expect($dto->documentoNumero)->toBe('12345678');
    expect($dto->password)->toBe('secret123');
    expect($dto->esAdministrador)->toBeTrue();

    $array = $dto->toArray();
    expect($array['nombre'])->toBe('Juan Perez');
    expect($array['email'])->toBe('juan@ejemplo.com');
    expect($array['documento_tipo_id'])->toBe(1);
    expect($array['documento_numero'])->toBe('12345678');
    expect($array['password'])->toBe('secret123');
    expect($array['es_administrador'])->toBeTrue();
});

test('UpdateUserProfileDTO handles partial profile updates correctly', function () {
    $data = [
        'email' => 'nuevo_email@ejemplo.com',
    ];

    $dto = UpdateUserProfileDTO::fromArray($data);

    expect($dto->email)->toBe('nuevo_email@ejemplo.com');
    expect($dto->nombre)->toBeNull();
    expect($dto->documentoTipoId)->toBeNull();
    expect($dto->password)->toBeNull();

    $array = $dto->toArray();
    expect($array)->toHaveKey('email');
    expect($array)->not()->toHaveKey('nombre');
    expect($array)->not()->toHaveKey('password');
});
