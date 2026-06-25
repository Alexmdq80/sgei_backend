<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistritoUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DistritoUsuarioController extends Controller
{
    use AuthorizesRequests;

    /**
     * List all district-user associations.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('manage-districts', Usuario::class);

        $associations = DistritoUsuario::with(['usuario.persona', 'distrito'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($associations);
    }

    /**
     * Assign a user to a district.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('manage-districts', Usuario::class);

        $validated = $request->validate([
            'usuario_id' => 'required|uuid|exists:usuarios,id',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        // Asegurarse de que el usuario tenga el rol jefe_distrital
        $usuario = Usuario::findOrFail($validated['usuario_id']);
        if (!$usuario->hasRole('jefe_distrital')) {
            $usuario->assignRole('jefe_distrital');
        }

        $association = DistritoUsuario::updateOrCreate(
            ['usuario_id' => $validated['usuario_id']],
            ['departamento_id' => $validated['departamento_id']]
        );

        return response()->json([
            'message' => 'Jefe Distrital asignado correctamente.',
            'data' => $association->load(['usuario.persona', 'distrito'])
        ], 201);
    }

    /**
     * Remove the association.
     */
    public function destroy(string $id): JsonResponse
    {
        $distritoUsuario = DistritoUsuario::findOrFail($id);
        $this->authorize('manage-districts', Usuario::class);

        $distritoUsuario->delete();

        return response()->json([
            'message' => 'Asignación de distrito eliminada.'
        ]);
    }
}
