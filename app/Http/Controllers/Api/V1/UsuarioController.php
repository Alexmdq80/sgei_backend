<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UsuarioResource;
use App\Http\Requests\Api\V1\UsuarioRequest;
use App\DTOs\User\UpdateUserProfileDTO;
use Illuminate\Support\Carbon;
use \Illuminate\Validation\ValidationException;
use \App\Services\CupofService;
use \Illuminate\Database\QueryException;

class UsuarioController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Manually resend the activation invitation to a user (pure invitation to set a password).
     */
    public function resendActivation(Usuario $usuario): JsonResponse
    {
        $this->authorize('manageScoped', $usuario);

        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: No se puede reenviar la activación a un superusuario.',
                'code' => 403
            ], 403);
        }

        try {
            $this->userService->resendActivation($usuario);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['password'][0] ?? 'No se pudo reenviar la invitación.',
                'code' => 422
            ], 422);
        }

        return response()->json([
            'message' => 'Invitación de activación reenviada con éxito al correo del usuario.'
        ]);
    }
    /**
     * Resend the email verification notification to a user.
     */
    public function resendEmailVerification(Usuario $usuario): JsonResponse
    {
        $this->authorize('manageScoped', $usuario);

        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: No se puede reenviar la verificación a un superusuario.',
                'code' => 403
            ], 403);
        }

        try {
            $this->userService->resendEmailVerification($usuario);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['email'][0] ?? 'No se pudo reenviar la verificación.',
                'code' => 422
            ], 422);
        }

        return response()->json([
            'message' => 'Verificación de email reenviada con éxito al correo del usuario.'
        ]);
    }
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Usuario::class);
        $filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page', 'provincia_id', 'region_id', 'departamento_id', 'role', 'password_set', 'email_verified', 'persona_linked', 'sort_by', 'order']);

        $users = $this->userService->getAll($filters);

        return UsuarioResource::collection($users);
    }

    /**
     * Display the specified user.
     */

    public function show(Usuario $usuario)
    {

        // 1. Cargar TODAS las relaciones necesarias
        $usuario->load([
            'persona',
            'persona.contacto',
            'persona.documentoTipo',
            'persona.escuelasPersonas.escuela',
            'persona.escuelasPersonas.role',
            'documentoTipo',
            'roles'
        ]);

        // 2. Autorizar DESPUÉS de cargar (la policy usa relaciones ya cargadas → sin N+1)
        $this->authorize('view', $usuario);
        return new UsuarioResource($usuario);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('update', $usuario);

        // Verificación de concurrencia por Bloqueo Optimista (Unix Timestamp)
        if ($request->filled('updated_at')) {
            $clientTimestamp = Carbon::parse($request->input('updated_at'))->timestamp;
            $dbTimestamp = $usuario->updated_at?->timestamp;

            if ($dbTimestamp && $dbTimestamp !== $clientTimestamp) {
                return response()->json([
                    'error' => 'Conflicto de concurrencia: El registro fue modificado por otro administrador mientras lo editabas. Por favor, recarga los datos.',
                    'code' => 409
                ], 409);
            }
        }
        // Proteger integridad del vínculo con el padrón
        $usuario->load('persona');
        if ($usuario->persona) {
            $emailChanged = $request->filled('email') && $request->input('email') !== $usuario->email;
            $dniChanged = ($request->filled('documento_tipo_id') && $request->input('documento_tipo_id') != $usuario->documento_tipo_id)
                || ($request->filled('documento_numero') && $request->input('documento_numero') != $usuario->documento_numero);

            if ($emailChanged || $dniChanged) {
                return response()->json([
                    'error' => 'Operación Inválida: Este usuario está vinculado a una persona del padrón. No se permite modificar el DNI o el Email para preservar la integridad del vínculo.',
                    'code' => 422
                ], 422);
            }
        }

        $dto = UpdateUserProfileDTO::fromRequest($request);

        try {
            $user = $this->userService->updateProfile($usuario, $dto);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'error' => 'Ya existe otro usuario con el mismo tipo y número de documento. No se pueden duplicar documentos entre usuarios.',
                    'code' => 422,
                ], 422);
            }
            throw $e;
        }

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
            app(CupofService::class)->syncAllRolesFromCupof($usuario);

            return response()->json([
                'message' => 'El usuario ya se encontraba vinculado y ahora ha sido activado.',
                'user' => new UsuarioResource($usuario->fresh(['persona', 'escuelaUsuarios.role']))
            ]);
        }

        // Buscar la persona que coincida (DNI + Email)
        $persona = Persona::where('documento_tipo_id', $usuario->documento_tipo_id)
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
                'code' => 403
            ], 403);
        }

        $persona->update(['usuario_id' => $usuario->id]);
        $usuario->update(['estado' => 'activo']);

        // Sincronizar roles basados en CUPOF inmediatamente tras la vinculación
        app(CupofService::class)->syncAllRolesFromCupof($usuario);

        return response()->json([
            'message' => 'Vinculación con el padrón confirmada con éxito.',
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
