<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use App\Http\Resources\PersonaResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ComunidadEducativaController extends Controller
{
    /**
     * Lista a toda la comunidad educativa vinculada a una escuela.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $schoolId = $request->input('escuela_id');
        $relacionFilter = $request->input('relacion');

        if (!$schoolId) {
            return response()->json(['error' => 'Debe especificar una institución educativa.'], 422);
        }

        // Validar permisos
        \Illuminate\Support\Facades\Gate::authorize('view-comunidad', (int)$schoolId);

        $query = Persona::with([
            'documentoTipo', 
            'usuario', 
            'nacionalidad', 
            'genero', 
            'contacto',
            'movimientosCupofActivos' => function($q) use ($schoolId) {
                $q->whereHas('cupof', function($sq) use ($schoolId) {
                    $sq->where('escuela_id', $schoolId);
                })->with(['cupof.escalafon', 'cupof.puestoTipo']);
            },
            'inscripcion' => function($q) use ($schoolId) {
                $q->where('escuela_id', $schoolId);
            },
            'vinculosComoAdulto' => function($q) use ($schoolId) {
                $q->whereHas('inscripcion', function($sq) use ($schoolId) {
                    $sq->where('escuela_id', $schoolId);
                });
            }
        ])
            ->where(function($q) use ($schoolId, $relacionFilter) {
                if ($relacionFilter) {
                    $this->applyRelacionFilter($q, $schoolId, $relacionFilter);
                } else {
                    // 1. Personal de la escuela (CUPOF activo)
                    $q->whereHas('movimientosCupof', function($sq) use ($schoolId) {
                        $sq->where('activo', true)
                          ->whereHas('cupof', function($ssq) use ($schoolId) {
                              $ssq->where('escuela_id', $schoolId);
                          });
                    });

                    // 2. Alumnos inscritos (Inscripcion)
                    $q->orWhereHas('inscripcion', function($sq) use ($schoolId) {
                        $sq->where('escuela_id', $schoolId);
                    });

                    // 3. Familiares vinculados a alumnos de la escuela
                    $q->orWhereHas('vinculosComoAdulto', function($sq) use ($schoolId) {
                        $sq->whereHas('inscripcion', function($ssq) use ($schoolId) {
                            $ssq->where('escuela_id', $schoolId);
                        });
                    });
                }
            });

        // Búsqueda opcional
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('documento_numero', 'like', "%{$search}%");
            });
        }

        $personas = $query->orderBy('apellido')->orderBy('nombre')->paginate($request->per_page ?? 15);

        return PersonaResource::collection($personas);
    }

    /**
     * Applies the relationship filter to the query.
     */
    private function applyRelacionFilter($query, $schoolId, $relacion): void
    {
        $relacion = mb_strtolower($relacion, 'UTF-8');

        if (str_contains($relacion, 'docente') || str_contains($relacion, 'auxiliar') || str_contains($relacion, 'administrativo')) {
            $query->whereHas('movimientosCupof', function($q) use ($schoolId, $relacion) {
                $q->where('activo', true)
                  ->whereHas('cupof', function($sq) use ($schoolId, $relacion) {
                      $sq->where('escuela_id', $schoolId)
                        ->whereHas('escalafon', function($ssq) use ($relacion) {
                            $ssq->where('nombre', 'like', "%{$relacion}%");
                        });
                  });
            });
        } elseif ($relacion === 'estudiante') {
            $query->whereHas('inscripcion', function($q) use ($schoolId) {
                $q->where('escuela_id', $schoolId);
            });
        } else {
            // Asumimos que es un tipo de vínculo familiar (PADRE, MADRE, TUTOR, etc.)
            // Debemos verificar que la persona tenga ese tipo de vínculo con AL MENOS UN estudiante de esta escuela.
            $query->whereExists(function ($ex) use ($schoolId, $relacion) {
                $ex->select(\DB::raw(1))
                   ->from('persona_vinculo_persona')
                   ->join('vinculos', 'persona_vinculo_persona.vinculo_id', '=', 'vinculos.id')
                   ->join('inscripcions', 'persona_vinculo_persona.persona_estudiante_id', '=', 'inscripcions.persona_id')
                   ->whereColumn('persona_vinculo_persona.persona_adulto_id', 'personas.id')
                   ->where('inscripcions.escuela_id', $schoolId)
                   ->where('vinculos.nombre', 'like', "%{$relacion}%")
                   ->whereNull('persona_vinculo_persona.deleted_at')
                   ->whereNull('inscripcions.deleted_at');
            });
        }
    }
}
