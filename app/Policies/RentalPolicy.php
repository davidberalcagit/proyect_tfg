<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Rental $rental): bool
    {
        if (!$user->customer) {
            return false;
        }

        if ($user->customer->id === $rental->id_cliente) {
            return true;
        }

        if ($user->customer->id === $rental->car->id_vendedor) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->customer) {
            return true;
        }

        return false;
    }
}
