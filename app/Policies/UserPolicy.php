<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->can('view users data')) {
            return true;
        }

        return false;
    }

    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        if ($user->can('view users data')) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->can('manage users')) {
            return true;
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        if ($user->can('manage users')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        if ($model->hasRole('admin')) {
            return false;
        }

        if ($user->can('manage users')) {
            return true;
        }

        return false;
    }

    public function ban(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('admin')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }
}
