<?php

namespace App\DTOs\Escuela;

use Illuminate\Foundation\Http\FormRequest;

readonly class UpdateEscuelaDTO
{
    public function __construct(
        public ?string $nombre = null,
        public ?string $numero = null,
        public ?string $cueAnexo = null,
        public ?int $localidadId = null,
        public ?string $claveProvincial = null,
        public ?int $ambitoId = null,
        public ?int $dependenciaId = null,
        public ?int $sectorId = null,
        public ?string $domicilio = null,
        public ?string $telefono = null,
        public ?string $email = null,
        public ?string $codigoPostal = null,
        public ?array $modalidadesNivelesIds = null,
    ) {}

    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: isset($data['nombre']) ? (string) $data['nombre'] : null,
            numero: isset($data['numero']) ? (string) $data['numero'] : null,
            cueAnexo: isset($data['cue_anexo']) ? (string) $data['cue_anexo'] : null,
            localidadId: isset($data['localidad_id']) ? (int) $data['localidad_id'] : null,
            claveProvincial: isset($data['clave_provincial']) ? (string) $data['clave_provincial'] : null,
            ambitoId: isset($data['ambito_id']) ? (int) $data['ambito_id'] : null,
            dependenciaId: isset($data['dependencia_id']) ? (int) $data['dependencia_id'] : null,
            sectorId: isset($data['sector_id']) ? (int) $data['sector_id'] : null,
            domicilio: isset($data['domicilio']) ? (string) $data['domicilio'] : null,
            telefono: isset($data['telefono']) ? (string) $data['telefono'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            codigoPostal: isset($data['codigo_postal']) ? (string) $data['codigo_postal'] : null,
            modalidadesNivelesIds: isset($data['modalidades_niveles_ids']) ? (array) $data['modalidades_niveles_ids'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'nombre' => $this->nombre,
            'numero' => $this->numero,
            'cue_anexo' => $this->cueAnexo,
            'localidad_id' => $this->localidadId,
            'clave_provincial' => $this->claveProvincial,
            'ambito_id' => $this->ambitoId,
            'dependencia_id' => $this->dependenciaId,
            'sector_id' => $this->sectorId,
            'domicilio' => $this->domicilio,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'codigo_postal' => $this->codigoPostal,
            'modalidades_niveles_ids' => $this->modalidadesNivelesIds,
        ], fn ($val) => $val !== null);
    }
}
