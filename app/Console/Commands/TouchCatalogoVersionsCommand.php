<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CatalogoVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class TouchCatalogoVersionsCommand extends Command
{
    /**
     * Nombre y firma del comando.
     *
     * @var string
     */
    protected $signature = 'catalogos:touch-versions
                            {--tabla=* : Tabla(s) real(es) a versionar (ej. localidads). Por defecto: todas las versionadas}
                            {--dry-run : Muestra el resultado sin escribir en la base de datos}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Refresca la versión de los catálogos maestros para invalidar la caché del frontend';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! Schema::hasTable('catalogo_versions')) {
            $this->error('La tabla catalogo_versions no existe. Ejecutá: php artisan migrate');

            return self::FAILURE;
        }

        $solicitadas = (array) $this->option('tabla');
        $tablas = $solicitadas === [] ? $this->tablasVersionadas() : array_values(array_unique($solicitadas));
        $dryRun = (bool) $this->option('dry-run');

        if ($tablas === []) {
            $this->warn('No hay tablas versionadas para procesar.');

            return self::SUCCESS;
        }

        $filas = [];
        $versionadas = 0;

        foreach ($tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                $this->warn("Se omite '{$tabla}': la tabla no existe en esta base de datos.");

                continue;
            }

            $anterior = CatalogoVersion::versionDe($tabla);

            if ($dryRun) {
                $nueva = '(sin cambios)';
            } else {
                CatalogoVersion::touchVersion($tabla);
                $nueva = CatalogoVersion::versionDe($tabla);
                $versionadas++;
            }

            $filas[] = [$tabla, $anterior, $nueva];
        }

        if ($filas === []) {
            $this->warn('No se versionó ninguna tabla.');

            return self::SUCCESS;
        }

        $this->table(['Tabla', 'Versión anterior', 'Versión nueva'], $filas);

        $this->info($dryRun
            ? sprintf('Dry-run: se habrían versionado %d tabla(s).', count($filas))
            : sprintf('Listo: %d catálogo(s) versionado(s).', $versionadas));

        return self::SUCCESS;
    }

    /**
     * Tablas versionadas: la constante del modelo más las que ya tengan fila registrada.
     *
     * @return array<int, string>
     */
    private function tablasVersionadas(): array
    {
        return array_values(array_unique(array_merge(
            CatalogoVersion::TABLAS,
            CatalogoVersion::query()->pluck('tabla')->all()
        )));
    }
}
