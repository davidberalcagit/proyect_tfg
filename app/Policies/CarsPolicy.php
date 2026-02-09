<?php

namespace App\Policies;

use App\Models\Cars;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarsPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->can('view cars');
    }

    public function view(User $user, Cars $car): bool
    {
        return $user->can('view cars');
    }


    public function create(User $user): bool
    {
        return $user->can('create cars') && $user->customer;
    }


    public function update(User $user, Cars $car): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->customer && $user->customer->id === $car->id_vendedor) {
            return $car->id_estado === 4;
        }

        return false;
    }

    public function delete(User $user, Cars $car): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->customer && $user->customer->id === $car->id_vendedor) {

            return true;
        }

        return false;
    }


    public function rent(User $user, Cars $car): bool
    {
        if ($user->customer && $user->customer->id === $car->id_vendedor) {
            return false;
        }

        return $car->id_estado === 3;
    }
}
