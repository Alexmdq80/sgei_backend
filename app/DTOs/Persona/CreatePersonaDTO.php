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
        public ?DocumentoIdentidad $documentoIdentidad = null,   // ANTES: sin "?"
        public ?string $nombreAlternativo = null,
        public ?int $sexoId = null,
        public ?int $generoId = null,
        public ?int $documentoSituacionId = null,                 // NUEVO
        public ?string $nacimientoFecha = null,
        public ?int $nacionalidadNacionId = null,
        public ?int $nacionId = null,
        public ?int $provinciaId = null,
        public ?int $departamentoId = null,
        public ?int $localidadId = null,
        public ?string $tramite = null,
        public ?string $cuilPrefijo = null,
        public ?string $cuilSufijo = null,
        public ?bool $viveSi = null,
        public ?string $email = null
    ) {
    }


    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        $documentoIdentidad = (isset($data['documento_tipo_id']) && isset($data['documento_numero']) && $data['documento_numero'] !== '')
            ? new DocumentoIdentidad(
                tipoId: (int) $data['documento_tipo_id'],
                numero: (string) $data['documento_numero'],
            )
            : null;

        return new self(
            apellido: (string) ($data['apellido'] ?? ''),
            nombre: (string) ($data['nombre'] ?? ''),
            documentoIdentidad: $documentoIdentidad,
            nombreAlternativo: isset($data['nombre_alternativo']) ? (string) $data['nombre_alternativo'] : null,
            sexoId: isset($data['sexo_id']) ? (int) $data['sexo_id'] : null,
            generoId: isset($data['genero_id']) ? (int) $data['genero_id'] : null,
            documentoSituacionId: isset($data['documento_situacion_id']) ? (int) $data['documento_situacion_id'] : null,
            nacimientoFecha: isset($data['nacimiento_fecha']) ? (string) $data['nacimiento_fecha'] : null,
            nacionalidadNacionId: isset($data['nacionalidad_nacion_id']) ? (int) $data['nacionalidad_nacion_id'] : null,
            nacionId: isset($data['nacion_id']) ? (int) $data['nacion_id'] : null,
            provinciaId: isset($data['provincia_id']) ? (int) $data['provincia_id'] : null,
            departamentoId: isset($data['departamento_id']) ? (int) $data['departamento_id'] : null,
            localidadId: isset($data['localidad_id']) ? (int) $data['localidad_id'] : null,
            tramite: isset($data['tramite']) ? (string) $data['tramite'] : null,
            cuilPrefijo: isset($data['CUIL_prefijo']) ? (string) $data['CUIL_prefijo'] : null,
            cuilSufijo: isset($data['CUIL_sufijo']) ? (string) $data['CUIL_sufijo'] : null,
            viveSi: isset($data['vive_si']) ? (bool) $data['vive_si'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
        );
    }


    public function toArray(): array
    {
        $data = [
            'apellido' => $this->apellido,
            'nombre' => $this->nombre,
            'nombre_alternativo' => $this->nombreAlternativo,
            'sexo_id' => $this->sexoId,
            'genero_id' => $this->generoId,
            'documento_situacion_id' => $this->documentoSituacionId,
            'nacimiento_fecha' => $this->nacimientoFecha,
            'nacionalidad_nacion_id' => $this->nacionalidadNacionId,
            'nacion_id' => $this->nacionId,
            'provincia_id' => $this->provinciaId,
            'departamento_id' => $this->departamentoId,
            'localidad_id' => $this->localidadId,
            'tramite' => $this->tramite,
            'CUIL_prefijo' => $this->cuilPrefijo,
            'CUIL_sufijo' => $this->cuilSufijo,
            'vive_si' => $this->viveSi,
            'email' => $this->email,
        ];

        if ($this->documentoIdentidad !== null) {
            $data['documento_tipo_id'] = $this->documentoIdentidad->tipoId();
            $data['documento_numero'] = $this->documentoIdentidad->numero();
        }

        return array_filter($data, fn($val) => $val !== null);
    }

}
