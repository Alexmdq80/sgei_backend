<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgenteController extends Controller
{
    /**
     * List and search agents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Agente::with('persona');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('persona', function($q) use ($search) {
                $q->where('documento_numero', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%");
            })->orWhere('legajo', 'like', "%{$search}%");
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Create an agent from a persona.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'persona_id' => 'required|exists:personas,id|unique:agentes,persona_id',
            'legajo' => 'nullable|string|unique:agentes,legajo',
            'fecha_ingreso_sistema' => 'nullable|date',
        ]);

        $agente = Agente::create($validated);
        return response()->json($agente->load('persona'), 201);
    }
}
