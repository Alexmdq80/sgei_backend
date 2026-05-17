<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PersonaResource;
use App\Services\UserService;
use App\Services\PersonaService;
use App\Http\Requests\Api\V1\Admin\PersonaRequest;

class PersonaController extends Controller
{
    protected UserService $userService;
    protected PersonaService $personaService;

    public function __construct(UserService $userService, PersonaService $personaService)
    {
        $this->userService = $userService;
        $this->personaService = $personaService;
    }

    /**
     * Display a listing of the people (Agentes/Personas).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Persona::class);
        
        $query = Persona::with([
            'documentoTipo', 
            'usuario.roles', 
            'usuario.provinciaUsuario.provincia', 
            'usuario.regionUsuario.region', 
            'usuario.distritoUsuario.distrito',
            'nacionalidad', 
            'genero'
        ]);

        // 1. Búsqueda por nombre, apellido o documento
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('documento_numero', 'like', "%{$search}%");
            });
        }

        // 2. Filtro: Solo Agentes (personas con movimientos de CUPOF)
        if ($request->boolean('only_agents')) {
            $query->whereHas('movimientosCupof', function($q) use ($request) {
                // Filtro por escuela específica si se solicita
                if ($request->filled('escuela_id')) {
                    $q->whereHas('cupof', function($sq) use ($request) {
                        $sq->where('escuela_id', $request->escuela_id);
                    });
                }
            });
        }

        $personas = $query->orderBy('apellido')->orderBy('nombre')->paginate($request->per_page ?? 15);

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
            'usuario.roles',
            'usuario.provinciaUsuario.provincia', 
            'usuario.regionUsuario.region', 
            'usuario.distritoUsuario.distrito',
            'nacionalidad', 
            'nacimientoPais', 
            'nacimientoProvincia', 
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

        $validated = $request->validated();
        $personaData = \Illuminate\Support\Arr::except($validated, ['email']);

        if (!empty($personaData['cuil'])) {
            $parts = explode('-', str_replace([' ', '.'], '', $personaData['cuil']));
            if (count($parts) === 3) {
                $personaData['CUIL_prefijo'] = $parts[0];
                $personaData['CUIL_sufijo'] = $parts[2];
            }
        }

        $persona = Persona::create($personaData);

        // Crear contacto si se proporcionó email
        if (!empty($validated['email'])) {
            $persona->contacto()->create([
                'email' => $validated['email']
            ]);
        }

        return response()->json([
            'message' => 'Persona registrada con éxito en el padrón.',
            'data' => new PersonaResource($persona->fresh(['contacto', 'usuario']))
        ], 201);
    }

    /**
     * Update the specified person in storage.
     */
    public function update(PersonaRequest $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $persona);

        $validated = $request->validated();

        // REGLA DE SEGURIDAD: Controlar cambios de identidad (DNI o Email)
        $emailChanged = isset($validated['email']) && $validated['email'] !== ($persona->contacto?->email ?? null);
        $dniChanged = $validated['documento_tipo_id'] != $persona->documento_tipo_id || 
                     $validated['documento_numero'] != $persona->documento_numero;

        if ($persona->usuario_id) {
            // No permitir cambio de email si está vinculado (regla previa mantenida)
            if ($emailChanged) {
                return response()->json([
                    'error' => 'Seguridad: No se puede modificar el correo electrónico de una persona que ya tiene un usuario vinculado. Debe desvincular el usuario primero para realizar este cambio.',
                    'code' => 403
                ], 403);
            }

            // Si cambia el DNI, desvincular automáticamente al usuario
            if ($dniChanged) {
                $linkedUser = $persona->usuario;
                $persona->update(['usuario_id' => null]);
                
                // Actualizar estado del usuario desvinculado según su verificación
                if ($linkedUser) {
                    $newState = $linkedUser->hasVerifiedEmail() ? 'email_verificado' : 'email_pendiente';
                    $linkedUser->update(['estado' => $newState]);
                }
            }
        }

        $personaData = \Illuminate\Support\Arr::except($validated, ['email']);
        $persona->update($personaData);

        // Actualizar o Limpiar email de contacto
        if (array_key_exists('email', $validated)) {
            $newEmail = !empty($validated['email']) ? $validated['email'] : null;
            $persona->contacto()->updateOrCreate(
                ['persona_id' => $persona->id],
                ['email' => $newEmail]
            );
        }

