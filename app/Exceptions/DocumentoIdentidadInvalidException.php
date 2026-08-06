<?php

declare(strict_types=1);

namespace App\Exceptions;

use InvalidArgumentException;

class DocumentoIdentidadInvalidException extends InvalidArgumentException
{
    public function __construct(
        string $numero,
        int $tipoId,
        string $detalle = ''
    ) {
        $mensaje = "El documento (tipo ID: {$tipoId}, número: '{$numero}') no es válido.";
        if ($detalle) {
            $mensaje .= " {$detalle}";
        }
        parent::__construct($mensaje, 422);
    }
}
