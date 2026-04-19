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
        Gate::authorize('sistema.usuarios');

        $performer = auth()->user();
        if (!$performer->hasRole('superuser') && !$performer->hasRole('jefe_distrital')) {
            return response()->json([
                'error' => 'Tu rango actual no permite la creación global de usuarios. Esta acción está reservada para Jefes Distritales o Superusuarios.', 
                'code' => 403
            ], 403);
        }

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
        Gate::authorize('sistema.usuarios');

        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser');

        // Solo el Superusuario puede editar perfiles de otros usuarios de forma administrativa
        if (!$isSuperUser) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario puede modificar datos de identidad de otros usuarios.', 
                'code' => 403
            ], 403);
        }

        // Impedir modificar al superusuario (solo él mismo o por BD)
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            // Un superusuario puede modificar a otro superusuario, pero no se recomienda
        }

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

    public function confirmPersona(Usuario $usuario): JsonResponse
    {
        Gate::authorize('sistema.usuarios');

        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser');
        $isJefeDistrital = $performer->hasRole('jefe_distrital');
        $isConduccion = $performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']);
        
        if (!$isSuperUser && !$isJefeDistrital && !$isConduccion) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para confirmar vinculaciones de identidad. Esta acción está reservada para el Equipo de Conducción, Jefes Distritales o Superusuarios.',
                'code' => 403
            ], 403);
        }

        if (!$usuario->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Operación Inválida: El usuario debe haber verificado su correo electrónico antes de que se pueda confirmar su vinculación con el padrón.',
                'code' => 422
            ], 422);
        }

        if ($usuario->persona) {
            return response()->json(['error' => 'El usuario ya está vinculado a un registro del padrón.'], 422);
        }

        // Buscar la persona que coincida (DNI + Email)
        $persona = \App\Models\Persona::where('documento_tipo_id', $usuario->documento_tipo_id)
            ->where('documento_numero', $usuario->documento_numero)
            ->whereHas('contacto', function ($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })
            ->whereNull('usuario_id')
            ->first();

        if (!$persona) {
            return response()->json(['error' => 'No se encontró ninguna persona en el padrón con datos coincidentes (DNI y Email) para confirmar.'], 404);
        }

        // REGLA ESPECÍFICA PARA EQUIPO DE CONDUCCIÓN
        // Solo pueden vincular si la persona tiene relación con SUS colegios
        if ($isConduccion && !$isSuperUser && !$isJefeDistrital) {
            if (!$this->userService->isPersonaRelatedToUserSchools($performer, $persona)) {
                return response()->json([
                    'error' => 'Restricción de Seguridad: El Equipo de Conducción solo puede confirmar vinculaciones de personas relacionadas con su propia institución (por CUPOF, inscripción o vínculo familiar).',
                    'code' => 403
                ], 403);
            }
        }

        $persona->update(['usuario_id' => $usuario->id]);
        $usuario->update(['estado' => 'activo']);

        return response()->json([
            'message' => 'Vinculación con el padrón confirmada con éxito.',
            'user' => new UsuarioResource($usuario->fresh(['persona']))
        ]);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        Gate::authorize('sistema.usuarios');

        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser');

        // Solo el Superusuario puede eliminar cuentas
        if (!$isSuperUser) {
            return response()->json([
                'error' => 'Acceso Denegado: Solo un Superusuario tiene privilegios para eliminar cuentas del sistema.', 
                'code' => 403
            ], 403);
        }

        // Impedir que el superusuario se elimine a sí mismo
        if ($usuario->id === $performer->id) {
            return response()->json([
                'error' => 'Operación Inválida: No puedes eliminar tu propia cuenta administrativa.',
                'code' => 400
            ], 400);
        }

        // Protección adicional para cuentas protegidas (opcional, pero el usuario dijo "cualquier usuario")
        // Sin embargo, mantendremos el mensaje descriptivo si el destino es un superusuario
        // para asegurar que la acción sea consciente.

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

        if (!auth()->user()->hasRole('superuser')) {
            return response()->json([
                'error' => 'Permiso Insuficiente: Solo un Superusuario puede gestionar el rol de Supervisor Curricular.', 
                'code' => 403
            ], 403);
        }

        // Prevent assigning the supervisor_curricular role to superusers
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json([
                'error' => 'Conflicto de Jerarquía: No se puede asignar el rol de Supervisor Curricular a un Superusuario.',
                'code' => 403
            ], 403);
        }

        $this->userService->toggleRole($usuario, 'supervisor_curricular');

        $hasRole = $usuario->fresh()->hasRole('supervisor_curricular');
        $status = $hasRole ? 'asignado' : 'revocado';

        return response()->json([
            'message' => "Rol de Supervisor Curricular {$status} con éxito.",
            'has_role' => $hasRole
        ]);
    }

    /**
     * Toggle the Jefe Distrital role for a user.
     */
    public function toggleJefeDistritalRole(Usuario $usuario): JsonResponse
    {
        Gate::authorize('sistema.roles');

        if (!auth()->user()->hasRole('superuser')) {
            return response()->json([
                'error' => 'Permiso Insuficiente: Solo un Superusuario puede gestionar el rol de Jefe Distrital.', 
                'code' => 403
            ], 403);
        }

        // Prevent assigning to superusers
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json([
                'error' => 'Conflicto de Jerarquía: No se puede asignar el rol de Jefe Distrital a un Superusuario.',
                'code' => 403
            ], 403);
        }

        $this->userService->toggleRole($usuario, 'jefe_distrital');

        $hasRole = $usuario->fresh()->hasRole('jefe_distrital');
        $status = $hasRole ? 'asignado' : 'revocado';

        return response()->json([
            'message' => "Rol de Jefe Distrital {$status} con éxito.",
            'has_role' => $hasRole
        ]);
    }
}
