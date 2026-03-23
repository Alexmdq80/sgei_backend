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
        $georefTables = [
            'georef_fuentes' => ['nombre', 'orden', 'vigente'],
            'georef_categorias' => ['nombre', 'orden', 'vigente'],
            'georef_funcions' => ['nombre', 'orden', 'vigente'],
        ];

        foreach ($georefTables as $table => $columns) {
            Schema::create($table, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    if ($column === 'orden') {
                        $table->integer($column)->nullable();
                    } elseif ($column === 'vigente') {
                        $table->boolean($column)->default(true);
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
        Schema::dropIfExists('georef_funcions');
        Schema::dropIfExists('georef_categorias');
        Schema::dropIfExists('georef_fuentes');
    }
};
