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

            // Si cambia el DNI, desvincular automáticamente al usuario y limpiar roles/contextos
            if ($dniChanged) {
                $this->personaService->unlinkUser($persona);
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
        // SEGÚN SPEC: Toda la cadena jerárquica (Provincial, Regional, Distrital) tiene CRUD global.
        // Todos pueden reenviar activaciones de personas del padrón.
        $canResend = $performer->hasRole('superuser')
            || $performer->hasRole('jefe_provincial')
            || $performer->hasRole('jefe_regional')
            || $performer->hasRole('jefe_distrital')
            || $performer->es_administrador;

        if (!$canResend) {
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
        $isSuperUser = $performer->hasRole('superuser') || $performer->es_administrador;
        $isProvincial = $performer->hasRole('jefe_provincial');
        $isRegional = $performer->hasRole('jefe_regional');
        $isDistrital = $performer->hasRole('jefe_distrital');
        
        if (!$isSuperUser && !$isProvincial && !$isRegional && !$isDistrital) {
            return response()->json([
                'error' => 'Acceso Denegado: No tienes los privilegios necesarios para confirmar vinculaciones de identidad.',
                'code' => 403
            ], 403);
        }

        if ($persona->usuario_id) {
            $existingUser = $persona->usuario;
            if (!$existingUser || $existingUser->estado !== 'vinculacion_pendiente') {
                return response()->json(['error' => 'Esta persona ya tiene un usuario vinculado y activo.'], 422);
            }
            // Si el usuario existe y está pendiente, permitimos que el flujo continúe para validación jerárquica y activación
            $matchingUser = $existingUser;
        } else {
            $matchingUser = \App\Models\Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
                ->where('documento_numero', $persona->documento_numero)
                ->where('email', $persona->contacto->email)
                ->with(['roles', 'provinciaUsuario', 'regionUsuario.region', 'distritoUsuario'])
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

        // REGLAS JERÁRQUICAS DE VINCULACIÓN
        if (!$isSuperUser) {
            // 1. Jefe Provincial: Solo puede vincular Jefes Regionales de su provincia
            if ($isProvincial) {
                $userProvId = $performer->provinciaUsuario?->provincia_id;
                
                $isTargetRegionalInMyProv = $matchingUser->hasRole('jefe_regional') && 
                                           $matchingUser->regionUsuario?->region?->provincia_id === $userProvId;

                if (!$isTargetRegionalInMyProv) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Provincial, solo puedes confirmar vinculaciones para Jefes Regionales de tu provincia.',
                        'code' => 403
                    ], 403);
                }
            }
            // 2. Jefe Regional: Solo puede vincular Jefes Distritales de su región
            elseif ($isRegional) {
                $userRegionId = $performer->regionUsuario?->region_id;
                
                $isTargetDistritalInMyRegion = $matchingUser->hasRole('jefe_distrital') && 
                                             $matchingUser->distritoUsuario?->distrito?->region_id === $userRegionId;

                if (!$isTargetDistritalInMyRegion) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Regional, solo puedes confirmar vinculaciones para Jefes Distritales de tu región.',
                        'code' => 403
                    ], 403);
                }
            }
            // 3. Jefe Distrital: Solo puede vincular Equipo de Conducción de su distrito
            elseif ($isDistrital) {
                $userDistId = $performer->distritoUsuario?->departamento_id;
                
                // Cargamos movimientos activos para validar contra la Persona (no el Usuario, que aún no tiene roles)
                $persona->load(['movimientosCupofActivos.cupof.escuela.localidad', 'movimientosCupofActivos.cupof.escalafon', 'movimientosCupofActivos.cupof.puestoTipo']);
                
                $isTargetConduccionInMyDistrict = false;
                foreach ($persona->movimientosCupofActivos as $movimiento) {
                    $escuela = $movimiento->cupof->escuela;
                    if ($escuela->localidad?->departamento_id === $userDistId) {
                        $roleName = $this->cupofService->mapCupofToRole($movimiento->cupof);
                        if (in_array($roleName, ['director', 'vicedirector', 'secretario', 'prosecretario'])) {
                            $isTargetConduccionInMyDistrict = true;
                            break;
                        }
                    }
                }

                if (!$isTargetConduccionInMyDistrict) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Distrital, solo puedes confirmar vinculaciones para el Equipo de Conducción de escuelas dentro de tu distrito.',
                        'code' => 403
                    ], 403);
                }
            }
        }

        $persona->update(['usuario_id' => $matchingUser->id]);
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
        $isProvincial = $performer->hasRole('jefe_provincial');
        $isRegional = $performer->hasRole('jefe_regional');
        $isDistrital = $performer->hasRole('jefe_distrital');

        if (!$persona->usuario_id) {
            return response()->json(['error' => 'Esta persona no tiene ningún usuario vinculado.'], 422);
        }

        $linkedUser = $persona->usuario;
        $linkedUser->loadMissing(['roles', 'provinciaUsuario', 'regionUsuario.region', 'distritoUsuario']);

        // REGLAS JERÁRQUICAS DE DESVINCULACIÓN (Espejo de Vinculación)
        if (!$isSuperUser) {
            // 1. Jefe Provincial
            if ($isProvincial) {
                $userProvId = $performer->provinciaUsuario?->provincia_id;

                $isTargetRegionalInMyProv = $linkedUser->hasRole('jefe_regional') && 
                                           $linkedUser->regionUsuario?->region?->provincia_id === $userProvId;

                if (!$isTargetRegionalInMyProv) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Provincial, solo puedes desvincular Jefes Regionales de tu provincia.',
                        'code' => 403
                    ], 403);
                }
            }
            // 2. Jefe Regional
            elseif ($isRegional) {
                $userRegionId = $performer->regionUsuario?->region_id;

                $isTargetDistritalInMyRegion = $linkedUser->hasRole('jefe_distrital') && 
                                             $linkedUser->distritoUsuario?->distrito?->region_id === $userRegionId;

                if (!$isTargetDistritalInMyRegion) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Regional, solo puedes desvincular Jefes Distritales de tu región.',
                        'code' => 403
                    ], 403);
                }
            }
            // 3. Jefe Distrital
            elseif ($isDistrital) {
                $userDistId = $performer->distritoUsuario?->departamento_id;

                $hasConduccionRole = $linkedUser->hasAnyRole(['director', 'vicedirector', 'secretario', 'prosecretario']);

                $isTargetInMyDistrict = false;
                if ($hasConduccionRole) {
                    $isTargetInMyDistrict = $linkedUser->escuelaUsuarios()
                        ->whereHas('escuela.localidad', function($q) use ($userDistId) {
                            $q->where('departamento_id', $userDistId);
                        })
                        ->exists();
                }

                if (!$hasConduccionRole || !$isTargetInMyDistrict) {
                    return response()->json([
                        'error' => 'Restricción de Seguridad: Como Jefe Distrital, solo puedes desvincular el Equipo de Conducción de tu distrito.',
                        'code' => 403
                    ], 403);
                }
            }
        }

        $this->personaService->unlinkUser($persona);

        return response()->json([
            'message' => 'Usuario desvinculado con éxito.'
        ]);
    }

    /**
     * Assigns the Jefe Provincial role to a persona.
     */
    public function assignJefeProvincial(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        if (!auth()->user()->hasRole('superuser') && !$performer->es_administrador) {
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
        
        // REGLA ESTRICTA: Sólo un Jefe Provincial o un SuperUsuario puede asignar Jefe Regional.
        if (!$performer->hasRole('jefe_provincial') && !$performer->hasRole('superuser') && !$performer->es_administrador) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Jefe Provincial o un SuperUsuario puede asignar el rol de Jefe Regional.'], 403);
        }

        $request->validate([
            'region_id' => 'required|exists:regions,id'
        ]);

        // Validaciones jerárquicas geográficas
        if (!$performer->hasRole('superuser') && !$performer->es_administrador) {
            $region = \App\Models\Region::find($request->region_id);
            if ($region->provincia_id !== $performer->provinciaUsuario->provincia_id) {
                return response()->json(['error' => 'Acceso Denegado: Solo puedes asignar Jefes Regionales para regiones de tu propia provincia.'], 403);
            }
        }

        // EVITAR DUPLICADOS: Verificar si ya tiene asignada exactamente esta misma región
        if ($persona->usuario && $persona->usuario->hasRole('jefe_regional')) {
            $regionActual = $persona->usuario->regionUsuario;
            if ($regionActual && $regionActual->region_id == $request->region_id) {
                return response()->json([
                    'error' => 'Esta persona ya tiene asignada la región seleccionada.',
                    'code'  => 422
                ], 422);
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
        
        // REGLA ESTRICTA: Solo un Jefe Provincial, Jefe Regional o un SuperUsuario puede asignar el rol de Jefe Distrital.
        if (!$performer->hasRole('jefe_provincial') && !$performer->hasRole('jefe_regional') && !$performer->hasRole('superuser') && !$performer->es_administrador) {
            return response()->json(['error' => 'Acceso Denegado: Solo un Jefe Provincial, Jefe Regional o un SuperUsuario puede asignar el rol de Jefe Distrital.'], 403);
        }

        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id'
        ]);

        // Cargar la relación 'region' para poder verificar la provincia
        $departamento = \App\Models\Departamento::with('region')->find($request->departamento_id);

        // Validaciones jerárquicas geográficas
        //$departamento = \App\Models\Departamento::find($request->departamento_id);
        //****************** */

          // Validaciones jerárquicas geográficas (Omitidas para Superusuarios)
        if (!$performer->hasRole('superuser') && !$performer->es_administrador) {

        // CASO 1: Si es Jefe Regional, validamos contra su Región Educativa
            if ($performer->hasRole('jefe_regional')) {
                $performer->loadMissing('regionUsuario');
                if (!$performer->regionUsuario || $departamento->region_id !== $performer->regionUsuario->region_id) {
                    return response()->json([
                        'error' => 'Acceso Denegado: Solo puedes asignar Jefes Distritales para departamentos dentro de tu Región Educativa.
    '
                    ], 403);
                }
            }

            // CASO 2: Si es Jefe Provincial, validamos contra su Provincia
            elseif ($performer->hasRole('jefe_provincial')) {
                $performer->loadMissing('provinciaUsuario');
                $provinciaId = $performer->provinciaUsuario?->provincia_id;

                if (!$provinciaId || $departamento->region?->provincia_id !== $provinciaId) {
                    return response()->json([
                        'error' => 'Acceso Denegado: Solo puedes asignar Jefes Distritales para departamentos dentro de tu Provincia.'
                    ], 403);
                }
            }
        }
        
        
        //****************** */
        /*if (!$performer->hasRole('superuser') && !$performer->es_administrador) {
            // El departamento debe pertenecer a la región del Jefe Regional
            $performer->loadMissing('regionUsuario.region');
            if (!$performer->regionUsuario || $departamento->region_id !== $performer->regionUsuario->region_id) {
                return response()->json(['error' => 'Acceso Denegado: Solo puedes asignar Jefes Distritales para departamentos dentro de tu Región Educativa.'], 403);
            }
        }*/

        // Verificar si la persona ya tiene asignado exactamente este mismo distrito
        if ($persona->usuario && $persona->usuario->hasRole('jefe_distrital')) {
            $distritoActual = $persona->usuario->distritoUsuario;
            if ($distritoActual && $distritoActual->departamento_id == $request->departamento_id) {
                return response()->json([
                    'error' => 'Esta persona ya tiene asignado el distrito seleccionado.',
                    'code'  => 422
                ], 422);
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
        if (!auth()->user()->hasRole('superuser') && !$performer->es_administrador) {
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
        if (!$performer->hasRole('superuser') && !$performer->es_administrador) {
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
