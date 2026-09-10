<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use Illuminate\Http\Request;

readonly class PersonaContactoDTO
{
    public function __construct(
        public ?string $telefonoCodigoArea = null,
        public ?string $telefono = null,
        public ?string $celularCodigoArea = null,
        public ?string $celular = null,
        public ?string $email = null,
        public ?string $observaciones = null,
        public ?bool $observacionesProvided = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $observacionesProvided = array_key_exists('observaciones', $request->all());

        return new self(
            telefonoCodigoArea: $request->filled('telefono_codigo_area') ? (string) $request->telefono_codigo_area : null,
            telefono: $request->filled('telefono') ? (string) $request->telefono : null,
            celularCodigoArea: $request->filled('celular_codigo_area') ? (string) $request->celular_codigo_area : null,
            celular: $request->filled('celular') ? (string) $request->celular : null,
            email: $request->filled('email') ? (string) $request->email : null,
            observaciones: $observacionesProvided
            ? (($request->observaciones !== null && $request->observaciones !== '')
                ? (string) $request->observaciones
                : null)
            : null,
            observacionesProvided: $observacionesProvided,
        );
    }

    public function toArray(): array
    {
        $data = $this->filtrarNulos([
            'telefono_codigo_area' => $this->telefonoCodigoArea,
            'telefono' => $this->telefono,
            'celular_codigo_area' => $this->celularCodigoArea,
            'celular' => $this->celular,
            'email' => $this->email,
        ]);

        // Si el frontend envió la clave (aunque sea vacía) => se incluye (null = limpiar)
        if ($this->observacionesProvided) {
            $data['observaciones'] = $this->observaciones;
        }

        return $data;
    }

    /** Descarta claves null para NO pisar valores previos en la BD. */
    private function filtrarNulos(array $datos): array
    {
        return array_filter($datos, fn ($valor) => $valor !== null);
    }
}
