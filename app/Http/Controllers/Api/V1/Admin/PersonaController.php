<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PersonaResource;
use App\Services\UserService;
use App\Http\Requests\Api\V1\Admin\PersonaRequest;

class PersonaController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the people (Agentes/Personas).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Persona::with(['documentoTipo', 'usuario', 'nacionalidad', 'genero']);

        $performer = auth()->user();
        $isSuperUser = $performer->hasRole('superuser');

        // 1. Filtro: Solo Agentes (personas con movimientos de CUPOF)
        if ($request->boolean('only_agents')) {
            $query->whereHas('movimientosCupof', function($q) use ($performer, $isSuperUser, $request) {
                // Si no es superusuario, solo ve agentes de sus escuelas
                if (!$isSuperUser) {
                    $schoolIds = $performer->escuelaUsuarios()->pluck('escuela_id')->toArray();
                    $q->whereHas('cupof', function($sq) use ($schoolIds) {
                        $sq->whereIn('escuela_id', $schoolIds);
                    });
                }
                
                // Filtro adicional por escuela específica si se solicita
                if ($request->filled('escuela_id')) {
                    $q->whereHas('cupof', function($sq) use ($request) {
                        $sq->where('escuela_id', $request->escuela_id);
                    });
                }
            });
        }

        // 2. Búsqueda por nombre, apellido o documento
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('documento_numero', 'like', "%{$search}%");
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
        return new PersonaResource($persona->load([
            'documentoTipo', 
            'usuario', 
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
}
