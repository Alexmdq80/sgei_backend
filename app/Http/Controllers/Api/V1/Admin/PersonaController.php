<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PersonaResource;

class PersonaController extends Controller
{
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
            'cuil' => 'nullable|string|max:13'
        ]);

        if (!empty($validated['cuil'])) {
            $parts = explode('-', str_replace([' ', '.'], '', $validated['cuil']));
            if (count($parts) === 3) {
                $validated['CUIL_prefijo'] = $parts[0];
                $validated['CUIL_sufijo'] = $parts[2];
            }
        }

        $matchingUser = \App\Models\Usuario::where('documento_tipo_id', $validated['documento_tipo_id'])
            ->where('documento_numero', $validated['documento_numero'])
            ->whereNotNull('email_verified_at')
            ->first();

        if ($matchingUser && !$matchingUser->persona) {
            $validated['usuario_id'] = $matchingUser->id;
        }

        $persona = Persona::create($validated);

        return response()->json([
            'message' => 'Persona registrada con éxito en el padrón.',
            'data' => new PersonaResource($persona)
        ], 201);
    }

    /**
     * Intenta vincular una persona existente con un usuario por DNI.
     */
    public function tryLinkUser(Persona $persona): \Illuminate\Http\JsonResponse
    {
        if ($persona->usuario_id) {
            return response()->json(['error' => 'Esta persona ya tiene un usuario vinculado.'], 422);
        }

        $matchingUser = \App\Models\Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
            ->where('documento_numero', $persona->documento_numero)
            ->first();

        if (!$matchingUser) {
            return response()->json(['error' => 'No se encontró ningún usuario con el mismo documento.'], 404);
        }

        if (!$matchingUser->email_verified_at) {
            return response()->json(['error' => 'Se encontró un usuario, pero aún no ha verificado su cuenta de correo electrónico.'], 422);
        }

        if ($matchingUser->persona) {
            return response()->json(['error' => 'El usuario coincidente ya está vinculado a otra persona.'], 422);
        }

        $persona->update(['usuario_id' => $matchingUser->id]);

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
