<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use App\Http\Resources\EscuelaPersonaResource;
use App\Http\Requests\Api\V1\Admin\EscuelaPersonaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EscuelaPersonaController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * List school-user links.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // El permiso viewAny en la Policy verifica si es Superuser, Jefe o Conducción
        $this->authorize('viewAny', \App\Models\EscuelaPersona::class);

        $query = \App\Models\EscuelaPersona::with(['persona.documentoTipo', 'escuela.localidad', 'role']);

        // Si no es Superusuario ni Jefatura, limitamos a sus propias escuelas
        if (!$user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            $mySchools = $user->persona?->escuelasPersonas()    
                ->whereHas('role', function($q) {
                    $q->whereIn('name', \App\Services\EscuelaService::HIERARCHICAL_ROLES);
                })
                ->whereNotNull('verified_at')
                ->pluck('escuela_id');
            
            $query->whereIn('escuela_id', $mySchools);
        }
        
        if ($request->has('escuela_id')) {
            $query->where('escuela_id', $request->escuela_id);
        }

        if ($request->has('persona_id')) {    
            $query->where('persona_id', $request->persona_id);
        }

        return EscuelaPersonaResource::collection($query->paginate($request->per_page ?? 15));
                
    }

    /**
     * Direct assign a user to a school.
     */
    public function store(EscuelaPersonaRequest $request): JsonResponse
    {
        try {
            $persona = \App\Models\Persona::findOrFail($request->persona_id);
            
            $link = $this->escuelaService->assignDirect($persona, $request->escuela_id, $request->role_id);

            return response()->json([
                'message' => 'Rol institucional asignado con éxito.',
                'data' => new EscuelaPersonaResource($link)
            ], 201);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = ($code >= 400 && $code < 600) ? $code : 400;

            return response()->json([
                'error' => $e->getMessage(),
                'code' => $status
            ], $status);
        }
    }

    /**
     * Update an existing school-user link (change role).
     */
    public function update(EscuelaPersonaRequest $request, string $id): JsonResponse
    {
        try {
            $link = \App\Models\EscuelaPersona::findOrFail($id);
            
            $this->escuelaService->validateAssignmentPermissions($link->escuela_id, $request->role_id);

            $link->update([
                'role_id' => $request->role_id,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Rol institucional actualizado con éxito.',
                'data' => new EscuelaPersonaResource($link->fresh()->load(['persona', 'escuela', 'role']))
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = ($code >= 400 && $code < 600) ? $code : 400;

            return response()->json([
                'error' => $e->getMessage(),
                'code' => $status
            ], $status);
        }
    }

    /**
     * Remove a link (un-link user from school).
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $link = \App\Models\EscuelaPersona::findOrFail($id);  
            
            $this->escuelaService->validateAssignmentPermissions($link->escuela_id, $link->role_id);

            $link->delete();
            
            return response()->json([
                'message' => 'Vinculación eliminada con éxito.'
            ]);
        } catch (\Exception $e) {
             $code = $e->getCode();
             $status = ($code >= 400 && $code < 600) ? $code : 400;

             return response()->json([
                 'error' => $e->getMessage(),
                 'code' => $status
             ], $status);
        }
    }
}
