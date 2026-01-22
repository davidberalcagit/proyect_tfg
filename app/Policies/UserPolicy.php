<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'soporte']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasRole(['admin', 'soporte'])) {
            return true;
        }
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        // Soporte puede editar (opcional, si quieres)
        if ($user->hasRole('soporte')) {
            return true;
        }
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin puede borrar a cualquiera (menos a sí mismo)
        if ($user->hasRole('admin')) {
            return $user->id !== $model->id;
        }

        // Soporte puede borrar usuarios (pero no a admins ni a sí mismo)
        if ($user->hasRole('soporte')) {
            return $user->id !== $model->id && !$model->hasRole('admin');
        }

        // El usuario puede borrarse a sí mismo
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can ban another user.
     */
    public function ban(User $user, User $model): bool
    {
        return $user->hasRole('admin') &&
               $user->id !== $model->id &&
               !$model->hasRole('admin');
    }
}
