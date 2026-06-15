<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use App\Http\Resources\EscuelaUsuarioResource;
use App\Http\Requests\Api\V1\Admin\EscuelaUsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EscuelaUsuarioController extends Controller
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
        $this->authorize('viewAny', \App\Models\EscuelaUsuario::class);

        $query = \App\Models\EscuelaUsuario::with(['usuario.persona', 'escuela', 'role']);

        // Si no es Superusuario ni Jefatura, limitamos a sus propias escuelas (Equipo de Conducción)
        if (!$user->hasAnyRole(['superuser', 'jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            $mySchools = $user->escuelaUsuarios()
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

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        return EscuelaUsuarioResource::collection($query->paginate($request->per_page ?? 15));
    }

    /**
     * Direct assign a user to a school.
     */
    public function store(EscuelaUsuarioRequest $request): JsonResponse
    {
        try {
            $user = \App\Models\Usuario::findOrFail($request->usuario_id);
            $link = $this->escuelaService->assignDirect($user, $request->escuela_id, $request->role_id);

            return response()->json([
                'message' => 'Rol institucional asignado con éxito.',
                'data' => new EscuelaUsuarioResource($link)
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
    public function update(EscuelaUsuarioRequest $request, string $id): JsonResponse
    {
        try {
            $link = \App\Models\EscuelaUsuario::findOrFail($id);
            
            // Validar permisos usando el nuevo método del servicio
            $this->escuelaService->validateAssignmentPermissions($link->escuela_id, $request->role_id);

            // Actualizar el rol del registro específico
            $link->update([
                'role_id' => $request->role_id,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Rol institucional actualizado con éxito.',
                'data' => new EscuelaUsuarioResource($link->fresh()->load(['usuario.persona', 'escuela', 'role']))
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
            $link = \App\Models\EscuelaUsuario::findOrFail($id);
            
            // Validar permisos usando el servicio antes de eliminar
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
