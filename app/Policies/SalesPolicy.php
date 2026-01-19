<?php

namespace App\Policies;

use App\Models\Sales;
use App\Models\User;

class SalesPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Sales $sale): bool
    {
        if ($user->hasRole('admin')) return true;
        if (!$user->customer) return false;

        return $user->customer->id === $sale->id_comprador ||
               $user->customer->id === $sale->id_vendedor;
    }
}
