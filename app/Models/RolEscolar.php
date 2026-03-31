<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RolEscolar extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $auditGroup = "entities";

    protected $table = 'roles_escolares';

    protected $fillable = [
        'nombre',
        'orden',
        'vigente'
    ];

    /**
     * Relationship to users associated with this school role.
     */
    public function escuelaUsuarios(): HasMany
    {
        return $this->hasMany(EscuelaUsuario::class, 'rol_escolar_id');
    }
}
