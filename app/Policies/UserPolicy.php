<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'soporte']);
    }


    public function view(User $user, User $model): bool
    {
        if ($user->hasRole(['admin', 'soporte'])) {
            return true;
        }
        return $user->id === $model->id;
    }


    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'soporte']);
    }


    public function update(User $user, User $model): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('soporte')) {
            return true;
        }
        return $user->id === $model->id;
    }


    public function delete(User $user, User $model): bool
    {
        if ($user->hasRole('admin')) {
            return $user->id !== $model->id;
        }

        if ($user->hasRole('soporte')) {
            return $user->id !== $model->id && !$model->hasRole('admin');
        }

        return $user->id === $model->id;
    }


    public function ban(User $user, User $model): bool
    {
        return $user->hasRole('admin') &&
               $user->id !== $model->id &&
               !$model->hasRole('admin');
    }
}
