<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use Illuminate\Http\Request;

readonly class PersonaDomicilioDTO
{
    public function __construct(
        public bool $nacionProvided = false,
        public bool $provinciaProvided = false,
        public bool $departamentoProvided = false,
        public bool $localidadProvided = false,
        public ?int $nacionId = null,
        public ?int $provinciaId = null,
        public ?int $departamentoId = null,
        public ?int $localidadId = null,
        public ?int $calleId = null,
        public ?string $calleNombre = null,
        public ?int $calleEntre1Id = null,
        public ?string $calleEntre1Nombre = null,
        public ?int $calleEntre2Id = null,
        public ?string $calleEntre2Nombre = null,
        public ?string $numero = null,
        public ?string $piso = null,
        public ?string $unidad = null,
        public ?string $torre = null,
        public ?string $codigoPostal = null,
        public ?string $observaciones = null,
        public bool $observacionesProvided = false,
        public bool $blanquear = false,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $observacionesProvided = array_key_exists('observaciones', $request->all());

        return new self(
            nacionProvided: $request->has('nacion_id'),
            provinciaProvided: $request->has('provincia_id'),
            departamentoProvided: $request->has('departamento_id'),
            localidadProvided: $request->has('localidad_id'),
            nacionId: $request->filled('nacion_id') ? (int) $request->nacion_id : null,
            provinciaId: $request->filled('provincia_id') ? (int) $request->provincia_id : null,
            departamentoId: $request->filled('departamento_id') ? (int) $request->departamento_id : null,
            localidadId: $request->filled('localidad_id') ? (int) $request->localidad_id : null,
            calleId: $request->filled('calle_id') ? (int) $request->calle_id : null,
            calleNombre: $request->filled('calle_nombre') ? (string) $request->calle_nombre : null,
            calleEntre1Id: $request->filled('calle_entre_1_id') ? (int) $request->calle_entre_1_id : null,
            calleEntre1Nombre: $request->filled('calle_entre_1_nombre') ? (string) $request->calle_entre_1_nombre : null,
            calleEntre2Id: $request->filled('calle_entre_2_id') ? (int) $request->calle_entre_2_id : null,
            calleEntre2Nombre: $request->filled('calle_entre_2_nombre') ? (string) $request->calle_entre_2_nombre : null,
            numero: $request->filled('numero') ? (string) $request->numero : null,
            piso: $request->filled('piso') ? (string) $request->piso : null,
            unidad: $request->filled('unidad') ? (string) $request->unidad : null,
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
        $data = [];
        if ($this->nacionProvided) {
            $data['nacion_id'] = $this->nacionId;
        }
        if ($this->provinciaProvided) {
            $data['provincia_id'] = $this->provinciaId;
        }
        if ($this->departamentoProvided) {
            $data['departamento_id'] = $this->departamentoId;
        }
        if ($this->localidadProvided) {
            $data['localidad_id'] = $this->localidadId;
        }

        $data = array_merge($data, $this->filtrarNulos([
            'calle_id' => $this->calleId,
            'calle_nombre' => $this->calleNombre,
            'calle_entre_1_id' => $this->calleEntre1Id,
            'calle_entre_1_nombre' => $this->calleEntre1Nombre,
            'calle_entre_2_id' => $this->calleEntre2Id,
            'calle_entre_2_nombre' => $this->calleEntre2Nombre,
            'numero' => $this->numero,
            'piso' => $this->piso,
            'unidad' => $this->unidad,
            'torre' => $this->torre,
            'codigo_postal' => $this->codigoPostal,
        ]));

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
            'provincia_id' => null,
            'departamento_id' => null,
            'localidad_id' => null,
            'calle_id' => null,
            'calle_nombre' => null,
            'calle_entre_1_id' => null,
            'calle_entre_1_nombre' => null,
            'calle_entre_2_id' => null,
            'calle_entre_2_nombre' => null,
            'numero' => null,
            'piso' => null,
            'unidad' => null,
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
        return array_filter($datos, fn($valor) => $valor !== null);
    }
}