<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Get a paginated list of users with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Usuario::query()->with([
            'persona', 
            'documentoTipo', 
            'escuelaUsuarios.escuela', 
            'escuelaUsuarios.role',
            'roles'
        ]);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('documento_numero', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filtro por Escuela específica (ID o CUE)
        if (!empty($filters['escuela_id'])) {
            $query->whereHas('escuelaUsuarios', function ($q) use ($filters) {
                $q->where('escuela_id', $filters['escuela_id']);
            });
        }

        if (!empty($filters['cue_anexo'])) {
            $query->whereHas('escuelaUsuarios.escuela', function ($q) use ($filters) {
                $q->where('cue_anexo', $filters['cue_anexo']);
            });
        }

        // Filtro por Estado de Vinculación
        if (!empty($filters['vinculation'])) {
            if ($filters['vinculation'] === 'vinculated') {
                $query->whereHas('escuelaUsuarios', function ($q) {
                    $q->whereNotNull('verified_at');
                });
            } elseif ($filters['vinculation'] === 'pending') {
                $query->whereHas('escuelaUsuarios', function ($q) {
                    $q->whereNull('verified_at');
                });
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user (Administrative).
     */
    public function create(array $data): Usuario
    {
        // Asegurar contraseña: si está vacía, usar el default del sistema
        $password = !empty($data['password']) ? $data['password'] : 'Sgei!2026_Admin';
        $data['password'] = Hash::make($password);
        
        $data['verification_token'] = Str::random(60);
        $data['verification_token_created_at'] = now();

        $user = Usuario::create($data);

        // Si el admin lo crea ya verificado (o si por alguna razón ya tiene email_verified_at)
        if ($user->hasVerifiedEmail()) {
            $this->linkToPersona($user);
        } else {
            // Enviar notificación de verificación
            $user->notify(new VerifyEmailNotification($user->verification_token));
        }

        return $user;
    }

    /**
     * Link the user to a persona if identification matches and email is verified.
     * Requires match: documento_tipo_id and documento_numero.
     */
    public function linkToPersona(Usuario $user): void
    {
        if (!$user->hasVerifiedEmail()) {
            return;
        }

        if (!$user->documento_tipo_id || !$user->documento_numero) {
            return;
        }

        // Search for a persona with matching documents
        $persona = Persona::where('documento_tipo_id', $user->documento_tipo_id)
                          ->where('documento_numero', $user->documento_numero)
                          ->whereNull('usuario_id') // Only link if not already linked
                          ->first();

        if ($persona) {
            $persona->update(['usuario_id' => $user->id]);
        }
    }

    /**
     * Link a persona to an existing user if a match is found.
     * Useful when creating or updating a persona.
     */
    public function linkPersonaToUser(Persona $persona): void
    {
        if ($persona->usuario_id) {
            return;
        }

        if (!$persona->documento_tipo_id || !$persona->documento_numero) {
            return;
        }

        // Search for a user with matching documents
        $user = Usuario::where('documento_tipo_id', $persona->documento_tipo_id)
                       ->where('documento_numero', $persona->documento_numero)
                       ->whereNotNull('email_verified_at') // Only link to verified users
                       ->first();

        if ($user) {
            // Check if this user is already linked to another persona (unlikely but possible)
            $existingLink = Persona::where('usuario_id', $user->id)->exists();
            if (!$existingLink) {
                $persona->update(['usuario_id' => $user->id]);
            }
        }
    }

    /**
     * Resend the verification notification to the user.
     */
    public function resendVerification(Usuario $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->forceFill([
            'verification_token' => Str::random(60),
            'verification_token_created_at' => now(),
        ])->save();

        $user->notify(new VerifyEmailNotification($user->verification_token));
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateProfile(Usuario $user, array $data): Usuario
    {
        // Handle password update if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Don't try to update password if empty
        }

        // Check for email change
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $performer = \Illuminate\Support\Facades\Auth::user();
            $isAdmin = $performer?->es_administrador || $performer?->hasRole('superuser');

            if (!$isAdmin && !$user->canChangeEmail()) {
                throw ValidationException::withMessages([
                    'email' => ['Has alcanzado el límite máximo de cambios de correo electrónico (3).'],
                ]);
            }

            // Prepare verification reset
            $data['email_verified_at'] = null;
            $data['verification_token'] = Str::random(60);
            $data['verification_token_created_at'] = now();
            $data['email_set_at'] = now();
            $data['email_correction_attempts'] = $user->email_correction_attempts + 1;
            $data['estado'] = 'email_pendiente';
            
            // Notify the user about the new verification
            $user->update($data);
            $user->notify(new VerifyEmailNotification($user->verification_token));
        } else {
            $user->update($data);
        }

        $this->linkToPersona($user);
        return $user;
    }

    /**
     * Assign or revoke a role from a user.
     */
    public function toggleRole(Usuario $user, string $roleName): void
    {
        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
        } else {
            $user->assignRole($roleName);
        }
    }

    /**
     * Delete a user (Soft Delete).
     */
    public function delete(Usuario $user): bool
    {
        return $user->delete();
    }

    /**
     * Update the user's avatar and delete the old one if it exists.
     */
    public function updateAvatar(Usuario $user, UploadedFile $avatar): string
    {
        // Delete old avatar if exists
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // Create a custom filename: user_id_timestamp.extension
        $timestamp = time();
        $extension = $avatar->getClientOriginalExtension();
        $filename = "{$user->id}_{$timestamp}.{$extension}";

        // Store new avatar in 'public/avatars' with the custom filename
        $path = $avatar->storeAs('avatars', $filename, 'public');
        
        $user->update(['avatar_path' => $path]);

        return asset('storage/' . $path);
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Usuario $user): void
    {
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update(['avatar_path' => null]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Usuario $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
