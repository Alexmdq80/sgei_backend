<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Nacion;
use App\Services\NacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NacionController extends Controller
{
    public function __construct(
        protected NacionService $nacionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);
        
        return response()->json($this->nacionService->getAll($search, (int)$perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_georef' => 'nullable|integer',
            'continente_id' => 'required|exists:continentes,id',
            'nombre' => 'required|string|max:255|unique:nacions,nombre',
            'nacionalidad' => 'required|string|max:255'
        ]);

        $item = $this->nacionService->create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->nacionService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nacion $nacion): JsonResponse
    {
        $validated = $request->validate([
            'id_georef' => 'nullable|integer',
            'continente_id' => 'required|exists:continentes,id',
            'nombre' => 'required|string|max:255|unique:nacions,nombre,' . $nacion->id,
            'nacionalidad' => 'required|string|max:255'
        ]);

        $updated = $this->nacionService->update($nacion, $validated);
        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nacion $nacion): JsonResponse
    {
        $this->nacionService->delete($nacion);
        return response()->json(null, 204);
    }
}
