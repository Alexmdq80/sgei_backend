<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\CatalogoVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Mantiene sincronizada `catalogo_versions` con los cambios reales de un catálogo:
 * altas, modificaciones, bajas lógicas, bajas físicas y restauraciones.
 */
trait ActualizaVersionCatalogo
{
    /**
     * Caché de disponibilidad de la tabla de versions (una copia por clase que usa el trait).
     */
    protected static ?bool $catalogoVersionsDisponible = null;

    /**
     * Registra los listeners de Eloquent que incrementan la versión del catálogo.
     */
    protected static function bootActualizaVersionCatalogo(): void
    {
        // Altas y modificaciones (modelo sin cambios reales => no invalida la caché del frontend).
        static::saved(static function (Model $model): void {
            if (! $model->wasRecentlyCreated && ! $model->wasChanged()) {
                return;
            }

            static::versionarCatalogo($model->getTable());
        });

        // Bajas: soft delete y también hard delete (forceDelete dispara `deleted`).
        static::deleted(static function (Model $model): void {
            static::versionarCatalogo($model->getTable());
        });

        // Un restore vuelve a exponer el registro: el catálogo cambió.
        static::restored(static function (Model $model): void {
            static::versionarCatalogo($model->getTable());
        });
    }

    /**
     * Incrementa la versión de la tabla indicada si la tabla de versions existe.
     */
    protected static function versionarCatalogo(string $tabla): void
    {
        if (! static::catalogoVersionsDisponible()) {
            return;
        }

        CatalogoVersion::touchVersion($tabla);
    }

    /**
     * Verifica una sola vez por proceso y por clase que `catalogo_versions` exista.
     */
    protected static function catalogoVersionsDisponible(): bool
    {
        if (static::$catalogoVersionsDisponible === null) {
            static::$catalogoVersionsDisponible = Schema::hasTable('catalogo_versions');
        }

        return static::$catalogoVersionsDisponible;
    }
}
