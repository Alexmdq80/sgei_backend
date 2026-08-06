<?php

namespace App\DTOs\Cupof;

use Illuminate\Foundation\Http\FormRequest;

readonly class CreateCupofDTO
{
    public function __construct(
        public string $codigoCupof,
        public int $escuelaId,
        public int $escalafonId,
        public int $puestoTipoId,
        public ?int $asignaturaId = null,
        public ?string $nombreCargo = null,
        public int $cantidad = 1,
        public string $estadoCupof = 'disponible',
    ) {}

    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            codigoCupof: (string) ($data['codigo_cupof'] ?? ''),
            escuelaId: (int) ($data['escuela_id'] ?? 0),
            escalafonId: (int) ($data['escalafon_id'] ?? 0),
            puestoTipoId: (int) ($data['puesto_tipo_id'] ?? 0),
            asignaturaId: isset($data['asignatura_id']) ? (int) $data['asignatura_id'] : null,
            nombreCargo: isset($data['nombre_cargo']) ? (string) $data['nombre_cargo'] : null,
            cantidad: isset($data['cantidad']) ? (int) $data['cantidad'] : 1,
            estadoCupof: (string) ($data['estado_cupof'] ?? 'disponible'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'codigo_cupof' => $this->codigoCupof,
            'escuela_id' => $this->escuelaId,
            'escalafon_id' => $this->escalafonId,
            'puesto_tipo_id' => $this->puestoTipoId,
            'asignatura_id' => $this->asignaturaId,
            'nombre_cargo' => $this->nombreCargo,
            'cantidad' => $this->cantidad,
            'estado_cupof' => $this->estadoCupof,
        ], fn ($val) => $val !== null);
    }
}
