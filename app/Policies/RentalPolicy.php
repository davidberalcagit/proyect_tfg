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
        if ($user->hasRole('admin')) return true;
        if (!$user->customer) return false;

        return $user->customer->id === $rental->id_cliente ||
               $user->customer->id === $rental->car->id_vendedor;
    }

    public function create(User $user): bool
    {
        return $user->customer !== null;
    }
}
