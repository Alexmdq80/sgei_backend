<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Services\UserService;
use App\Services\CupofService;
use App\Services\PersonaService;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UsuarioPersonaController extends Controller
{
    protected UserService $userService;
    protected CupofService $cupofService;
    protected PersonaService $personaService;

    public function __construct(
        UserService $userService,
        CupofService $cupofService,
        PersonaService $personaService
    ) {
        $this->userService = $userService;
        $this->cupofService = $cupofService;
        $this->personaService = $personaService;
    }

    /**
     * Confirma la vinculación de un usuario con un registro del padrón.
     */
        public function confirmPersona(Request $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('confirmPersona', $usuario);

        $force = $request->boolean('force');

        if (!$usuario->hasVerifiedEmail() && !$force) {
            return response()->json([
                'error' => 'Operación Inválida: El usuario debe haber verificado su correo electrónico antes de que se pueda confirmar su vinculación con el padrón.',
                'code' => 422
            ], 422);
        }

        if ($usuario->persona) {
            $usuarioActualizado = DB::transaction(function () use ($usuario) {
                if ($usuario->estado !== 'activo') {
                    $usuario->update(['estado' => 'activo']);
                }

                $this->cupofService->syncAllRolesFromCupof($usuario);

                return $usuario->fresh(['persona', 'persona.escuelasPersonas.role']);
            });

            return response()->json([
                'message' => 'El usuario ya se encontraba vinculado y ahora ha sido activado.',
                'user' => new UsuarioResource($usuarioActualizado)
            ]);
        }

        // Buscar la persona que coincida (DNI + Email)
        $persona = Persona::where('documento_tipo_id', $usuario->documento_tipo_id)
            ->where('documento_numero', $usuario->documento_numero)
            ->whereHas('contacto', function ($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })
            ->whereNull('usuario_id')
            ->first();

        if (!$persona) {
            return response()->json(['error' => 'No se encontró ninguna persona en el padrón con datos coincidentes (DNI y Email) para confirmar.'], 404);
        }

        $usuarioActualizado = DB::transaction(function () use ($usuario, $persona) {
            $persona->update(['usuario_id' => $usuario->id]);
            $usuario->update(['estado' => 'activo']);

            $this->cupofService->syncAllRolesFromCupof($usuario);

            return $usuario->fresh(['persona', 'persona.escuelasPersonas.role']);
        });

        return response()->json([
            'message' => 'Vinculación con el padrón confirmada con éxito.',
            'user' => new UsuarioResource($usuarioActualizado)
        ]);
    }

    /**
     * Busca personas candidatas a vincularse con un usuario (mismo DNI + Email).
     */
    public function candidatosPersona(Usuario $usuario): JsonResponse
    {
        $performer = auth()->user();

        $this->authorize('manageCandidatos', $usuario);

        if ($usuario->persona) {
            return response()->json(['error' => 'Este usuario ya tiene una persona vinculada.'], 422);
        }

        $candidatos = $this->userService->getCandidatosPersona($usuario, $performer);

        return response()->json([
            'data' => $candidatos->map(fn ($p) => [
                'id' => $p->id,
                'nombre_completo' => "{$p->apellido}, {$p->nombre}",
                'documento_tipo' => $p->documentoTipo?->nombre,
                'documento_numero' => $p->documentoNumeroRaw(),
                'email' => $p->contacto?->email,
                'relaciones' => $this->getRelacionesCandidato($p),
            ])
        ]);
    }

    /**
     * Vincula una persona candidata a un usuario.
     */
    public function vincularPersona(Usuario $usuario, Persona $persona): JsonResponse
    {
        $this->authorize('vincularPersona', [$usuario, $persona]);

        $usuarioActualizado = DB::transaction(function () use ($usuario, $persona) {
            $persona->update(['usuario_id' => $usuario->id]);
            $usuario->update(['estado' => 'activo']);

            $this->cupofService->syncAllRolesFromCupof($usuario);

            return $usuario->fresh(['persona', 'persona.contacto', 'persona.escuelasPersonas.role']);
        });

        return response()->json([
            'message' => 'Persona vinculada con éxito al usuario.',
            'user' => new UsuarioResource($usuarioActualizado)
        ]);
    }

    /**
     * Desvincula la persona del usuario.
     */
    public function desvincularPersona(Usuario $usuario): JsonResponse
    {
        $this->authorize('desvincularPersona', $usuario);

        $persona = $usuario->persona;

        $usuarioActualizado = DB::transaction(function () use ($usuario, $persona) {
            $this->personaService->unlinkUser($persona);

            return $usuario->fresh(['persona', 'persona.contacto', 'persona.escuelasPersonas.role']);
        });

        return response()->json([
            'message' => 'Persona desvinculada con éxito del usuario.',
            'user' => new UsuarioResource($usuarioActualizado)
        ]);
    }

    /**
     * Helper: Obtiene las relaciones institucionales de un candidato.
     */
    private function getRelacionesCandidato(Persona $persona): array
    {
        $relaciones = [];

        foreach ($persona->movimientosCupofActivos as $mov) {
            $relaciones[] = "CUPOF: " . ($mov->cupof?->nombre_cargo ?? 'Cargo');
        }
        if ($persona->inscripcion) {
            $relaciones[] = "ESTUDIANTE";
        }
        foreach ($persona->vinculosComoAdulto as $v) {
            $relaciones[] = "VÍNCULO FAMILIAR";
        }

        return array_values(array_unique($relaciones));
    }
}
