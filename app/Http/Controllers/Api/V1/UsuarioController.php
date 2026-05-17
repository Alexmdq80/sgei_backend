<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UsuarioResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Api\V1\UsuarioRequest;

use App\Notifications\AccountInvitationNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Manually resend the activation invitation to a user.
     */
    public function resendActivation(Usuario $usuario): JsonResponse
    {
        $this->authorize('manageGlobal', Usuario::class);

        DB::transaction(function () use ($usuario) {
            $usuario->verification_token = Str::random(60);
            $usuario->verification_token_created_at = now();
            // Si el usuario estaba activo, lo volvemos a poner en espera para forzar el flujo
            $usuario->estado = 'esperando_activacion';
            $usuario->email_verified_at = null; 
            $usuario->save();

            $usuario->notify(new AccountInvitationNotification($usuario->verification_token));
        });

        return response()->json([
            'message' => 'Invitación de activación reenviada con éxito al correo del usuario.'
        ]);
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Usuario::class);
        
        $performer = auth()->user();
        $filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page']);

        // REGLA: Equipo de Conducción solo ve usuarios vinculados a su colegio
        if (!$performer->hasRole('superuser') && $performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            $escuelas = $performer->escuela_usuarios()->whereNotNull('verified_at')->pluck('escuela_id')->toArray();
            $filters['escuela_ids'] = $escuelas; // El UserService debe manejar escuela_ids como array
        }

        $users = $this->userService->getAll($filters);

        return UsuarioResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UsuarioRequest $request): JsonResponse
    {
        $this->authorize('create', Usuario::class);

        $user = $this->userService->create($request->validated());

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
        $this->authorize('view', $usuario);
        return new UsuarioResource($usuario->load(['persona', 'documentoTipo', 'roles']));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('update', $usuario);

        $user = $this->userService->updateProfile($usuario, $request->validated());

        return response()->json([
            'message' => 'Usuario actualizado con éxito.',
            'user' => new UsuarioResource($user)
        ]);
    }

    public function confirmPersona(Usuario $usuario): JsonResponse
    {
        // Se permite a SuperUser, Distrital y Conducción según PersonaPolicy o lógica de negocio
        $performer = auth()->user();
        
        if (!$performer->hasRole('superuser') && !$performer->hasRole('jefe_distrital') && !$performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario'])) {
            return response()->json(['error' => 'Acceso Denegado.'], 403);
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
        if ($performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']) && !$performer->hasRole('superuser')) {
            if (!$this->userService->isPersonaRelatedToUserSchools($performer, $persona)) {
                return response()->json([
                    'error' => 'Restricción de Seguridad: El Equipo de Conducción solo puede confirmar vinculaciones de personas relacionadas con su propia institución.',
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
        $this->authorize('delete', $usuario);

        // Impedir que el superusuario se elimine a sí mismo
        if ($usuario->id === auth()->id()) {
            return response()->json([
                'error' => 'Operación Inválida: No puedes eliminar tu propia cuenta administrativa.',
                'code' => 400
            ], 400);
        }

        $this->userService->delete($usuario);

        return response()->json([
            'message' => 'Usuario eliminado con éxito.'
        ]);
    }
}
