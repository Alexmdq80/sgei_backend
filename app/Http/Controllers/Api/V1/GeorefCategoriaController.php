<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GeorefCategoria;
use App\Services\GeorefCategoriaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class GeorefCategoriaController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected GeorefCategoriaService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 15);
        
        return response()->json($this->service->getAll($search, $perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:georef_categorias,nombre',
                'orden' => 'nullable|integer',
                'vigente' => 'boolean'
            ]);

            $item = $this->service->create($validated);
            return response()->json($item, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->validator->errors()->first(),
                'code' => 400
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al crear la categoría Georef.',
                'code' => 400
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->service->getById($id));
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Categoría Georef no encontrada.',
                'code' => 404
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeorefCategoria $georefCategoria): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:georef_categorias,nombre,' . $georefCategoria->id,
                'orden' => 'nullable|integer',
                'vigente' => 'boolean'
            ]);

            $updated = $this->service->update($georefCategoria, $validated);
            return response()->json($updated);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->validator->errors()->first(),
                'code' => 400
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al actualizar la categoría Georef.',
                'code' => 400
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeorefCategoria $georefCategoria): JsonResponse
    {
        try {
            $this->service->delete($georefCategoria);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al eliminar la categoría Georef.',
                'code' => 400
            ], 400);
        }
    }
}
