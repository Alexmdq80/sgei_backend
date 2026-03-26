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
        $query = Usuario::query()->with(['persona', 'documentoTipo']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('documento_numero', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user (Administrative).
     */
    public function create(array $data): Usuario
    {
        $data['password'] = Hash::make($data['password'] ?? 'sgei1234'); // Default password if not provided
        $data['verification_token'] = Str::random(60);

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
     */
    public function linkToPersona(Usuario $user): void
    {
        if (!$user->hasVerifiedEmail()) {
            return;
        }

        if (!$user->documento_tipo_id || !$user->documento_numero) {
            return;
        }

        $persona = Persona::where('documento_tipo_id', $user->documento_tipo_id)
                          ->where('documento_numero', $user->documento_numero)
                          ->whereNull('usuario_id') // Solo vincular si no tiene usuario
                          ->first();

        if ($persona) {
            $persona->update(['usuario_id' => $user->id]);
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

        if (!$user->verification_token) {
            $user->update(['verification_token' => Str::random(60)]);
        }

        $user->notify(new VerifyEmailNotification($user->verification_token));
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateProfile(Usuario $user, array $data): Usuario
    {
        $user->update($data);
        $this->linkToPersona($user);
        return $user;
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

        // Store new avatar in 'public/avatars'
        $path = $avatar->store('avatars', 'public');
        
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