        // Si cambió el DNI, intentar buscar un nuevo usuario coincidente
        if ($dniChanged) {
            $persona->refresh();
            $this->userService->linkPersonaToUser($persona);
        }

        return response()->json([
            'message' => 'Registro de persona actualizado con éxito.',
            'data' => new PersonaResource($persona->fresh(['contacto', 'usuario']))
        ]);
    }

    public function destroy(Persona $persona): \Illuminate\Http\JsonResponse
    {
        $this->authorize('delete', $persona);

        $persona->delete();

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
        if (!$performer->hasRole('superuser') && !$performer->hasRole('jefe_distrital')) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para realizar esta acción administrativa.',
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
        $isSuperUser = $performer->hasRole('superuser');
        $isJefeDistrital = $performer->hasRole('jefe_distrital');
        $isConduccion = $performer->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']);
        
        if (!$isSuperUser && !$isJefeDistrital && !$isConduccion) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para confirmar vinculaciones de identidad. Esta acción está reservada para el Equipo de Conducción, Jefes Distritales o Superusuarios.',
                'code' => 403
            ], 403);
        }

        if ($persona->usuario_id) {
            return response()->json(['error' => 'Esta persona ya tiene un usuario vinculado.'], 422);
        }

        $persona->loadMissing('contacto');
        if (!$persona->contacto || !$persona->contacto->email) {
            return response()->json(['error' => 'La persona no tiene un email de contacto registrado en el padrón para validar la identidad digital.'], 422);
        }

        $matchingUser = \App\Models\Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
            ->where('documento_numero', $persona->documento_numero)
            ->where('email', $persona->contacto->email)
            ->first();

        if (!$matchingUser) {
            return response()->json(['error' => 'No se encontró ningún usuario con el mismo documento y correo electrónico coincidente.'], 404);
        }

        if (!$matchingUser->email_verified_at) {
            return response()->json(['error' => 'Se encontró un usuario coincidente, pero aún no ha verificado su cuenta de correo electrónico.'], 422);
        }

        if ($matchingUser->persona) {
            return response()->json(['error' => 'El usuario coincidente ya está vinculado a otra persona.'], 422);
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

        $persona->update(['usuario_id' => $matchingUser->id]);
        $matchingUser->update(['estado' => 'activo']);

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
        if (!$persona->usuario_id) {
            return response()->json(['error' => 'Esta persona no tiene ningún usuario vinculado.'], 422);
        }

        $persona->update(['usuario_id' => null]);

        return response()->json([
            'message' => 'Usuario desvinculado con éxito.'
        ]);
    }

    /**
     * Assigns the Jefe Provincial role to a persona.
     */
    public function assignJefeProvincial(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        if (!auth()->user()->hasRole('superuser')) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario puede asignar el rol de Jefe Provincial.'], 403);
        }

        $request->validate([
            'provincia_id' => 'required|exists:provincias,id'
        ]);

        try {
            $user = $this->personaService->assignJefeProvincial($persona, $request->provincia_id);
            return response()->json([
                'message' => 'Cargo de Jefe Provincial asignado con éxito.',
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Assigns the Jefe Regional role to a persona.
     */
    public function assignJefeRegional(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();
        
        // REGLA ESTRICTA: Superusuario o Jefe Provincial asigna Jefe Regional
        if (!$performer->hasRole('superuser') && !$performer->hasRole('jefe_provincial')) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario o Jefe Provincial puede asignar el rol de Jefe Regional.'], 403);
        }

        $request->validate([
            'region_id' => 'required|exists:regions,id'
        ]);

        // Validaciones jerárquicas geográficas
        if (!$performer->hasRole('superuser') && $performer->hasRole('jefe_provincial')) {
            $region = \App\Models\Region::find($request->region_id);
            if ($region->provincia_id !== $performer->provinciaUsuario->provincia_id) {
                return response()->json(['error' => 'Acceso Denegado: Solo puedes asignar Jefes Regionales para regiones de tu propia provincia.'], 403);
            }
        }

        try {
            $user = $this->personaService->assignJefeRegional($persona, $request->region_id);
            return response()->json([
                'message' => 'Cargo de Jefe Regional asignado con éxito.',
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Assigns the Jefe Distrital role to a persona.
     */
    public function assignJefeDistrital(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();
        
        // REGLA ESTRICTA: Solo Superusuario o Jefe Regional puede asignar el rol de Jefe Distrital.
        // (Se quita Jefe Provincial de este nivel para forzar la cadena lineal)
        if (!$performer->hasRole('superuser') && !$performer->hasRole('jefe_regional')) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario o Jefe Regional puede asignar el rol de Jefe Distrital.'], 403);
        }

        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id'
        ]);

        // Validaciones jerárquicas geográficas
        if (!$performer->hasRole('superuser')) {
            $departamento = \App\Models\Departamento::find($request->departamento_id);
            
            if ($performer->hasRole('jefe_regional')) {
                // El departamento debe pertenecer a la región del Jefe Regional
                $performer->loadMissing('regionUsuario.region');
                if (!$performer->regionUsuario || $departamento->region_id !== $performer->regionUsuario->region_id) {
                    return response()->json(['error' => 'Acceso Denegado: Solo puedes asignar Jefes Distritales para departamentos dentro de tu Región Educativa.'], 403);
                }
            }
        }

        try {
            $user = $this->personaService->assignJefeDistrital($persona, $request->departamento_id);
            return response()->json([
                'message' => 'Cargo de Jefe Distrital asignado con éxito.',
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Assigns the Supervisor Curricular role to a persona.
     */
    public function assignSupervisor(Persona $persona): \Illuminate\Http\JsonResponse
    {
        // SEGÚN REGLA: Sólo un superusuario puede asignar Supervisor Curricular
        if (!auth()->user()->hasRole('superuser')) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario puede asignar el rol de Supervisor Curricular.'], 403);
        }

        try {
            $user = $this->personaService->assignSupervisor($persona);
            return response()->json([
                'message' => 'Cargo de Supervisor Curricular asignado con éxito.',
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Removes an administrative role from a persona.
     */
    public function removeRole(Request $request, Persona $persona, string $role): \Illuminate\Http\JsonResponse
    {
        $performer = auth()->user();
        $allowedRoles = ['jefe_provincial', 'jefe_regional', 'jefe_distrital', 'supervisor_curricular'];
        
        if (!in_array($role, $allowedRoles)) {
            return response()->json(['error' => 'Rol no válido para esta operación administrativa.'], 422);
        }

        // Reglas de seguridad jerárquica para remoción (Espejo de asignación)
        if (!$performer->hasRole('superuser')) {
            if ($role === 'jefe_provincial' || $role === 'supervisor_curricular') {
                return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario puede remover este cargo.'], 403);
            }

            if ($role === 'jefe_regional') {
                if (!$performer->hasRole('jefe_provincial')) {
                    return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario o Jefe Provincial puede remover el cargo de Jefe Regional.'], 403);
                }
                
                $persona->loadMissing('usuario.regionUsuario.region');
                $targetRegion = $persona->usuario?->regionUsuario?->region;
                if ($targetRegion && $targetRegion->provincia_id !== $performer->provinciaUsuario->provincia_id) {
                    return response()->json(['error' => 'Acceso Denegado: Solo puedes remover Jefes Regionales de tu propia provincia.'], 403);
                }
            }

            if ($role === 'jefe_distrital') {
                // REGLA ESTRICTA: Solo Jefe Regional puede remover Jefe Distrital
                if (!$performer->hasRole('jefe_regional')) {
                    return response()->json(['error' => 'Acceso Denegado: Solo un Superusuario o Jefe Regional puede remover el cargo de Jefe Distrital.'], 403);
                }

                $persona->loadMissing('usuario.distritoUsuario.distrito');
                $targetDistrito = $persona->usuario?->distritoUsuario?->distrito;

                if ($performer->hasRole('jefe_regional')) {
                    $performer->loadMissing('regionUsuario');
                    if (!$performer->regionUsuario || ($targetDistrito && $targetDistrito->region_id !== $performer->regionUsuario->region_id)) {
                        return response()->json(['error' => 'Acceso Denegado: Solo puedes remover Jefes Distritales de tu propia Región Educativa.'], 403);
                    }
                }
            }
        }

        try {
            $this->personaService->removeAdministrativeRole($persona, $role);
            return response()->json(['message' => 'Rol administrativo revocado con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
