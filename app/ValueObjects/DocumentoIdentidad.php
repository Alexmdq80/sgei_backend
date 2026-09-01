<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

readonly class DocumentoIdentidad
{
       private const PATRONES_POR_TIPO = [
        1 => '/^\d{7,8}$/',   // DNI
        2 => '/^\d{7,8}$/',   // LC (Libreta Cívica)
        3 => '/^\d{7,8}$/',   // LE (Libreta de Enrolamiento)
        4 => '/^[A-Za-z0-9]{3,20}$/', // Pasaporte
        5 => '/^[A-Za-z0-9\-\s]{5,20}$/', // CPI
        6 => '/^[A-Za-z0-9\-\s]{3,20}$/', // Documento Extranjero
        7 => '/^IND-\d{6}$/',            // Indocumentado
        8 => '/^[A-Za-z0-9\-\s]{1,30}$/', // Otro
    ];

    public readonly int $tipoId;
    private readonly string $numero;

    public function __construct(
        int $tipoId,
        string $numero,
    ) {
        // 1. SIN property promotion → asignamos manualmente
        $this->tipoId = $tipoId;

        // 2. Sanitizamos ANTES de guardar en la propiedad
        $numero = $this->sanitizar($numero);

        // 3. Validamos
        $this->validar($tipoId, $numero);

        // 4. Guardamos el valor YA sanitizado
        $this->numero = $numero;
    }

    private function sanitizar(string $valor): string
    {
        return str_replace(['.', ' ', '-', '_', ','], '', trim($valor));
    }

    private function validar(int $tipoId, string $numero): void
    {
        // BUG 3 FIX: Lanzar excepción si el tipo no existe en el mapa
        if (!isset(self::PATRONES_POR_TIPO[$tipoId])) {
            throw new InvalidArgumentException(
                "El tipo de documento ID '{$tipoId}' no es válido. " .
                "Los tipos permitidos son: " . implode(', ', array_keys(self::PATRONES_POR_TIPO))
            );
        }

        $patron = self::PATRONES_POR_TIPO[$tipoId];

        if (!preg_match($patron, $numero)) {
            throw new InvalidArgumentException(
                "El número de documento '{$numero}' no es válido para el tipo de documento seleccionado (ID: {$tipoId})."
            );
        }
    }

    public function numero(): string
    {
        return $this->numero;
    }

    public function tipoId(): int
    {
        return $this->tipoId;
    }

    public function getFormatted(): string
    {
        // BUG 2 FIX: No formatear con puntos si no es numérico (ej: pasaporte)
        if (!ctype_digit($this->numero)) {
            return $this->numero;
        }

        $len = strlen($this->numero);

        if ($len === 7) {
            return substr($this->numero, 0, 1) . '.' .
                   substr($this->numero, 1, 3) . '.' .
                   substr($this->numero, 4, 3);
        }

        if ($len === 8) {
            return substr($this->numero, 0, 2) . '.' .
                   substr($this->numero, 2, 3) . '.' .
                   substr($this->numero, 5, 3);
        }

        return $this->numero;
    }

    public function equals(DocumentoIdentidad $other): bool
    {
        return $this->tipoId === $other->tipoId
            && $this->numero === $other->numero;
    }

    public function __toString(): string
    {
        return "{$this->tipoId}-{$this->numero}";
    }
}
