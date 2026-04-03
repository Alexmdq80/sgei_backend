<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RolEscolar;
use Illuminate\Http\Request;

class RolEscolarController extends Controller
{
    /**
     * Display a listing of the school roles.
     */
    public function index()
    {
        return response()->json(RolEscolar::all());
    }
}
