<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escuela extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "localidad_id",
        "ambito_id",
        "dependencia_id",
        "sector_id",
        "cue_anexo",
        "clave_provincial",
        "nombre",
        "numero",
        "codigo_localidad",
        "domicilio",
        "telefono",
        "email",
        "codigo_postal"
    ];

    /**
     * Relationship to the locality.
     */
    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }

    /**
     * Relationship to the ambit.
     */
    public function ambito(): BelongsTo
    {
        return $this->belongsTo(Ambito::class);
    }

    /**
     * Relationship to the dependency.
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class);
    }

    /**
     * Relationship to the sector.
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Relationship to the registration origin records.
     */
    public function inscripcionProcedencias(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    /**
     * Relationship to the transfer registration records.
     */
    public function inscripcionPases(): HasMany
    {
        return $this->hasMany(InscripcionPase::class);
    }

    /**
     * Relationship to the students files (Legajos).
     */
    public function legajos(): HasMany
    {
        return $this->hasMany(Legajo::class);
    }

    /**
     * Relationship to the institutional proposals.
     */
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class);
    }

    /**
     * Relationship to the users associated with the school.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class)
                    ->withPivot(['usuario_tipo_id', 'verified_at']);
    }

    /**
     * Relationship to the modalities and levels associated with the school.
     */
    public function modalidadesNiveles(): BelongsToMany
    {
        return $this->belongsToMany(ModalidadNivel::class)
                    ->using(EscuelaModalidadNivel::class);
    }

    /**
     * Relationship to the offers associated with the school.
     */
    public function ofertas(): BelongsToMany
    {
        return $this->belongsToMany(Oferta::class);
    }
}
