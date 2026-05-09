<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provincia;
use App\Models\Departamento;
use App\Models\Localidad;
use App\Http\Requests\Api\V1\GeografiaRequest;
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
    public function departamentos(GeografiaRequest $request): JsonResponse
    {
        $departamentos = Departamento::where('provincia_id', $request->provincia_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
            
        return response()->json($departamentos);
    }

    /**
     * List localities by department.
     */
    public function localidades(GeografiaRequest $request): JsonResponse
    {
        $localidades = Localidad::where('departamento_id', $request->departamento_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
            
        return response()->json($localidades);
    }

    /**
     * List all educational regions.
     */
    public function regiones(): JsonResponse
    {
        $regiones = \App\Models\Region::orderBy('numero')->get(['id', 'numero']);
        return response()->json($regiones);
    }
}
