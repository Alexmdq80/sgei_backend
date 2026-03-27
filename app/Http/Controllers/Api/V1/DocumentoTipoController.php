<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentoTipo;
use Illuminate\Http\JsonResponse;

class DocumentoTipoController extends Controller
{
    /**
     * Listado de tipos de documento vigentes.
     * Devuelve los tipos necesarios para la identificación de personas y login.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tipos = DocumentoTipo::where('vigente', true)
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        return response()->json([
            'data' => $tipos
        ]);
    }
}
