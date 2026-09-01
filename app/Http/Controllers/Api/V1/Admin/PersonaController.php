<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PersonaResource;
use App\Services\UserService;
use App\Services\PersonaService;
use App\Services\CupofService;
use App\Http\Requests\Api\V1\Admin\PersonaRequest;
use App\DTOs\Persona\CreatePersonaDTO;
use App\DTOs\Persona\UpdatePersonaDTO;
use App\DTOs\Persona\PersonaFilterDTO;
use App\Exceptions\ConfirmationRequiredException;
use App\Notifications\UserLinkedNotification;
use Illuminate\Support\Facades\Storage;
class PersonaController extends Controller
{
    protected UserService $userService;
    protected PersonaService $personaService;
    protected CupofService $cupofService;

    public function __construct(
        UserService $userService,
        PersonaService $personaService,
        CupofService $cupofService
    ) {
        $this->userService = $userService;
        $this->personaService = $personaService;
        $this->cupofService = $cupofService;
    }

    /**
     * Display a listing of the people (Agentes/Personas).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Persona::class);

        $filters = PersonaFilterDTO::fromRequest($request);
        $personas = $this->personaService->getFilteredPaginated($filters);

        return PersonaResource::collection($personas);
    }

    /**
     * Display the specified person.
     */
    public function show(Persona $persona): PersonaResource
    {
        $this->authorize('view', $persona);

        return new PersonaResource($persona->load([
            'documentoTipo',
            'documentoSituacion',
            'sexo',
            'genero',
            'usuario.roles',
            'nacionalidad',
            'nacimientoPais',
            'nacimientoProvincia',
            'nacimientoDepartamento',
            'nacimientoLocalidad',
            'domicilio.calle',
            'contacto'
        ]));
    }

    /**
     * Store a newly created person in storage.
     */
    public function store(PersonaRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Persona::class);

        $dto = CreatePersonaDTO::fromRequest($request);
        $persona = $this->personaService->createPersona(
            $dto,
            $request->input('cuil'),
            $request->input('email')
        );

