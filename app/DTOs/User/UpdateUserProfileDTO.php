<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\ValueObjects\DocumentoIdentidad;
use Illuminate\Foundation\Http\FormRequest;

readonly class UpdateUserProfileDTO
{
    public ?int $documentoTipoId;
    public ?string $documentoNumero;
    public ?DocumentoIdentidad $documentoIdentidad;

    public function __construct(
        public ?string $nombre = null,
        public ?string $email = null,
        ?int $documentoTipoId = null,
        ?string $documentoNumero = null,
        ?DocumentoIdentidad $documentoIdentidad = null,
        public ?string $password = null,
    ) {
        if ($documentoIdentidad !== null) {
            $this->documentoIdentidad = $documentoIdentidad;
            $this->documentoTipoId = $documentoIdentidad->tipoId();
            $this->documentoNumero = $documentoIdentidad->numero();
        } else {
            $this->documentoTipoId = $documentoTipoId;
            $this->documentoNumero = $documentoNumero;
            if ($documentoTipoId !== null && $documentoNumero !== null && $documentoTipoId > 0 && $documentoNumero !== '') {
                $this->documentoIdentidad = new DocumentoIdentidad($documentoTipoId, $documentoNumero);
            } else {
                $this->documentoIdentidad = null;
            }
        }
    }

    /** Shortcut: tipo de documento como int. */
    public function documentoTipoId(): ?int
    {
        return $this->documentoTipoId;
    }

    /** Shortcut: número de documento como string crudo. */
    public function documentoNumero(): ?string
    {
        return $this->documentoNumero;
    }

    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: isset($data['nombre']) ? (string) $data['nombre'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            documentoTipoId: isset($data['documento_tipo_id']) ? (int) $data['documento_tipo_id'] : null,
            documentoNumero: isset($data['documento_numero']) ? (string) $data['documento_numero'] : null,
            password: isset($data['password']) && !empty($data['password']) ? (string) $data['password'] : null,
        );
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->nombre !== null) {
            $array['nombre'] = $this->nombre;
        }
        if ($this->email !== null) {
            $array['email'] = $this->email;
        }
        if ($this->documentoTipoId !== null) {
            $array['documento_tipo_id'] = $this->documentoTipoId;
        }
        if ($this->documentoNumero !== null) {
            $array['documento_numero'] = $this->documentoNumero;
        }
        if ($this->password !== null) {
            $array['password'] = $this->password;
        }

        return $array;
    }
}
