<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PersonaResource;
use App\Services\UserService;

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
        $query = Persona::with(['documentoTipo', 'usuario', 'nacionalidad']);

        // Búsqueda por nombre, apellido o documento
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // ... (validación y creación existente)
        $validated = $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'documento_tipo_id' => 'required|integer|exists:documento_tipos,id',
            'documento_numero' => 'required|string|max:20|unique:personas,documento_numero',
            'nacimiento_fecha' => 'nullable|date',
            'cuil' => 'nullable|string|max:13',
            'email' => 'nullable|email|max:255'
        ]);

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
            // El ContactoObserver disparará linkPersonaToUser automáticamente
        }

        return response()->json([
            'message' => 'Persona registrada con éxito en el padrón.',
            'data' => new PersonaResource($persona->fresh(['contacto', 'usuario']))
        ], 201);
    }

    /**
     * Update the specified person in storage.
     */
    public function update(Request $request, Persona $persona): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'documento_tipo_id' => 'required|integer|exists:documento_tipos,id',
            'documento_numero' => 'required|string|max:20|unique:personas,documento_numero,' . $persona->id,
            'nacimiento_fecha' => 'nullable|date',
            'cuil' => 'nullable|string|max:13',
            'email' => 'nullable|email|max:255'
        ]);

        $personaData = \Illuminate\Support\Arr::except($validated, ['email']);

        if (!empty($personaData['cuil'])) {
            $parts = explode('-', str_replace([' ', '.'], '', $personaData['cuil']));
            if (count($parts) === 3) {
                $personaData['CUIL_prefijo'] = $parts[0];
                $personaData['CUIL_sufijo'] = $parts[2];
            }
        }

        $persona->update($personaData);

        // Actualizar email de contacto
        if (isset($validated['email'])) {
            $persona->contacto()->updateOrCreate(
                ['persona_id' => $persona->id],
                ['email' => $validated['email']]
            );
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
