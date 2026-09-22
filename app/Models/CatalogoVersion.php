<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Metadatos de versionado de los catálogos maestros.
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogoVersion query()
 *
 * @mixin \Eloquent
 */
class CatalogoVersion extends Model
{
    /**
     * Tablas de catálogos maestros versionadas (fuente de verdad del versionado).
     *
     * @var array<int, string>
     */
    public const TABLAS = [
        'nacions',
        'provincias',
        'regions',
        'departamentos',
        'localidads',
        'documento_tipos',
        'documento_situacions',
        'sexos',
        'generos',
        'calles',
    ];

    /**
     * Clave primaria string (nombre de la tabla versionada).
     *
     * @var string
     */
    protected $primaryKey = 'tabla';

    /**
     * La PK no es autoincremental.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipo de la PK.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atributos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tabla', 'version'];

    /**
     * Incrementa la versión de un catálogo en UNA sola sentencia SQL (upsert).
     */
    public static function touchVersion(string $tabla): void
    {
        static::query()->upsert(
            [
                [
                    'tabla' => $tabla,
                    'version' => now()->format('Y-m-d H:i:s.u'),
                ],
            ],
            ['tabla'],   // columna de conflicto (PK)
            ['version']  // columnas a actualizar en caso de conflicto
        );
    }

    /**
     * Devuelve la versión almacenada de una tabla (con fallback compatible).
     */
    public static function versionDe(string $tabla): string
    {
        return (string) (static::query()->whereKey($tabla)->value('version') ?? 'v1');
    }
}
