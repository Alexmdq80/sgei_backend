<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use Illuminate\Http\Request;

/**
 * DTO para los filtros de búsqueda/listado de Personas.
 * Encapsula todos los parámetros de query posibles del index,
 * incluyendo filtros demográficos, geográficos y jurisdiccionales.
 */
readonly class PersonaFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $onlyAgents = null,
        public ?int $escuelaId = null,
        public ?int $provinciaId = null,
        public ?int $departamentoId = null,
        public ?int $localidadId = null,
        public ?int $nacionalidadNacionId = null,
        public ?int $sexoId = null,
        public ?int $generoId = null,
        public ?int $documentoTipoId = null,
        public ?bool $hasUser = null,
        public ?int $perPage = null,
        public ?string $sortBy = null,
        public ?string $order = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->filled('search') ? (string) $request->search : null,
            onlyAgents: $request->has('only_agents') ? $request->boolean('only_agents') : null,
            escuelaId: $request->filled('escuela_id') ? (int) $request->escuela_id : null,
            provinciaId: $request->filled('provincia_id') ? (int) $request->provincia_id : null,
            departamentoId: $request->filled('departamento_id') ? (int) $request->departamento_id : null,
            localidadId: $request->filled('localidad_id') ? (int) $request->localidad_id : null,
            nacionalidadNacionId: $request->filled('nacionalidad_nacion_id') ? (int) $request->nacionalidad_nacion_id : null,
            sexoId: $request->filled('sexo_id') ? (int) $request->sexo_id : null,
            generoId: $request->filled('genero_id') ? (int) $request->genero_id : null,
            documentoTipoId: $request->filled('documento_tipo_id') ? (int) $request->documento_tipo_id : null,
            hasUser: $request->has('has_user') ? $request->boolean('has_user') : null,
            perPage: $request->filled('per_page') ? (int) $request->per_page : null,
            sortBy: $request->filled('sort_by') ? (string) $request->sort_by : null,
            order: $request->filled('order') ? (string) $request->order : null,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            search: !empty($data['search']) ? (string) $data['search'] : null,
            onlyAgents: isset($data['only_agents']) ? (bool) $data['only_agents'] : null,
            escuelaId: isset($data['escuela_id']) ? (int) $data['escuela_id'] : null,
            provinciaId: isset($data['provincia_id']) ? (int) $data['provincia_id'] : null,
            departamentoId: isset($data['departamento_id']) ? (int) $data['departamento_id'] : null,
            localidadId: isset($data['localidad_id']) ? (int) $data['localidad_id'] : null,
            nacionalidadNacionId: isset($data['nacionalidad_nacion_id']) ? (int) $data['nacionalidad_nacion_id'] : null,
            sexoId: isset($data['sexo_id']) ? (int) $data['sexo_id'] : null,
            generoId: isset($data['genero_id']) ? (int) $data['genero_id'] : null,
            documentoTipoId: isset($data['documento_tipo_id']) ? (int) $data['documento_tipo_id'] : null,
            hasUser: isset($data['has_user']) ? (bool) $data['has_user'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : null,
            sortBy: isset($data['sort_by']) ? (string) $data['sort_by'] : null,
            order: isset($data['order']) ? (string) $data['order'] : null,
        );
    }
}
