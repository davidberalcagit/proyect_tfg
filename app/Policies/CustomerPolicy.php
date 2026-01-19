<?php

namespace App\Policies;

use App\Models\Customers;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view customers data');
    }

    public function view(User $user, Customers $customer): bool
    {
        if ($user->hasRole(['admin', 'supervisor'])) return true;

        return $user->id === $customer->id_usuario;
    }

    public function update(User $user, Customers $customer): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->id === $customer->id_usuario;
    }
}
