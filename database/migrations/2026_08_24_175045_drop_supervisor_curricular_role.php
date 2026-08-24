<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Quita asignaciones de permiso, luego el rol supervisor_curricular (si existe)
        Permission::where('name', 'supervisor_curricular')->delete();
        Role::where('name', 'supervisor_curricular')->delete();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
