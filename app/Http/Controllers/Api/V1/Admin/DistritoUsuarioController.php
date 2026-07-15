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
        $usuario = auth()->user();

        $esSuperuser       = $usuario->hasRole('superuser');
        $esJefeProvincial  = $usuario->hasRole('jefe_provincial');
        $esJefeRegional    = $usuario->hasRole('jefe_regional');

        if (!$esSuperuser && !$esJefeProvincial && !$esJefeRegional) {
            return response()->json([
                'error' => 'Acceso Denegado.',
                'code'  => 403
            ], 403);
        }

        $query = DistritoUsuario::with(['usuario.persona', 'distrito.departamento.region.provincia']);

        if ($esJefeRegional) {
            $regionId = $usuario->regionUsuario?->region_id;
            $query->whereHas('distrito', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        } elseif ($esJefeProvincial) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            $query->whereHas('distrito.region', function ($q) use ($provinciaId) {
                $q->where('provincia_id', $provinciaId);
            });
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Assign a user to a district.
     */
    public function store(Request $request): JsonResponse
    {
        $usuario = auth()->user();

        $esSuperuser       = $usuario->hasRole('superuser');
        $esJefeProvincial  = $usuario->hasRole('jefe_provincial');
        $esJefeRegional    = $usuario->hasRole('jefe_regional');

        if (!$esSuperuser && !$esJefeProvincial && !$esJefeRegional) {
            return response()->json([
                'error' => 'Acceso Denegado.',
                'code'  => 403
            ], 403);
        }

        $validated = $request->validate([
            'usuario_id'      => 'required|uuid|exists:usuarios,id',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        $departamento = \App\Models\Departamento::with('region')->findOrFail($validated['departamento_id']);

        if ($esJefeRegional) {
            $regionId = $usuario->regionUsuario?->region_id;
            if ($departamento->region_id !== $regionId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo podés asignar Jefes Distritales en departamentos de tu región.',
                    'code'  => 403
                ], 403);
            }
        } elseif ($esJefeProvincial) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            if ($departamento->region?->provincia_id !== $provinciaId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo podés asignar Jefes Distritales en departamentos de tu provincia.',
                    'code'  => 403
                ], 403);
            }
        }

        $targetUser = Usuario::findOrFail($validated['usuario_id']);
        if (!$targetUser->hasRole('jefe_distrital')) {
            $targetUser->assignRole('jefe_distrital');
        }

        $association = DistritoUsuario::updateOrCreate(
            ['usuario_id' => $validated['usuario_id']],
            ['departamento_id' => $validated['departamento_id']]
        );

        return response()->json([
            'message' => 'Jefe Distrital asignado correctamente.',
            'data'    => $association->load(['usuario.persona', 'distrito'])
        ], 201);
    }

    /**
     * Remove the association.
     */
    public function destroy(string $id): JsonResponse
    {
        $distritoUsuario = DistritoUsuario::with('distrito.region')->findOrFail($id);
        $usuario = auth()->user();

        $esSuperuser       = $usuario->hasRole('superuser');
        $esJefeProvincial  = $usuario->hasRole('jefe_provincial');
        $esJefeRegional    = $usuario->hasRole('jefe_regional');

        if (!$esSuperuser && !$esJefeProvincial && !$esJefeRegional) {
            return response()->json([
                'error' => 'Acceso Denegado.',
                'code'  => 403
            ], 403);
        }

        if ($esJefeRegional) {
            $regionId = $usuario->regionUsuario?->region_id;
            if ($distritoUsuario->distrito?->region_id !== $regionId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo podés remover Jefes Distritales de tu región.',
                    'code'  => 403
                ], 403);
            }
        } elseif ($esJefeProvincial) {
            $provinciaId = $usuario->provinciaUsuario?->provincia_id;
            if ($distritoUsuario->distrito?->region?->provincia_id !== $provinciaId) {
                return response()->json([
                    'error' => 'Acceso Denegado: Solo podés remover Jefes Distritales de tu provincia.',
                    'code'  => 403
                ], 403);
            }
        }

        $targetUser = $distritoUsuario->usuario;
        if ($targetUser?->hasRole('jefe_distrital')) {
            $targetUser->removeRole('jefe_distrital');
        }

        $distritoUsuario->delete();

        return response()->json(['message' => 'Asignación de distrito eliminada.']);
    }
}
