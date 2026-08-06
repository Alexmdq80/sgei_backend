<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\ValueObjects\DocumentoIdentidad;
use Illuminate\Foundation\Http\FormRequest;

readonly class CreateUserDTO
{
    public int $documentoTipoId;
    public string $documentoNumero;

    public function __construct(
        public string $nombre,
        public string $email,
        public DocumentoIdentidad $documentoIdentidad,
        public ?string $password = null,
        public bool $esAdministrador = false,
        public ?string $estado = null,
        public ?string $verificationToken = null,
        public mixed $verificationTokenCreatedAt = null,
    ) {
        $this->documentoTipoId = $this->documentoIdentidad->tipoId();
        $this->documentoNumero = $this->documentoIdentidad->numero();
    }

    /** Shortcut: tipo de documento como int (delegado al VO). */
    public function documentoTipoId(): int
    {
        return $this->documentoIdentidad->tipoId();
    }

    /** Shortcut: número de documento como string crudo (delegado al VO). */
    public function documentoNumero(): string
    {
        return $this->documentoIdentidad->numero();
    }

    public static function fromRequest(FormRequest $request, array $overrides = []): self
    {
        $data = array_merge($request->validated(), $overrides);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: (string) ($data['nombre'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            documentoIdentidad: new DocumentoIdentidad(
                tipoId: (int) ($data['documento_tipo_id'] ?? 0),
                numero: (string) ($data['documento_numero'] ?? ''),
            ),
            password: isset($data['password']) ? (string) $data['password'] : null,
            esAdministrador: (bool) ($data['es_administrador'] ?? false),
            estado: isset($data['estado']) ? (string) $data['estado'] : null,
            verificationToken: isset($data['verification_token']) ? (string) $data['verification_token'] : null,
            verificationTokenCreatedAt: $data['verification_token_created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        $array = [
            'nombre' => $this->nombre,
            'email' => $this->email,
            // Extraemos el string crudo del VO — nunca pasamos el objeto a Eloquent
            'documento_tipo_id' => $this->documentoIdentidad->tipoId(),
            'documento_numero'  => $this->documentoIdentidad->numero(),
            'es_administrador'  => $this->esAdministrador,
        ];

        if ($this->password !== null) {
            $array['password'] = $this->password;
        }

        if ($this->estado !== null) {
            $array['estado'] = $this->estado;
        }

        if ($this->verificationToken !== null) {
            $array['verification_token'] = $this->verificationToken;
        }

        if ($this->verificationTokenCreatedAt !== null) {
            $array['verification_token_created_at'] = $this->verificationTokenCreatedAt;
        }

        return $array;
    }
}
