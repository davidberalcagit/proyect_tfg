<?php

namespace App\Policies;

use App\Models\Customers;
use App\Models\User;

class CustomersPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->can('view customers data')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Customers $customer): bool
    {
        if ($user->id === $customer->id_usuario) {
        return true;
    }

        if ($user->can('view customers data')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Customers $customer): bool
    {
        if ($user->id === $customer->id_usuario) {
            return true;
        }

        return false;
    }
}
