<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use Illuminate\Http\Request;

readonly class PersonaDomicilioDTO
{
    public function __construct(
        public ?int $nacionId = null,
        public ?int $localidadId = null,
        public ?int $calleId = null,
        public ?int $calleEntre1Id = null,
        public ?int $calleEntre2Id = null,
        public ?string $numero = null,
        public ?string $piso = null,
        public ?string $departamento = null,
        public ?string $torre = null,
        public ?string $codigoPostal = null,
        public ?string $observaciones  = null,
        public bool $observacionesProvided = false,
        public bool $blanquear = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $observacionesProvided = array_key_exists('observaciones', $request->all());

        return new self(
            nacionId: $request->filled('nacion_id') ? (int) $request->nacion_id : null,
            localidadId: $request->filled('localidad_id') ? (int) $request->localidad_id : null,
            calleId: $request->filled('calle_id') ? (int) $request->calle_id : null,
            calleEntre1Id: $request->filled('calle_entre_1_id') ? (int) $request->calle_entre_1_id : null,
            calleEntre2Id: $request->filled('calle_entre_2_id') ? (int) $request->calle_entre_2_id : null,
            numero: $request->filled('numero') ? (string) $request->numero : null,
            piso: $request->filled('piso') ? (string) $request->piso : null,
            departamento: $request->filled('departamento') ? (string) $request->departamento : null,
            torre: $request->filled('torre') ? (string) $request->torre : null,
            codigoPostal: $request->filled('codigo_postal') ? (string) $request->codigo_postal : null,
            observaciones: $observacionesProvided
                ? (($request->observaciones !== null && $request->observaciones !== '')
                    ? (string) $request->observaciones
                    : null)
                : null,
            observacionesProvided: $observacionesProvided,
            blanquear: filter_var($request->input('blanquear', false), FILTER_VALIDATE_BOOL),
        );
    }

    /** Actualización parcial: descarta null para NO pisar valores previos. */
    public function toArray(): array
    {
        $data = $this->filtrarNulos([
            'nacion_id' => $this->nacionId,
            'localidad_id' => $this->localidadId,
            'calle_id' => $this->calleId,
            'calle_entre_1_id' => $this->calleEntre1Id,
            'calle_entre_2_id' => $this->calleEntre2Id,
            'numero' => $this->numero,
            'piso' => $this->piso,
            'departamento' => $this->departamento,
            'torre' => $this->torre,
            'codigo_postal' => $this->codigoPostal,
        ]);

        // Si el frontend envió la clave (aunque sea vacía) => se include (null = limpiar)
        if ($this->observacionesProvided) {
            $data['observaciones'] = $this->observaciones;
        }

        return $data;
    }

    /** Blanqueo total ("Domicilio Desconocido"): TODOS los campos geográficos a null. */
    public function toBlankArray(): array
    {
        $data = [
            'nacion_id' => null,
            'localidad_id' => null,
            'calle_id' => null,
            'calle_entre_1_id' => null,
            'calle_entre_2_id' => null,
            'numero' => null,
            'piso' => null,
            'departamento' => null,
            'torre' => null,
            'codigo_postal' => null,
        ];

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