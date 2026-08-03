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

        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json([
                'error' => 'Acceso Denegado: No se puede reenviar la activación a un superusuario.',
                'code' => 403
            ], 403);
        }

        DB::transaction(function () use ($usuario) {
            $usuario->verification_token = Str::random(60);
            $usuario->verification_token_created_at = now();
            // Si el usuario estaba activo, lo volvemos a poner en espera para forzar el flujo
            // $usuario->estado = 'esperando_activacion';
            // $usuario->email_verified_at = null; 
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
        
        //$filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page']);
        $filters = $request->only(['search', 'per_page', 'escuela_id', 'cue_anexo', 'vinculation', 'page', 'provincia_id', 'region_id', 'departamento_id']);
        
        $users = $this->userService->getAll($filters);

        return UsuarioResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     */
    /* public function store(UsuarioRequest $request): JsonResponse
    {
        $this->authorize('create', Usuario::class);

        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'Usuario creado con éxito.',
            'user' => new UsuarioResource($user)
        ], 201);
    }*/ 

    /**
     * Display the specified user.
     */
    /*public function show(Usuario $usuario)
    {
        $this->authorize('view', $usuario);
        return new UsuarioResource($usuario->load(['persona', 'documentoTipo', 'roles']));
    }*/
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
            'roles',
            'provinciaUsuario.provincia',
            'regionUsuario.region',
            'distritoUsuario.distrito'
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

        $dto = \App\DTOs\User\UpdateUserProfileDTO::fromRequest($request);
        $user = $this->userService->updateProfile($usuario, $dto);

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
                'user' => new UsuarioResource($usuario->fresh(['persona', 'persona.escuelasPersonas.role']))
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
            'user' => new UsuarioResource($usuario->fresh(['persona', 'persona.escuelasPersonas.role']))
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

    /**
     * Busca personas candidatas a vincularse con un usuario (mismo DNI + Email).
     * Filtra por jurisdicción del usuario logueado.
     */
    public function candidatosPersona(Usuario $usuario): JsonResponse
    {
        $performer = auth()->user();

        // Autorización: Solo Superuser y Jefaturas pueden buscar candidatos
        if (!$performer->hasRole('superuser') && !$performer->es_administrador
            && !$performer->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return response()->json(['error' => 'Acceso Denegado: No tienes permisos para buscar candidatos del padrón.'], 403);
        }

        // No permitir vincular superusuarios/administradores
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json(['error' => 'Acceso Denegado: No se puede vincular un superusuario con el padrón.'], 403);
        }

        // Verificar que el usuario no tenga ya una persona vinculada
        if ($usuario->persona) {
            return response()->json(['error' => 'Este usuario ya tiene una persona vinculada.'], 422);
        }

        $candidatos = $this->userService->getCandidatosPersona($usuario, $performer);

        return response()->json([
            'data' => $candidatos->map(fn ($p) => [
                'id' => $p->id,
                'nombre_completo' => "{$p->apellido}, {$p->nombre}",
                'documento_tipo' => $p->documentoTipo?->nombre,
                'documento_numero' => $p->documento_numero,
                'email' => $p->contacto?->email,
                'relaciones' => $this->getRelacionesCandidato($p),
            ])
        ]);
    }

    /**
     * Vincula una persona candidata a un usuario.
     */
    public function vincularPersona(Usuario $usuario, \App\Models\Persona $persona): JsonResponse
    {
        $performer = auth()->user();

        // Autorización: Solo Superuser y Jefaturas pueden vincular
        if (!$performer->hasRole('superuser') && !$performer->es_administrador
            && !$performer->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return response()->json(['error' => 'Acceso Denegado: No tienes permisos para vincular personas del padrón.'], 403);
        }

        // No permitir vincular superusuarios/administradores
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json(['error' => 'Acceso Denegado: No se puede vincular un superusuario con el padrón.'], 403);
        }

        // Verificar que el usuario no tenga ya una persona vinculada
        if ($usuario->persona) {
            return response()->json(['error' => 'Este usuario ya tiene una persona vinculada.'], 422);
        }

        // Verificar que la persona no esté vinculada a otro usuario
        if ($persona->usuario_id) {
            return response()->json(['error' => 'Esta persona ya está vinculada a otro usuario.'], 422);
        }

        // Verificar coincidencia de identidad (DNI + Email)
        $documentoNumeroRaw = $persona->getRawOriginal('documento_numero');
        $emailCoincide = $persona->contacto?->email === $usuario->email;
        $dniCoincide = $persona->documento_tipo_id == $usuario->documento_tipo_id
            && $documentoNumeroRaw == $usuario->documento_numero;

        if (!$emailCoincide || !$dniCoincide) {
            return response()->json(['error' => 'La persona no coincide con el documento y email del usuario.'], 422);
        }

        // Verificar jurisdicción: la persona debe estar bajo la órbita del performer
        $candidatos = $this->userService->getCandidatosPersona($usuario, $performer);
        if (!$candidatos->contains('id', $persona->id)) {
            return response()->json(['error' => 'Acceso Denegado: La persona no se encuentra bajo tu jurisdicción.'], 403);
        }

        // Vincular
        $persona->update(['usuario_id' => $usuario->id]);
        $usuario->update(['estado' => 'activo']);

        // Sincronizar roles CUPOF
        app(\App\Services\CupofService::class)->syncAllRolesFromCupof($usuario);

        return response()->json([
            'message' => 'Persona vinculada con éxito al usuario.',
            'user' => new UsuarioResource($usuario->fresh(['persona', 'persona.contacto', 'persona.escuelasPersonas.role']))
        ]);
    }

    /**
     * Desvincula la persona del usuario.
     */
    public function desvincularPersona(Usuario $usuario): JsonResponse
    {
        $performer = auth()->user();

        // Autorización: Solo Superuser y Jefaturas pueden desvincular
        if (!$performer->hasRole('superuser') && !$performer->es_administrador
            && !$performer->hasAnyRole(['jefe_provincial', 'jefe_regional', 'jefe_distrital'])) {
            return response()->json(['error' => 'Acceso Denegado: No tienes permisos para desvincular personas del padrón.'], 403);
        }
        // No permitir desvincular superusuarios/administradores
        if ($usuario->es_administrador || $usuario->hasRole('superuser')) {
            return response()->json(['error' => 'Acceso Denegado: No se puede desvincular un superusuario del padrón.'], 403);
        }

        if (!$usuario->persona) {
            return response()->json(['error' => 'Este usuario no tiene una persona vinculada.'], 422);
        }

        $persona = $usuario->persona;

        // Verificar jurisdicción: la persona debe estar bajo la órbita del performer
        $candidatos = $this->userService->getCandidatosPersona($usuario, $performer);
        // Si no está en candidatos (porque ya está vinculada), verificamos por jurisdicción directa
        $enJurisdiccion = $candidatos->contains('id', $persona->id);

        if (!$enJurisdiccion && !$performer->hasRole('superuser') && !$performer->es_administrador) {
            // Verificar jurisdicción de la persona vinculada
            $enJurisdiccion = $this->personaEnJurisdiccion($persona, $performer);
            if (!$enJurisdiccion) {
                return response()->json(['error' => 'Acceso Denegado: La persona no se encuentra bajo tu jurisdicción.'], 403);
            }
        }

        // Desvincular
        app(\App\Services\PersonaService::class)->unlinkUser($persona);

        return response()->json([
            'message' => 'Persona desvinculada con éxito del usuario.',
            'user' => new UsuarioResource($usuario->fresh(['persona', 'persona.contacto', 'persona.escuelasPersonas.role']))
        ]);
    }

    /**
     * Helper: Verifica si una persona está bajo la jurisdicción del performer.
     */
    private function personaEnJurisdiccion(\App\Models\Persona $persona, Usuario $performer): bool
    {
        if ($performer->hasRole('jefe_provincial')) {
            $provId = $performer->provinciaUsuario?->provincia_id;
            return $persona->movimientosCupofActivos()
                ->whereHas('cupof.escuela.localidad.departamento', fn ($q) => $q->where('provincia_id', $provId))
                ->exists()
                || $persona->inscripcion()
                ->whereHas('escuelaProcedencia.localidad.departamento', fn ($q) => $q->where('provincia_id', $provId))
                ->exists();
        }
        if ($performer->hasRole('jefe_regional')) {
            $regId = $performer->regionUsuario?->region_id;
            return $persona->movimientosCupofActivos()
                ->whereHas('cupof.escuela.localidad.departamento', fn ($q) => $q->where('region_id', $regId))
                ->exists()
                || $persona->inscripcion()
                ->whereHas('escuelaProcedencia.localidad.departamento', fn ($q) => $q->where('region_id', $regId))
                ->exists();
        }
        if ($performer->hasRole('jefe_distrital')) {
            $distId = $performer->distritoUsuario?->departamento_id;
            return $persona->movimientosCupofActivos()
                ->whereHas('cupof.escuela.localidad', fn ($q) => $q->where('departamento_id', $distId))
                ->exists()
                || $persona->inscripcion()
                ->whereHas('escuelaProcedencia.localidad', fn ($q) => $q->where('departamento_id', $distId))
                ->exists();
        }
        return false;
    }

    /**
     * Helper: Obtiene las relaciones institucionales de un candidato.
     */
    private function getRelacionesCandidato(\App\Models\Persona $persona): array
    {
        $relaciones = [];

        foreach ($persona->movimientosCupofActivos as $mov) {
            $relaciones[] = "CUPOF: " . ($mov->cupof?->nombre_cargo ?? 'Cargo');
        }
        if ($persona->inscripcion) {
            $relaciones[] = "ESTUDIANTE";
        }
        foreach ($persona->vinculosComoAdulto as $v) {
            $relaciones[] = "VÍNCULO FAMILIAR";
        }

        return array_values(array_unique($relaciones));
    }
}