        return response()->json([
            'message' => 'Persona registrada con éxito en el padrón.',
            'data' => new PersonaResource($persona)
        ], 201);
    }

    /**
     * Update the specified person in storage.
     */
    public function update(PersonaRequest $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $persona);

        $dto = UpdatePersonaDTO::fromRequest($request);

        try {
            $updatedPersona = $this->personaService->updatePersona(
                $persona,
                $dto,
                array_key_exists('email', $request->validated())
            );
        } catch (ConfirmationRequiredException $e) {
            throw $e; // Dejar que Laravel invoque render() y devuelva HTTP 409
        } catch (\Exception $e) {
            $code = $e->getCode() === 403 ? 403 : 422;
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $code
            ], $code);
        }

        return response()->json([
            'message' => 'Registro de persona actualizado con éxito.',
            'data' => new PersonaResource($updatedPersona)
        ]);
    }

    public function destroy(Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('delete', $persona);

        $this->personaService->deletePersona($persona);

        return response()->json([
            'message' => 'Registro de persona eliminado con éxito.'
        ]);
    }

    /**
     * Manually resends the activation email for a Persona.
     */
    public function resendActivation(Persona $persona): \Illuminate\Http\JsonResponse
    {
        // Se aplican las mismas reglas que para link-user o asignación de roles
        $performer = auth()->user();

        $canResend = $performer->hasRole('superuser')
            || $performer->es_administrador;

        if (!$canResend) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para realizar esta acción administrativa.',
                'code' => 403
            ], 403);
        }

        if ($persona->usuario && ($persona->usuario->es_administrador || $persona->usuario->hasRole('superuser'))) {
            return response()->json([
                'error' => 'Acceso Denegado: No se puede reenviar la activación a un superusuario.',
                'code' => 403
            ], 403);
        }

        try {
            $this->personaService->resendActivation($persona);
            return response()->json([
                'message' => 'Invitación de activación reenviada con éxito al correo registrado.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function tryLinkUser(Persona $persona): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser') || $performer->es_administrador;

        if (!$isSuperUser) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para confirmar vinculaciones de identidad.',
                'code' => 403
            ], 403);
        }
        if (!$persona->vive_si) {
            return response()->json([
                'error' => 'Acción no permitida: la persona está registrada como fallecida y no puede vincularse a un usuario.',
                'code' => 409
            ], 409);
        }

        if ($persona->usuario_id) {
            $existingUser = $persona->usuario;
            if (!$existingUser || $existingUser->estado !== 'vinculacion_pendiente') {
                return response()->json(['error' => 'Esta persona ya tiene un usuario vinculado y activo.'], 422);
            }
            // Si el usuario existe y está pendiente, permitimos que el flujo continúe para validación jerárquica y activación
            $matchingUser = $existingUser;
        } else {
            $documentoNumeroRaw = $persona->getRawOriginal('documento_numero');
            $contactoEmail = $persona->contacto?->email;

            $matchingUser = \App\Models\Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
                ->where('documento_numero', $documentoNumeroRaw)
                ->where('email', $contactoEmail)
                ->with(['roles'])
                ->first();
        }

        if (!$matchingUser) {
            return response()->json(['error' => 'No se encontró ningún usuario con el mismo documento y correo electrónico coincidente.'], 404);
        }

        if (!$matchingUser->email_verified_at) {
            return response()->json(['error' => 'Se encontró un usuario coincidente, pero aún no ha verificado su cuenta de correo electrónico.'], 422);
        }

        if ($matchingUser->persona) {
            return response()->json(['error' => 'El usuario coincidente ya está vinculado a otra persona.'], 422);
        }

        $persona->update(['usuario_id' => $matchingUser->id]);
        $matchingUser->notify(new UserLinkedNotification($persona->nombre, $persona->apellido));
        $matchingUser->update(['estado' => 'activo']);

        // Sincronizar roles basados en CUPOF ahora que hay vínculo de identidad
        if (!$persona->relationLoaded('movimientosCupofActivos')) {
            $persona->load(['movimientosCupofActivos.cupof']);
        }

        foreach ($persona->movimientosCupofActivos as $movimiento) {
            $this->cupofService->refreshUserRoleInSchool($matchingUser, $movimiento->cupof->escuela_id, $persona);
        }

        return response()->json([
            'message' => 'Usuario vinculado con éxito.',
            'usuario_email' => $matchingUser->email
        ]);
    }

    /**
     * Desvincula el usuario de una persona.
     */
    public function unlinkUser(Persona $persona): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser') || $performer->es_administrador;

        if (!$persona->usuario_id) {
            return response()->json(['error' => 'Esta persona no tiene ningún usuario vinculado.'], 422);
        }

        $linkedUser = $persona->usuario;
        $linkedUser->loadMissing(['roles']);

        if (!$isSuperUser) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para desvincular usuarios.',
                'code' => 403
            ], 403);
        }
        $this->personaService->unlinkUser($persona);

        return response()->json([
            'message' => 'Usuario desvinculado con éxito.'
        ]);
    }

    /**
     * Removes an administrative role from a persona.
     */
    public function removeRole(Request $request, Persona $persona, string $role): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();

        if (!$performer->hasRole('superuser') && !$performer->es_administrador) {
            return response()->json(['error' => 'Rol no válido para esta operación administrativa.'], 422);
        }

        try {
            $this->personaService->removeAdministrativeRole($persona, $role);
            return response()->json(['message' => 'Rol administrativo revocado con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

    }
    /**
     * Stream a Persona's profile photo (authorized, private storage).
     */
    public function getFoto(Persona $persona)
    {
        $this->authorize('viewFoto', $persona);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        if (!$persona->foto_path || !$disk->exists($persona->foto_path)) {
            return response()->json(['error' => 'Foto no encontrada.'], 404);
        }

        return $disk->response($persona->foto_path);
    }

    /**
     * Upload / replace a Persona's profile photo.
     */
    public function uploadFoto(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $persona);

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ]);

        $fotoUrl = $this->personaService->updateFoto($persona, $request->file('foto'));

        return response()->json([
            'message' => 'Foto de perfil actualizada con éxito.',
            'foto_url' => $fotoUrl,
        ]);
    }

    /**
     * Delete a Persona's profile photo.
     */
    public function deleteFoto(Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $persona);

        $this->personaService->deleteFoto($persona);

        return response()->json([
            'message' => 'Foto de perfil eliminada con éxito.',
            'foto_url' => null,
        ]);
    }

}