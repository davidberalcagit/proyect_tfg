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
        if (!$user->customer) {
            return false;
        }

        if ($user->customer->id === $sale->id_comprador) {
            return true;
        }

        if ($user->customer->id === $sale->id_vendedor) {
            return true;
        }

        return false;
    }
}
