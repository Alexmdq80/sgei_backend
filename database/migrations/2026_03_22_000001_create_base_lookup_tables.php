<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $lookupTables = [
            'sexos' => ['nombre', 'letra', 'orden', 'vigente'],
            'generos' => ['nombre', 'orden', 'vigente'],
            'documento_tipos' => ['nombre', 'orden', 'vigente'],
            'documento_situacions' => ['nombre', 'orden', 'vigente'],
            'ambitos' => ['nombre', 'orden', 'vigente'],
            'dependencias' => ['nombre', 'orden', 'vigente'],
            'sectors' => ['nombre', 'orden', 'vigente'],
            'jornadas' => ['nombre', 'orden'],
            'turnos' => ['nombre', 'orden'],
            'nivels' => ['nombre', 'orden', 'vigente'],
            'modalidads' => ['nombre', 'orden', 'vigente'],
        ];

        foreach ($lookupTables as $table => $columns) {
            Schema::create($table, function (Blueprint $table) use ($columns) {
                // Usamos tinyint para tablas de referencia pequeñas, común en optimización de BD
                $table->tinyIncrements('id'); 
                foreach ($columns as $column) {
                    if ($column === 'orden') {
                        $table->integer($column)->nullable();
                    } elseif ($column === 'vigente') {
                        $table->boolean($column)->default(true);
                    } elseif ($column === 'letra') {
                        $table->string($column, 1)->nullable();
                    } else {
                        $table->string($column);
                    }
                }
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidads');
        Schema::dropIfExists('nivels');
        Schema::dropIfExists('turnos');
        Schema::dropIfExists('jornadas');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('dependencias');
        Schema::dropIfExists('ambitos');
        Schema::dropIfExists('documento_situacions');
        Schema::dropIfExists('documento_tipos');
        Schema::dropIfExists('generos');
        Schema::dropIfExists('sexos');
    }
};
