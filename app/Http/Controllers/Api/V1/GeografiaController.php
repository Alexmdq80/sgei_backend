<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provincia;
use App\Models\Departamento;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeografiaController extends Controller
{
    /**
     * List all provinces.
     */
    public function provincias(): JsonResponse
    {
        $provincias = Provincia::orderBy('nombre')->get(['id', 'nombre']);
        return response()->json($provincias);
    }

    /**
     * List departments by province.
     */
    public function departamentos(Request $request): JsonResponse
    {
        $request->validate(['provincia_id' => 'required|integer|exists:provincias,id']);
        
        $departamentos = Departamento::where('provincia_id', $request->provincia_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
            
        return response()->json($departamentos);
    }

    /**
     * List localities by department.
     */
    public function localidades(Request $request): JsonResponse
    {
        $request->validate(['departamento_id' => 'required|integer|exists:departamentos,id']);
        
        $localidades = Localidad::where('departamento_id', $request->departamento_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
            
        return response()->json($localidades);
    }
}
