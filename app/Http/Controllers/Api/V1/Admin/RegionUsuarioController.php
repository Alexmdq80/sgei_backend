<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegionUsuario;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegionUsuarioController extends Controller
{
    /**
     * List all region-user associations (Jefes Regionales).
     */
    public function index(Request $request): JsonResponse
    {
        $usuario = auth()->user();
        if (!$usuario->hasRole('superuser') && !$usuario->hasRole('jefe_provincial')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario o Jefe Provincial puede ver los Jefes Regionales.',
                'code' => 403
            ], 403);
        }

        $query = RegionUsuario::with(['usuario.persona', 'region.provincia']);

        if ($usuario->hasRole('jefe_provincial')) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            $query->whereHas('region', function ($q) use ($provinciaId) {
                $q->where('provincia_id', $provinciaId);
            });
        }

        $associations = $query->orderBy('created_at', 'desc')->get();

        return response()->json($associations);
    }

    /**
     * Assign a user to a region.
     */
    public function store(Request $request): JsonResponse
    {
        $usuario = auth()->user();
        if (!$usuario->hasRole('superuser') && !$usuario->hasRole('jefe_provincial')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario o Jefe Provincial puede asignar Jefes Regionales.',
                'code' => 403
            ], 403);
        }

        $validated = $request->validate([
            'usuario_id' => 'required|uuid|exists:usuarios,id',
            'region_id' => 'required|integer|exists:regions,id',
        ]);

        $region = \App\Models\Region::findOrFail($validated['region_id']);

        if ($usuario->hasRole('jefe_provincial')) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            if ($region->provincia_id !== $provinciaId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo puedes asignar Jefes Regionales para regiones dentro de tu provincia.',
                    'code' => 403
                ], 403);
            }
        }

        $targetUser = Usuario::findOrFail($validated['usuario_id']);
        if (!$targetUser->hasRole('jefe_regional')) {
            $targetUser->assignRole('jefe_regional');
        }

        $association = RegionUsuario::updateOrCreate(
            ['usuario_id' => $validated['usuario_id']],
            ['region_id' => $validated['region_id']]
        );

        return response()->json([
            'message' => 'Jefe Regional asignado correctamente.',
            'data' => $association->load(['usuario.persona', 'region.provincia'])
        ], 201);
    }

    /**
     * Remove the association.
     */
    public function destroy(string $id): JsonResponse
    {
        $regionUsuario = RegionUsuario::findOrFail($id);
        $usuario = auth()->user();
        if (!$usuario->hasRole('superuser') && !$usuario->hasRole('jefe_provincial')) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario o Jefe Provincial puede remover Jefes Regionales.',
                'code' => 403
            ], 403);
        }

        if ($usuario->hasRole('jefe_provincial')) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            $regionUsuario->loadMissing('region');
            if ($regionUsuario->region->provincia_id !== $provinciaId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo puedes remover Jefes Regionales de regiones dentro de tu provincia.',
                    'code' => 403
                ], 403);
            }
        }

        $targetUser = $regionUsuario->usuario;
        if ($targetUser) {
            if ($targetUser->hasRole('jefe_regional')) {
                $targetUser->removeRole('jefe_regional');
            }
        }

        $regionUsuario->delete();

        return response()->json([
            'message' => 'Asignación de región eliminada.'
        ]);
    }
}
