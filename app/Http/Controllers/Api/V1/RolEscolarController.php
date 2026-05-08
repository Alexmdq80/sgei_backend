<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\RoleResource;

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
            ->whereNotIn('name', ['superuser', 'jefe_distrital', 'supervisor_curricular'])
            ->get();
            
        return response()->json(RoleResource::collection($roles));
    }
}
