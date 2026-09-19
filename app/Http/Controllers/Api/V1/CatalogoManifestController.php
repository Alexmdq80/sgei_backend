<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CatalogoVersion;
use Illuminate\Http\JsonResponse;

class CatalogoManifestController extends Controller
{
    /**
     * Mapa: clave pública del manifiesto => tabla real del catálogo.
     *
     * Las claves DEBEN coincidir con las usadas por el frontend
     * (fe/src/context/AuthContext.jsx -> catalogCache.syncWithManifest()).
     *
     * @var array<string, string>
     */
    private const CATALOGOS = [
        'naciones' => 'nacions',
        'provincias' => 'provincias',
        'regiones' => 'regions',
        'departamentos' => 'departamentos',
        'localidades' => 'localidads',
        'documento_tipos' => 'documento_tipos',
        'documento_situacions' => 'documento_situacions',
        'sexos' => 'sexos',
        'generos' => 'generos',
    ];

    /**
     * Devuelve la versión de cada catálogo de referencia resuelta en UNA sola consulta SQL.
     * Respuesta ultraliviana (~300 bytes).
     */
    public function manifest(): JsonResponse
    {
        $versiones = CatalogoVersion::pluck('version', 'tabla');

        return response()->json(
            array_map(
                static fn (string $tabla): string => (string) ($versiones[$tabla] ?? 'v1'),
                self::CATALOGOS
            )
        );
    }
}
