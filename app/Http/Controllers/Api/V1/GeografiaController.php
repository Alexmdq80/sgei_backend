<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provincia;
use App\Models\Departamento;
use App\Models\Localidad;
use App\Http\Requests\Api\V1\GeografiaRequest;
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
     * List departments by province or educational region.
     */
    public function departamentos(Request $request): JsonResponse
    {
        $query = Departamento::orderBy('nombre');

        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        } elseif ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        return response()->json($query->get(['id', 'nombre', 'region_id']));
    }

    /**
     * List localities by department or educational region.
     */
    public function localidades(Request $request): JsonResponse
    {
        $query = Localidad::orderBy('nombre');

        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->departamento_id);
        } elseif ($request->filled('region_id')) {
            // Filtra localidades cuyos departamentos pertenecen a la región
            $depIds = Departamento::where('region_id', $request->region_id)->pluck('id');
            $query->whereIn('departamento_id', $depIds);
        }

        return response()->json($query->get(['id', 'nombre', 'departamento_id']));
    }

    /**
     * List educational regions.
     */
    public function regiones(GeografiaRequest $request): JsonResponse
    {
        $query = \App\Models\Region::query();

        if ($request->has('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }

        $regiones = $query->orderBy('numero')->get(['id', 'numero', 'provincia_id']);
        return response()->json($regiones);
    }
}
