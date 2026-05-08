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
        $user = auth()->user();
        $schoolId = $request->input('escuela_id');

        // Si no se proporciona escuela_id, intentamos obtenerla del contexto del usuario (si es Director, etc.)
        // Nota: En una implementación real, esto debería ser validado contra los permisos del usuario.
        if (!$schoolId) {
            return response()->json(['error' => 'Debe especificar una institución educativa.'], 422);
        }

        $query = Persona::with(['documentoTipo', 'usuario', 'nacionalidad', 'genero', 'contacto'])
            ->where(function($q) use ($schoolId) {
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
}
