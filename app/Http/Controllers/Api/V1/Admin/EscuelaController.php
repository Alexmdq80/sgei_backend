<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escuela;
use App\Services\EscuelaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EscuelaController extends Controller
{
    protected EscuelaService $escuelaService;

    public function __construct(EscuelaService $escuelaService)
    {
        $this->escuelaService = $escuelaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        return response()->json($this->escuelaService->getAllAdmin($search));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'numero' => 'required|string|max:50',
            'cue_anexo' => 'required|string|max:50|unique:escuelas,cue_anexo',
            'localidad_id' => 'required|exists:georef_localidads,id',
            'ambito_id' => 'nullable|exists:ambitos,id',
            'dependencia_id' => 'nullable|exists:dependencias,id',
            'sector_id' => 'nullable|exists:escuela_tipos,id', // En la DB se usa sector_id pero apunta a escuela_tipos
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuela = $this->escuelaService->create($request->all());
        return response()->json($escuela, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Escuela $escuela): JsonResponse
    {
        return response()->json($escuela->load([
            'localidad.departamento.provincia', 
            'ambito', 
            'dependencia', 
            'sector', 
            'modalidadesNiveles'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Escuela $escuela): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'numero' => 'required|string|max:50',
            'cue_anexo' => 'required|string|max:50|unique:escuelas,cue_anexo,' . $escuela->id,
            'localidad_id' => 'required|exists:georef_localidads,id',
            'ambito_id' => 'nullable|exists:ambitos,id',
            'dependencia_id' => 'nullable|exists:dependencias,id',
            'sector_id' => 'nullable|exists:escuela_tipos,id',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'code' => 400], 400);
        }

        $escuela = $this->escuelaService->update($escuela, $request->all());
        return response()->json($escuela);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Escuela $escuela): JsonResponse
    {
        $this->escuelaService->delete($escuela);
        return response()->json(null, 204);
    }
}
