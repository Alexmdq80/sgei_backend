<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use App\ValueObjects\DocumentoIdentidad;
use Illuminate\Foundation\Http\FormRequest;

readonly class CreatePersonaDTO
{
    public function __construct(
        public string $apellido,
        public string $nombre,
        public DocumentoIdentidad $documentoIdentidad,
        public ?string $nombreAlternativo = null,
        public ?int $sexoId = null,
        public ?int $generoId = null,
        public ?string $nacimientoFecha = null,
        public ?int $nacionalidadNacionId = null,
        public ?int $nacionId = null,
        public ?int $provinciaId = null,
        public ?int $departamentoId = null,
        public ?int $localidadId = null,
        public ?string $tramite = null,
        public ?string $cuilPrefijo = null,
        public ?string $cuilSufijo = null,
        public ?bool $poseeCpiSi = null,
        public ?bool $poseeDocExtSi = null,
        public ?bool $viveSi = null,
    ) {}

    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            apellido: (string) ($data['apellido'] ?? ''),
            nombre: (string) ($data['nombre'] ?? ''),
            documentoIdentidad: new DocumentoIdentidad(
                tipoId: (int) ($data['documento_tipo_id'] ?? 0),
                numero: (string) ($data['documento_numero'] ?? ''),
            ),
            nombreAlternativo: isset($data['nombre_alternativo']) ? (string) $data['nombre_alternativo'] : null,
            sexoId: isset($data['sexo_id']) ? (int) $data['sexo_id'] : null,
            generoId: isset($data['genero_id']) ? (int) $data['genero_id'] : null,
            nacimientoFecha: isset($data['nacimiento_fecha']) ? (string) $data['nacimiento_fecha'] : null,
            nacionalidadNacionId: isset($data['nacionalidad_nacion_id']) ? (int) $data['nacionalidad_nacion_id'] : null,
            nacionId: isset($data['nacion_id']) ? (int) $data['nacion_id'] : null,
            provinciaId: isset($data['provincia_id']) ? (int) $data['provincia_id'] : null,
            departamentoId: isset($data['departamento_id']) ? (int) $data['departamento_id'] : null,
            localidadId: isset($data['localidad_id']) ? (int) $data['localidad_id'] : null,
            tramite: isset($data['tramite']) ? (string) $data['tramite'] : null,
            cuilPrefijo: isset($data['CUIL_prefijo']) ? (string) $data['CUIL_prefijo'] : null,
            cuilSufijo: isset($data['CUIL_sufijo']) ? (string) $data['CUIL_sufijo'] : null,
            poseeCpiSi: isset($data['posee_cpi_si']) ? (bool) $data['posee_cpi_si'] : null,
            poseeDocExtSi: isset($data['posee_docExt_si']) ? (bool) $data['posee_docExt_si'] : null,
            viveSi: isset($data['vive_si']) ? (bool) $data['vive_si'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'apellido' => $this->apellido,
            'nombre' => $this->nombre,
            'documento_tipo_id' => $this->documentoIdentidad->tipoId(),
            'documento_numero' => $this->documentoIdentidad->numero(),
            'nombre_alternativo' => $this->nombreAlternativo,
            'sexo_id' => $this->sexoId,
            'genero_id' => $this->generoId,
            'nacimiento_fecha' => $this->nacimientoFecha,
            'nacionalidad_nacion_id' => $this->nacionalidadNacionId,
            'nacion_id' => $this->nacionId,
            'provincia_id' => $this->provinciaId,
            'departamento_id' => $this->departamentoId,
            'localidad_id' => $this->localidadId,
            'tramite' => $this->tramite,
            'CUIL_prefijo' => $this->cuilPrefijo,
            'CUIL_sufijo' => $this->cuilSufijo,
            'posee_cpi_si' => $this->poseeCpiSi,
            'posee_docExt_si' => $this->poseeDocExtSi,
            'vive_si' => $this->viveSi,
        ], fn ($val) => $val !== null);
    }
}
