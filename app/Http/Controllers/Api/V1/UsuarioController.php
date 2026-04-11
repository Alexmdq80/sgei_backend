<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UsuarioResource;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Gate;

class UsuarioController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page']);
        $users = $this->userService->getAll($filters);

        return UsuarioResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento_tipo_id' => 'nullable|integer|exists:documento_tipos,id',
            'documento_numero' => 'nullable|string|max:20',
            'es_administrador' => 'nullable|boolean',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'password' => ['nullable', 'string', Password::defaults()],
        ]);

        $user = $this->userService->create($validatedData);

        return response()->json([
            'message' => 'Usuario creado con éxito.',
            'user' => new UsuarioResource($user)
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Usuario $usuario)
    {
        return new UsuarioResource($usuario->load(['persona', 'documentoTipo', 'roles']));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento_tipo_id' => 'nullable|integer|exists:documento_tipos,id',
            'documento_numero' => 'nullable|string|max:20',
            'es_administrador' => 'nullable|boolean',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $usuario->id,
            'password' => ['nullable', 'string', Password::defaults()],
        ]);

        $user = $this->userService->updateProfile($usuario, $validatedData);

        return response()->json([
            'message' => 'Usuario actualizado con éxito.',
            'user' => new UsuarioResource($user)
        ]);
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->userService->delete($usuario);

        return response()->json([
            'message' => 'Usuario eliminado con éxito.'
        ]);
    }

    /**
     * Toggle the Supervisor Curricular role for a user.
     */
    public function toggleSupervisorRole(Usuario $usuario): JsonResponse
    {
        Gate::authorize('sistema.roles');

        $this->userService->toggleRole($usuario, 'supervisor_curricular');

        $hasRole = $usuario->fresh()->hasRole('supervisor_curricular');
        $status = $hasRole ? 'asignado' : 'revocado';

        return response()->json([
            'message' => "Rol de Supervisor Curricular {$status} con éxito.",
            'has_role' => $hasRole
        ]);
    }
}
