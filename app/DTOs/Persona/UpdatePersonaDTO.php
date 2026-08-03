<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use App\ValueObjects\DocumentoIdentidad;
use Illuminate\Foundation\Http\FormRequest;

/**
 * DTO para la actualización parcial de una Persona.
 * Todos los campos son opcionales (solo se actualizan los que se proporcionan).
 */
readonly class UpdatePersonaDTO
{
    public function __construct(
        public ?string $apellido = null,
        public ?string $nombre = null,
        public ?DocumentoIdentidad $documentoIdentidad = null,
        public ?string $email = null,
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
        $documentoIdentidad = null;
        if (isset($data['documento_tipo_id']) || isset($data['documento_numero'])) {
            $documentoIdentidad = new DocumentoIdentidad(
                tipoId: (int) ($data['documento_tipo_id'] ?? 0),
                numero: (string) ($data['documento_numero'] ?? ''),
            );
        }

        return new self(
            apellido: isset($data['apellido']) ? (string) $data['apellido'] : null,
            nombre: isset($data['nombre']) ? (string) $data['nombre'] : null,
            documentoIdentidad: $documentoIdentidad,
            email: array_key_exists('email', $data) ? ($data['email'] !== null ? (string) $data['email'] : null) : null,
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

    /**
     * Returns only the non-null fields as a flat array for Persona::update().
     * documento_numero is returned as the raw string, not the Value Object,
     * so it's safe to pass directly to Eloquent.
     */
    public function toPersonaArray(): array
    {
        $data = [];

        if ($this->apellido !== null)            $data['apellido'] = $this->apellido;
        if ($this->nombre !== null)              $data['nombre'] = $this->nombre;
        if ($this->documentoIdentidad !== null) {
            $data['documento_tipo_id'] = $this->documentoIdentidad->tipoId();
            $data['documento_numero']  = $this->documentoIdentidad->numero(); // raw string
        }
        if ($this->nombreAlternativo !== null)   $data['nombre_alternativo'] = $this->nombreAlternativo;
        if ($this->sexoId !== null)              $data['sexo_id'] = $this->sexoId;
        if ($this->generoId !== null)            $data['genero_id'] = $this->generoId;
        if ($this->nacimientoFecha !== null)     $data['nacimiento_fecha'] = $this->nacimientoFecha;
        if ($this->nacionalidadNacionId !== null) $data['nacionalidad_nacion_id'] = $this->nacionalidadNacionId;
        if ($this->nacionId !== null)            $data['nacion_id'] = $this->nacionId;
        if ($this->provinciaId !== null)         $data['provincia_id'] = $this->provinciaId;
        if ($this->departamentoId !== null)      $data['departamento_id'] = $this->departamentoId;
        if ($this->localidadId !== null)         $data['localidad_id'] = $this->localidadId;
        if ($this->tramite !== null)             $data['tramite'] = $this->tramite;
        if ($this->cuilPrefijo !== null)         $data['CUIL_prefijo'] = $this->cuilPrefijo;
        if ($this->cuilSufijo !== null)          $data['CUIL_sufijo'] = $this->cuilSufijo;
        if ($this->poseeCpiSi !== null)          $data['posee_cpi_si'] = $this->poseeCpiSi;
        if ($this->poseeDocExtSi !== null)       $data['posee_docExt_si'] = $this->poseeDocExtSi;
        if ($this->viveSi !== null)              $data['vive_si'] = $this->viveSi;

        return $data;
    }
}
