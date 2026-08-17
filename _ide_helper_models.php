<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $persona_id
 * @property string|null $legajo
 * @property \Illuminate\Support\Carbon|null $fecha_ingreso_sistema
 * @property string $estado_administrativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereEstadoAdministrativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereFechaIngresoSistema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereLegajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agente withoutTrashed()
 */
	class Agente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ambito withoutTrashed()
 */
	class Ambito extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int|null $anio_absoluto
 * @property int|null $anio_relativo
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnioPlan> $planAnios
 * @property-read int|null $plan_anios_count
 * @method static \Database\Factories\AnioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereAnioAbsoluto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereAnioRelativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anio withoutTrashed()
 */
	class Anio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $plan_id
 * @property int $anio_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Anio|null $anio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignatura> $asignaturas
 * @property-read int|null $asignaturas_count
 * @property-read \App\Models\Plan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Database\Factories\AnioPlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereAnioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnioPlan withoutTrashed()
 */
	class AnioPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int $anio_plan_id
 * @property int $horas_semanales
 * @property string|null $codigo
 * @property int $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnioPlan|null $anioPlan
 * @method static \Database\Factories\AsignaturaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereAnioPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereHorasSemanales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asignatura withoutTrashed()
 */
	class Asignatura extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $auditable_type
 * @property string|null $auditable_id
 * @property string $event
 * @property string|null $attempted_email
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $tags
 * @property array<array-key, mixed>|null $details
 * @property string $audit_driver
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAttemptedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationAudit withoutTrashed()
 */
	class AuthenticationAudit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property string $nombre
 * @property int|null $altura_fin_derecha
 * @property int|null $altura_fin_izquierda
 * @property int|null $altura_inicio_derecha
 * @property int|null $altura_inicio_izquierda
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioCalles
 * @property-read int|null $domicilio_calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioEntreCalles1
 * @property-read int|null $domicilio_entre_calles1_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilioEntreCalles2
 * @property-read int|null $domicilio_entre_calles2_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaFinDerecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaFinIzquierda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaInicioDerecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereAlturaInicioIzquierda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calle withoutTrashed()
 */
	class Calle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string $tipo
 * @property int|null $escalafon_id
 * @property bool $requiere_cursos
 * @property bool $activo
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Escalafon|null $escalafon
 * @method static \Database\Factories\CargoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereEscalafonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereRequiereCursos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo withoutTrashed()
 */
	class Cargo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInfoInscripcion> $historialInfoInscripciones
 * @property-read int|null $historial_info_inscripciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CierreCausa withoutTrashed()
 */
	class CierreCausa extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionFinalizado> $inscripcionFinalizados
 * @property-read int|null $inscripcion_finalizados_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Condicion withoutTrashed()
 */
	class Condicion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $persona_id
 * @property string|null $telefono_codigo_area
 * @property string|null $telefono
 * @property string|null $celular_codigo_area
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCelular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCelularCodigoArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereTelefonoCodigoArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacto withoutTrashed()
 */
	class Contacto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nacion> $naciones
 * @property-read int|null $naciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Continente withoutTrashed()
 */
	class Continente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $codigo_cupof
 * @property int $escuela_id
 * @property int|null $asignatura_id
 * @property int $escalafon_id
 * @property int $puesto_tipo_id
 * @property string|null $nombre_cargo
 * @property int $cantidad
 * @property string $estado_cupof
 * @property string|null $motivo_baja
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Asignatura|null $asignatura
 * @property-read \App\Models\Escalafon|null $escalafon
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\CupofMovimiento|null $movimientoActivo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientos
 * @property-read int|null $movimientos_count
 * @property-read \App\Models\PuestoTipo|null $puestoTipo
 * @method static \Database\Factories\CupofFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereAsignaturaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCantidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCodigoCupof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEscalafonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereEstadoCupof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereMotivoBaja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereNombreCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof wherePuestoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cupof withoutTrashed()
 */
	class Cupof extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cupof_id
 * @property int $persona_id
 * @property string $situacion_revista
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property string|null $resolucion
 * @property bool $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Cupof|null $cupof
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereCupofId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereResolucion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereSituacionRevista($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CupofMovimiento withoutTrashed()
 */
	class CupofMovimiento extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $provincia_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $provincia_interseccion
 * @property int|null $region_id
 * @property int|null $distrito_numero
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $georefAsentamientos
 * @property-read int|null $georef_asentamientos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefLocalidad> $georefLocalidades
 * @property-read int|null $georef_localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \App\Models\Provincia|null $provincia
 * @property-read \App\Models\Region|null $region
 * @method static \Database\Factories\DepartamentoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereDistritoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereProvinciaInterseccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withoutTrashed()
 */
	class Departamento extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dependencia withoutTrashed()
 */
	class Dependencia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $usuario_id
 * @property int $departamento_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read \App\Models\Departamento|null $distrito
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistritoUsuario withoutTrashed()
 */
	class DistritoUsuario extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoSituacion withoutTrashed()
 */
	class DocumentoSituacion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Database\Factories\DocumentoTipoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentoTipo withoutTrashed()
 */
	class DocumentoTipo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $persona_id
 * @property int|null $localidad_id
 * @property int|null $calle_id
 * @property int|null $calle_entre_1_id
 * @property int|null $calle_entre_2_id
 * @property string|null $numero
 * @property string|null $piso
 * @property string|null $torre
 * @property string|null $departamento
 * @property string|null $otros
 * @property string|null $codigo_postal
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Calle|null $calle
 * @property-read \App\Models\Calle|null $entreCalle1
 * @property-read \App\Models\Calle|null $entreCalle2
 * @property-read \App\Models\Localidad|null $localidad
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleEntre1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleEntre2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCalleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereDepartamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereOtros($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio wherePiso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereTorre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domicilio withoutTrashed()
 */
	class Domicilio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cupof> $cupofs
 * @property-read int|null $cupofs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escalafon withoutTrashed()
 */
	class Escalafon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $localidad_id
 * @property int|null $ambito_id
 * @property int|null $dependencia_id
 * @property int|null $sector_id
 * @property string|null $cue_anexo
 * @property string|null $clave_provincial
 * @property string $nombre
 * @property string|null $numero
 * @property string|null $codigo_localidad
 * @property string|null $domicilio
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $codigo_postal
 * @property int $modalidad_comun
 * @property int $modalidad_especial
 * @property int $modalidad_adultos
 * @property int $comun_inicial_maternal
 * @property int $comun_inicial_infantes
 * @property int $comun_primario
 * @property int $comun_secundario
 * @property int $comun_secundario_inet
 * @property int $comun_snu
 * @property int $comun_snu_inet
 * @property int $comun_snu_cursos
 * @property int $especial_temprana
 * @property int $especial_inicial
 * @property int $especial_primario
 * @property int $especial_secundario
 * @property int $especial_integracion
 * @property int $adultos_primario
 * @property int $adultos_secundario
 * @property int $adultos_profesional
 * @property int $adultos_profesional_inet
 * @property int $adultos_alfabetizacion
 * @property int $hospitalario_inicial
 * @property int $hospitalario_primario
 * @property int $hospitalario_secundario
 * @property int $talleres_artistica
 * @property int $servicios_complementarios
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ambito|null $ambito
 * @property-read \App\Models\Dependencia|null $dependencia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EscuelaPersona> $escuelasPersonas
 * @property-read int|null $escuelas_personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripcionProcedencias
 * @property-read int|null $inscripcion_procedencias_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Legajo> $legajos
 * @property-read int|null $legajos_count
 * @property-read \App\Models\Localidad|null $localidad
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModalidadNivel> $modalidadesNiveles
 * @property-read int|null $modalidades_niveles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Oferta> $ofertas
 * @property-read int|null $ofertas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @property-read \App\Models\Sector|null $sector
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Usuario> $usuarios
 * @property-read int|null $usuarios_count
 * @method static \Database\Factories\EscuelaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosAlfabetizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosProfesional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosProfesionalInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAdultosSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereAmbitoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereClaveProvincial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunInicialInfantes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunInicialMaternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSecundarioInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnuCursos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereComunSnuInet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereCueAnexo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDependenciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereDomicilio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialInicial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialIntegracion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereEspecialTemprana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioInicial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioPrimario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereHospitalarioSecundario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadAdultos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadComun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereModalidadEspecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereSectorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereServiciosComplementarios($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereTalleresArtistica($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Escuela withoutTrashed()
 */
	class Escuela extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $escuela_id
 * @property int $modalidad_nivel_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\ModalidadNivel|null $modalidadNivel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereModalidadNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaModalidadNivel withoutTrashed()
 */
	class EscuelaModalidadNivel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Oferta|null $oferta
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaOferta withoutTrashed()
 */
	class EscuelaOferta extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property int $escuela_id
 * @property int $persona_id
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Persona|null $persona
 * @property-read \Spatie\Permission\Models\Role|null $role
 * @method static \Database\Factories\EscuelaPersonaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaPersona withoutTrashed()
 */
	class EscuelaPersona extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModalidadNivel> $modalidadesNiveles
 * @property-read int|null $modalidades_niveles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaTipo withoutTrashed()
 */
	class EscuelaTipo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EscuelaUbicacion withoutTrashed()
 */
	class EscuelaUbicacion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $propuesta_id
 * @property int $seccion_tipo_id
 * @property string|null $division
 * @property string|null $division_nombre
 * @property string|null $nombre
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read \App\Models\Propuesta|null $propuesta
 * @property-read \App\Models\SeccionTipo|null $seccionTipo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereDivisionNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio wherePropuestaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereSeccionTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Espacio withoutTrashed()
 */
	class Espacio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero withoutTrashed()
 */
	class Genero extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $departamento_id
 * @property int|null $municipio_id
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departamento|null $departamento
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @property-read \App\Models\Municipio|null $municipio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefAsentamiento withoutTrashed()
 */
	class GeorefAsentamiento extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $asentamientos
 * @property-read int|null $asentamientos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calle> $calles
 * @property-read int|null $calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalidadCensal> $localidadesCensales
 * @property-read int|null $localidades_censales_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Provincia> $provincias
 * @property-read int|null $provincias_count
 * @method static \Database\Factories\GeorefCategoriaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefCategoria withoutTrashed()
 */
	class GeorefCategoria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $asentamientos
 * @property-read int|null $asentamientos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calle> $calles
 * @property-read int|null $calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalidadCensal> $localidadesCensales
 * @property-read int|null $localidades_censales_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Provincia> $provincias
 * @property-read int|null $provincias_count
 * @method static \Database\Factories\GeorefFuenteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuente withoutTrashed()
 */
	class GeorefFuente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalidadCensal> $localidadesCensales
 * @property-read int|null $localidades_censales_count
 * @method static \Database\Factories\GeorefFuncionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefFuncion withoutTrashed()
 */
	class GeorefFuncion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $departamento_id
 * @property int|null $municipio_id
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departamento|null $departamento
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @property-read \App\Models\Municipio|null $municipio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeorefLocalidad withoutTrashed()
 */
	class GeorefLocalidad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $cierre_causa_id
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $observaciones
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CierreCausa|null $cierreCausa
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCierreCausaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereObservaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInfoInscripcion withoutTrashed()
 */
	class HistorialInfoInscripcion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $inscripcion_id
 * @property int $persona_id
 * @property int|null $persona_firma_id
 * @property int|null $espacio_id
 * @property int|null $escuela_id
 * @property int|null $nivel_id
 * @property int|null $modalidad_id
 * @property int|null $condicion_id
 * @property int|null $persona_vinculo_persona_1_id
 * @property int|null $persona_vinculo_persona_2_id
 * @property int|null $persona_vinculo_persona_3_id
 * @property string|null $codigo_abc
 * @property int $proyecto_inclusion_si
 * @property int $concurre_especial_si
 * @property int $asistente_externo_si
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InscripcionBaja|null $baja
 * @property-read \App\Models\Condicion|null $condicion
 * @property-read \App\Models\Escuela|null $escuelaProcedencia
 * @property-read \App\Models\Espacio|null $espacio
 * @property-read \App\Models\InscripcionFinalizado|null $finalizado
 * @property-read \App\Models\HistorialInfoInscripcion|null $info
 * @property-read \App\Models\Inscripcion|null $inscripcion
 * @property-read \App\Models\Modalidad|null $modalidadProcedencia
 * @property-read \App\Models\Nivel|null $nivelProcedencia
 * @property-read \App\Models\InscripcionPase|null $pase
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\Persona|null $personaFirma
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_1
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_2
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_3
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereAsistenteExternoSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCodigoAbc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereConcurreEspecialSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaFirmaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion wherePersonaVinculoPersona3Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereProyectoInclusionSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialInscripcion withoutTrashed()
 */
	class HistorialInscripcion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property int $persona_id
 * @property int|null $persona_firma_id
 * @property int|null $espacio_id
 * @property int|null $escuela_id
 * @property int|null $nivel_id
 * @property int|null $modalidad_id
 * @property int|null $condicion_id
 * @property int|null $persona_vinculo_persona_1_id
 * @property int|null $persona_vinculo_persona_2_id
 * @property int|null $persona_vinculo_persona_3_id
 * @property string|null $codigo_abc
 * @property int $proyecto_inclusion_si
 * @property int $concurre_especial_si
 * @property int $asistente_externo_si
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Condicion|null $condicion
 * @property-read \App\Models\Escuela|null $escuelaProcedencia
 * @property-read \App\Models\Espacio|null $espacio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historial
 * @property-read int|null $historial_count
 * @property-read \App\Models\Modalidad|null $modalidadProcedencia
 * @property-read \App\Models\Nivel|null $nivelProcedencia
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\Persona|null $personaFirma
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_1
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_2
 * @property-read \App\Models\PersonaVinculoPersona|null $vinculoPersona_3
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereAsistenteExternoSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCodigoAbc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereConcurreEspecialSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaFirmaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion wherePersonaVinculoPersona3Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereProyectoInclusionSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inscripcion withoutTrashed()
 */
	class Inscripcion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $salida_motivo_id
 * @property string|null $otro_motivo
 * @property int $accion_contacto
 * @property int $accion_prevencion
 * @property int $accion_equipo
 * @property int $accion_otros
 * @property int $accion_ninguna
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @property-read \App\Models\SalidaMotivo|null $salidaMotivo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionContacto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionEquipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionNinguna($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionOtros($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereAccionPrevencion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereOtroMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereSalidaMotivoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionBaja withoutTrashed()
 */
	class InscripcionBaja extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historial_inscripcion_id
 * @property int|null $condicion_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Condicion|null $condicionFinalizacion
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCondicionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionFinalizado withoutTrashed()
 */
	class InscripcionFinalizado extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $escuela_id
 * @property int $historial_inscripcion_id
 * @property int|null $salida_motivo_id
 * @property int|null $escuela_ubicacion_id
 * @property string|null $otro_motivo
 * @property int $finalizado
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\EscuelaUbicacion|null $escuelaUbicacion
 * @property-read \App\Models\HistorialInscripcion|null $historialInscripcion
 * @property-read \App\Models\SalidaMotivo|null $salidaMotivo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereEscuelaUbicacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereFinalizado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereHistorialInscripcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereOtroMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereSalidaMotivoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InscripcionPase withoutTrashed()
 */
	class InscripcionPase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jornada withoutTrashed()
 */
	class Jornada extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $anio
 * @property int|null $orden
 * @property int $cerrado
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestas
 * @property-read int|null $propuestas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereAnio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCerrado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lectivo withoutTrashed()
 */
	class Lectivo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $persona_id
 * @property int $escuela_id
 * @property string|null $libro
 * @property string|null $folio
 * @property string|null $legajo
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \App\Models\Persona|null $persona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereFolio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereLegajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereLibro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo wherePersonaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legajo withoutTrashed()
 */
	class Legajo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $departamento_id
 * @property int|null $municipio_id
 * @property int|null $localidad_censal_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departamento|null $departamento
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domicilio> $domicilios
 * @property-read int|null $domicilios_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\LocalidadCensal|null $localidadCensal
 * @property-read \App\Models\Municipio|null $municipio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personasNacidas
 * @property-read int|null $personas_nacidas_count
 * @method static \Database\Factories\LocalidadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereLocalidadCensalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereMunicipioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localidad withoutTrashed()
 */
	class Localidad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property int|null $georef_funcion_id
 * @property string $nombre
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calle> $calles
 * @property-read int|null $calles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefAsentamiento> $georefAsentamientos
 * @property-read int|null $georef_asentamientos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\GeorefFuncion|null $georefFuncion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeorefLocalidad> $georefLocalidades
 * @property-read int|null $georef_localidades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Localidad> $localidades
 * @property-read int|null $localidades_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereGeorefFuncionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalidadCensal withoutTrashed()
 */
	class LocalidadCensal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read \App\Models\ModalidadNivel|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nivel> $niveles
 * @property-read int|null $niveles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withoutTrashed()
 */
	class Modalidad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $nivel_id
 * @property int $modalidad_id
 * @property int|null $escuela_tipo_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EscuelaTipo|null $escuelaTipo
 * @property-read \App\Models\EscuelaModalidadNivel|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @property-read \App\Models\Modalidad|null $modalidad
 * @property-read \App\Models\Nivel|null $nivel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereEscuelaTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereNivelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModalidadNivel withoutTrashed()
 */
	class ModalidadNivel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $provincia_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $provincia_interseccion
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \App\Models\Provincia|null $provincia
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereProvinciaInterseccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withoutTrashed()
 */
	class Municipio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $continente_id
 * @property string $nombre
 * @property string|null $nacionalidad
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Continente|null $continente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $nacionalidadPersonas
 * @property-read int|null $nacionalidad_personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Provincia> $provincias
 * @property-read int|null $provincias_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereContinenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereNacionalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nacion withoutTrashed()
 */
	class Nacion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 * @property-read int|null $inscripciones_count
 * @property-read \App\Models\ModalidadNivel|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modalidad> $modalidades
 * @property-read int|null $modalidades_count
 * @method static \Database\Factories\NivelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nivel withoutTrashed()
 */
	class Nivel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Oferta withoutTrashed()
 */
	class Oferta extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $usuario_id
 * @property int|null $documento_tipo_id
 * @property int|null $documento_situacion_id
 * @property int|null $sexo_id
 * @property int|null $genero_id
 * @property int|null $nacionalidad_nacion_id
 * @property int|null $nacion_id
 * @property int|null $provincia_id
 * @property int|null $departamento_id
 * @property int|null $localidad_id
 * @property \App\ValueObjects\DocumentoIdentidad|null $documento_numero
 * @property string|null $apellido
 * @property string|null $nombre
 * @property string|null $nombre_alternativo
 * @property string|null $tramite
 * @property int|null $posee_cpi_si
 * @property int|null $posee_docExt_si
 * @property int|null $vive_si
 * @property string|null $CUIL_prefijo
 * @property string|null $CUIL_sufijo
 * @property \Illuminate\Support\Carbon|null $nacimiento_fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacto|null $contacto
 * @property-read \App\Models\DocumentoSituacion|null $documentoSituacion
 * @property-read \App\Models\DocumentoTipo|null $documentoTipo
 * @property-read \App\Models\Domicilio|null $domicilio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EscuelaPersona> $escuelasPersonas
 * @property-read int|null $escuelas_personas_count
 * @property-read \App\Models\Genero|null $genero
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialInscripcion> $historialInscripciones
 * @property-read int|null $historial_inscripciones_count
 * @property-read \App\Models\Inscripcion|null $inscripcion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Legajo> $legajos
 * @property-read int|null $legajos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientosCupof
 * @property-read int|null $movimientos_cupof_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CupofMovimiento> $movimientosCupofActivos
 * @property-read int|null $movimientos_cupof_activos_count
 * @property-read \App\Models\Departamento|null $nacimientoDepartamento
 * @property-read \App\Models\Localidad|null $nacimientoLocalidad
 * @property-read \App\Models\Nacion|null $nacimientoPais
 * @property-read \App\Models\Provincia|null $nacimientoProvincia
 * @property-read \App\Models\Nacion|null $nacionalidad
 * @property-read \App\Models\Sexo|null $sexo
 * @property-read \App\Models\Usuario|null $usuario
 * @property-read \App\Models\PersonaVinculoPersona|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Persona> $vinculosComoAdulto
 * @property-read int|null $vinculos_como_adulto_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Persona> $vinculosComoEstudiante
 * @property-read int|null $vinculos_como_estudiante_count
 * @method static \Database\Factories\PersonaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inDepartamento(int $departamentoId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inProvincia(int $provinciaId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona inRegion(int $regionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCUILPrefijo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCUILSufijo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoSituacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereDocumentoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereGeneroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereLocalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacimientoFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNacionalidadNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereNombreAlternativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona wherePoseeCpiSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona wherePoseeDocExtSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereSexoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereTramite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona whereViveSi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Persona withoutTrashed()
 */
	class Persona extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $persona_estudiante_id
 * @property int $persona_adulto_id
 * @property int $vinculo_id
 * @property string|null $detalle
 * @property \Illuminate\Support\Carbon|null $vencimiento_fecha
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Persona|null $adulto
 * @property-read \App\Models\Persona|null $estudiante
 * @property-read \App\Models\Vinculo|null $vinculo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereDetalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona wherePersonaAdultoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona wherePersonaEstudianteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereVencimientoFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona whereVinculoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonaVinculoPersona withoutTrashed()
 */
	class PersonaVinculoPersona extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $plan_ciclo_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property int|null $duracion_anios
 * @property string|null $resolucion
 * @property string|null $orientacion
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnioPlan> $anioPlanes
 * @property-read int|null $anio_planes_count
 * @property-read \App\Models\PlanCiclo|null $planCiclo
 * @method static \Database\Factories\PlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDuracionAnios($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereOrientacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePlanCicloId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereResolucion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withoutTrashed()
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plan> $planes
 * @property-read int|null $planes_count
 * @method static \Database\Factories\PlanCicloFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanCiclo withoutTrashed()
 */
	class PlanCiclo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $escuela_id
 * @property int $anio_plan_id
 * @property int|null $turno_inicio_id
 * @property int|null $turno_fin_id
 * @property int|null $jornada_id
 * @property int|null $lectivo_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnioPlan|null $anioPlan
 * @property-read \App\Models\Lectivo|null $cicloLectivo
 * @property-read \App\Models\Escuela|null $escuela
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Espacio> $espacios
 * @property-read int|null $espacios_count
 * @property-read \App\Models\Jornada|null $jornada
 * @property-read \App\Models\Turno|null $turnoFin
 * @property-read \App\Models\Turno|null $turnoInicio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereAnioPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereEscuelaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereJornadaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereLectivoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereTurnoFinId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereTurnoInicioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Propuesta withoutTrashed()
 */
	class Propuesta extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $id_georef
 * @property int|null $nacion_id
 * @property int|null $georef_fuente_id
 * @property int|null $georef_categoria_id
 * @property string $nombre
 * @property string|null $nombre_completo
 * @property string|null $iso_nombre
 * @property string|null $iso_id
 * @property numeric|null $centroide_lat
 * @property numeric|null $centroide_lon
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \App\Models\GeorefCategoria|null $georefCategoria
 * @property-read \App\Models\GeorefFuente|null $georefFuente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipio> $municipios
 * @property-read int|null $municipios_count
 * @property-read \App\Models\Nacion|null $nacion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Region> $regiones
 * @property-read int|null $regiones_count
 * @method static \Database\Factories\ProvinciaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCentroideLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCentroideLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereGeorefCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereGeorefFuenteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIdGeoref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIsoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereIsoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereNombreCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Provincia withoutTrashed()
 */
	class Provincia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $usuario_id
 * @property int $provincia_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read \App\Models\Provincia|null $provincia
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProvinciaUsuario withoutTrashed()
 */
	class ProvinciaUsuario extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cupof> $cupofs
 * @property-read int|null $cupofs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PuestoTipo withoutTrashed()
 */
	class PuestoTipo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $usuario_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $device_id
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken withoutTrashed()
 */
	class RefreshToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $provincia_id
 * @property string $numero
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Departamento> $departamentos
 * @property-read int|null $departamentos_count
 * @property-read \App\Models\Provincia|null $provincia
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereProvinciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region withoutTrashed()
 */
	class Region extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $usuario_id
 * @property int $region_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read \App\Models\Region|null $region
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionUsuario withoutTrashed()
 */
	class RegionUsuario extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionBaja> $inscripcionBajas
 * @property-read int|null $inscripcion_bajas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InscripcionPase> $inscripcionPases
 * @property-read int|null $inscripcion_pases_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalidaMotivo withoutTrashed()
 */
	class SalidaMotivo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Espacio> $espacios
 * @property-read int|null $espacios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeccionTipo withoutTrashed()
 */
	class SeccionTipo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Escuela> $escuelas
 * @property-read int|null $escuelas_count
 * @method static \Database\Factories\SectorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sector withoutTrashed()
 */
	class Sector extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $letra
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Persona> $personas
 * @property-read int|null $personas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereLetra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sexo withoutTrashed()
 */
	class Sexo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestasTurnoFin
 * @property-read int|null $propuestas_turno_fin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Propuesta> $propuestasTurnoInicio
 * @property-read int|null $propuestas_turno_inicio_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Turno withoutTrashed()
 */
	class Turno extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property CarbonInterface|Carbon|null $verification_token_created_at
 * @property CarbonInterface|Carbon|null $email_verified_at
 * @property string $nombre
 * @property int|null $documento_tipo_id
 * @property string|null $documento_numero
 * @property bool $es_administrador
 * @property string $estado
 * @property string $email
 * @property string|null $avatar_path
 * @property string $password
 * @property bool $password_set
 * @property string|null $verification_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_set_at
 * @property int $email_correction_attempts
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DistritoUsuario|null $distritoUsuario
 * @property-read \App\Models\DocumentoTipo|null $documentoTipo
 * @property-read string|null $avatar_url
 * @property-read bool $has_password
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Persona|null $persona
 * @property-read \App\Models\ProvinciaUsuario|null $provinciaUsuario
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RefreshToken> $refreshTokens
 * @property-read int|null $refresh_tokens_count
 * @property-read \App\Models\RegionUsuario|null $regionUsuario
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UsuarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario inDepartamento(int $departamentoId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario inProvincia(int $provinciaId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario inRegion(int $regionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDocumentoNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereDocumentoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailCorrectionAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailSetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEsAdministrador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario wherePasswordSet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereVerificationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereVerificationTokenCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario withoutTrashed()
 */
	class Usuario extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vinculo_tipo_id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonaVinculoPersona> $pvps
 * @property-read int|null $pvps_count
 * @property-read \App\Models\VinculoTipo|null $vinculoTipo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo whereVinculoTipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vinculo withoutTrashed()
 */
	class Vinculo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int|null $orden
 * @property int $vigente
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vinculo> $vinculos
 * @property-read int|null $vinculos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo whereVigente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VinculoTipo withoutTrashed()
 */
	class VinculoTipo extends \Eloquent {}
}

