<?php

declare(strict_types=1);

namespace App\DTOs\Persona;

use Illuminate\Http\Request;

readonly class PersonaDomicilioDTO
{
    public function __construct(
        public ?int $localidadId = null,
        public ?int $calleId = null,
        public ?int $calleEntre1Id = null,
        public ?int $calleEntre2Id = null,
        public ?string $numero = null,
        public ?string $piso = null,
        public ?string $departamento = null,
        public ?string $torre = null,
        public ?string $codigoPostal = null,
        public ?string $otros = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            localidadId: $request->filled('localidad_id') ? (int) $request->localidad_id : null,
            calleId: $request->filled('calle_id') ? (int) $request->calle_id : null,
            calleEntre1Id: $request->filled('calle_entre_1_id') ? (int) $request->calle_entre_1_id : null,
            calleEntre2Id: $request->filled('calle_entre_2_id') ? (int) $request->calle_entre_2_id : null,
            numero: $request->filled('numero') ? (string) $request->numero : null,
            piso: $request->filled('piso') ? (string) $request->piso : null,
            departamento: $request->filled('departamento') ? (string) $request->departamento : null,
            torre: $request->filled('torre') ? (string) $request->torre : null,
            codigoPostal: $request->filled('codigo_postal') ? (string) $request->codigo_postal : null,
            otros: $request->filled('otros') ? (string) $request->otros : null,
        );
    }

    public function toArray(): array
    {
        return $this->filtrarNulos([
            'localidad_id' => $this->localidadId,
            'calle_id' => $this->calleId,
            'calle_entre_1_id' => $this->calleEntre1Id,
            'calle_entre_2_id' => $this->calleEntre2Id,
            'numero' => $this->numero,
            'piso' => $this->piso,
            'departamento' => $this->departamento,
            'torre' => $this->torre,
            'codigo_postal' => $this->codigoPostal,
            'otros' => $this->otros,
        ]);
    }

    /** Descarta claves null para NO pisar valores previos en la BD. */
    private function filtrarNulos(array $datos): array
    {
        return array_filter($datos, fn ($valor) => $valor !== null);
    }
}
