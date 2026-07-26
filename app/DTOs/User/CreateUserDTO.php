<?php

namespace App\DTOs\User;

use Illuminate\Foundation\Http\FormRequest;

readonly class CreateUserDTO
{
    public function __construct(
        public string $nombre,
        public string $email,
        public int $documentoTipoId,
        public string $documentoNumero,
        public ?string $password = null,
        public bool $esAdministrador = false,
        public ?string $estado = null,
        public ?string $verificationToken = null,
        public mixed $verificationTokenCreatedAt = null,
    ) {}

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
            documentoTipoId: (int) ($data['documento_tipo_id'] ?? 0),
            documentoNumero: (string) ($data['documento_numero'] ?? ''),
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
            'documento_tipo_id' => $this->documentoTipoId,
            'documento_numero' => $this->documentoNumero,
            'es_administrador' => $this->esAdministrador,
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
