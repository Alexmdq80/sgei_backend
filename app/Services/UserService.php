<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get a paginated list of users with optional filters.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Usuario::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('apellido', 'like', '%' . $filters['search'] . '%')
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
        return Usuario::create($data);
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateProfile(Usuario $user, array $data): Usuario
    {
        $user->update($data);
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
