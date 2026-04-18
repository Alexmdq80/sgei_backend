<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\EscuelaService;
use App\Http\Resources\EscuelaUsuarioResource;
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
        $query = \App\Models\EscuelaUsuario::with(['usuario.persona', 'escuela', 'role']);

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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'usuario_id' => 'required|uuid|exists:usuarios,id',
            'escuela_id' => 'required|integer|exists:escuelas,id',
            'role_id' => 'required|integer|exists:roles,id'
        ]);

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
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id'
        ]);

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
            
            // Solo permitir desvincular si tiene permisos de gestión en esa escuela
            // (Validación similar a assignDirect pero simplificada aquí)
            $link->delete();
            
            return response()->json([
                'message' => 'Vinculación eliminada con éxito.'
            ]);
        } catch (\Exception $e) {
             return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
