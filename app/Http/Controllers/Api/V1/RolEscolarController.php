<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RolEscolarController extends Controller
{
    /**
     * Display a listing of institutional roles (from Spatie).
     */
    public function index(): JsonResponse
    {
        // Solo devolvemos roles que tengan sentido institucional.
        // Excluimos explícitamente roles globales/administrativos de sistema.
        $roles = Role::where('guard_name', 'sanctum')
            ->get();

        return response()->json(RoleResource::collection($roles));
    }
}
