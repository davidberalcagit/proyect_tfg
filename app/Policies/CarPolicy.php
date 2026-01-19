<?php

namespace App\Policies;

use App\Models\Cars;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view cars');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cars $car): bool
    {
        return $user->can('view cars');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create cars') && $user->customer;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cars $car): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Solo el dueño puede editar
        if ($user->customer && $user->customer->id === $car->id_vendedor) {
            // Y solo si está pendiente de revisión (estado 4)
            return $car->id_estado === 4;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cars $car): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->customer && $user->customer->id === $car->id_vendedor) {
            return $car->id_estado === 4;
        }

        return false;
    }

    /**
     * Determine whether the user can rent the car.
     */
    public function rent(User $user, Cars $car): bool
    {
        // No puede alquilar su propio coche
        if ($user->customer && $user->customer->id === $car->id_vendedor) {
            return false;
        }

        // El coche debe estar en estado "En Alquiler" (3)
        return $car->id_estado === 3;
    }
}
