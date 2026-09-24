<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Http\Request;

/**
 * Normaliza el tamaño de página aceptando tanto `per_page` (canónico)
 * como `limit` (alias legacy del omnibox), con clamp anti-abuso.
 */
readonly class TamanoPagina
{
    public const DEFAULT = 15;

    private const MINIMO = 1;

    private const MAXIMO = 100;

    private function __construct(public int $valor) {}

    public static function fromRequest(Request $request, int $porDefecto = self::DEFAULT): self
    {
        // `?:` en vez de `??`: también cae al siguiente si llega "" o "0".
        $solicitado = (int) $request->input('per_page')
            ?: (int) $request->input('limit')
            ?: $porDefecto;

        return new self(max(self::MINIMO, min($solicitado, self::MAXIMO)));
    }
}
