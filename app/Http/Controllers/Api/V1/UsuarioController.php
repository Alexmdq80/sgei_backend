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
        $this->authorize('manageScoped', $usuario);

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
        
        $filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page']);

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
        $performer = auth()->user();
        
        // Autorización basada en Jurisdicción
        if (!$performer->hasRole('superuser') && !$performer->es_administrador && !$performer->can('manageScoped', $usuario)) {
            return response()->json(['error' => 'Acceso Denegado: No tienes permisos para gestionar este usuario según tu jurisdicción.'], 403);
        }

        if (!$usuario->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Operación Inválida: El usuario debe haber verificado su correo electrónico antes de que se pueda confirmar su vinculación con el padrón.',
                'code' => 422
            ], 422);
        }

        if ($usuario->persona) {
            // Si ya está vinculado, simplemente nos aseguramos de que el estado sea activo
            if ($usuario->estado !== 'activo') {
                $usuario->update(['estado' => 'activo']);
            }

            // Sincronizar roles por si acaso quedó desfasado
            app(\App\Services\CupofService::class)->syncAllRolesFromCupof($usuario);

            return response()->json([
                'message' => 'El usuario ya se encontraba vinculado y ahora ha sido activado.',
                'user' => new UsuarioResource($usuario->fresh(['persona', 'escuelaUsuarios.role']))
            ]);
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

        // REGLA: El Equipo de Conducción NO puede confirmar vinculaciones de identidad.
        // Solo Superusuario y Jefaturas Jerárquicas (Provincial, Regional, Distrital) tienen este poder.
        if ($performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']) && !$performer->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: El Equipo de Conducción no tiene permisos para confirmar vinculaciones de identidad con el padrón.',
                'code'  => 403
            ], 403);
        }

        $persona->update(['usuario_id' => $usuario->id]);
        $usuario->update(['estado' => 'activo']);

        // Sincronizar roles basados en CUPOF inmediatamente tras la vinculación
        app(\App\Services\CupofService::class)->syncAllRolesFromCupof($usuario);

        return response()->json([
            'message' => 'Vinculación con el padrón confirmada con éxito.',
            'user' => new UsuarioResource($usuario->fresh(['persona', 'escuelaUsuarios.role']))
        ]);
    }

    /**
     * Busca personas candidatas del padrón que coincidan con el usuario (DNI + Email).
     */
    public function candidatosPersona(Usuario $usuario)
    {
        $this->authorize('view', $usuario);

        $candidatos = $this->userService->getCandidatosPersona($usuario);

        return \App\Http\Resources\PersonaResource::collection($candidatos);
    }

    /**
     * Vincula manualmente una persona del padrón a un usuario.
     */
    public function vincularPersona(Usuario $usuario, \App\Models\Persona $persona): JsonResponse
    {
        // Validaciones de seguridad (espejo de confirmPersona)
        if ($usuario->persona) {
            return response()->json(['error' => 'El usuario ya tiene una persona vinculada.'], 422);
        }
        if ($persona->usuario_id) {
            return response()->json(['error' => 'Esta persona ya está vinculada a otro usuario.'], 422);
        }

        $persona->update(['usuario_id' => $usuario->id]);
        $usuario->update(['estado' => 'activo']);

        // Sincronizar roles basados en CUPOF
        app(\App\Services\CupofService::class)->syncAllRolesFromCupof($usuario);

        return response()->json([
            'message' => 'Persona vinculada con éxito al usuario.',
            'user' => new UsuarioResource($usuario->fresh(['persona', 'escuelaUsuarios.role']))
        ]);
    }

    /**
     * Desvincula la persona del usuario.
     */
    public function desvincularPersona(Usuario $usuario): JsonResponse
    {
        if (!$usuario->persona) {
            return response()->json(['error' => 'El usuario no tiene una persona vinculada.'], 422);
        }

        app(\App\Services\PersonaService::class)->unlinkUser($usuario->persona);

        return response()->json([
            'message' => 'Persona desvinculada con éxito.',
            'user' => new UsuarioResource($usuario->fresh(['persona', 'escuelaUsuarios.role']))
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
