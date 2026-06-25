<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProvinciaUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinciaUsuarioController extends Controller
{
    /**
     * List all province-user associations (Jefes Provinciales).
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario puede ver los Jefes Provinciales.',
                'code' => 403
            ], 403);
        }

        $associations = ProvinciaUsuario::with(['usuario.persona', 'provincia'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($associations);
    }

    /**
     * Assign a user to a province.
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario puede asignar Jefes Provinciales.',
                'code' => 403
            ], 403);
        }

        $validated = $request->validate([
            'usuario_id' => 'required|uuid|exists:usuarios,id',
            'provincia_id' => 'required|integer|exists:provincias,id',
        ]);

        $usuario = Usuario::findOrFail($validated['usuario_id']);
        if (!$usuario->hasRole('jefe_provincial')) {
            $usuario->assignRole('jefe_provincial');
        }

        $association = ProvinciaUsuario::updateOrCreate(
            ['usuario_id' => $validated['usuario_id']],
            ['provincia_id' => $validated['provincia_id']]
        );

        return response()->json([
            'message' => 'Jefe Provincial asignado correctamente.',
            'data' => $association->load(['usuario.persona', 'provincia'])
        ], 201);
    }

    /**
     * Remove the association.
     */
    public function destroy(string $id): JsonResponse
    {
        $provinciaUsuario = ProvinciaUsuario::findOrFail($id);
        if (!auth()->user()->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario puede remover Jefes Provinciales.',
                'code' => 403
            ], 403);
        }

        $usuario = $provinciaUsuario->usuario;
        if ($usuario) {
            if ($usuario->hasRole('jefe_provincial')) {
                $usuario->removeRole('jefe_provincial');
            }
        }

        $provinciaUsuario->delete();

        return response()->json([
            'message' => 'Asignación de provincia eliminada.'
        ]);
    }
}
