<?php

namespace App\Policies;

use App\Models\Cars;
use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Offer $offer): bool
    {
        if (!$user->customer) return false;

        return $user->customer->id === $offer->id_comprador ||
               $user->customer->id === $offer->id_vendedor;
    }

    public function create(User $user, Cars $car): bool
    {
        if (!$user->customer) return false;
        if (!$user->can('buy cars')) return false;

        if ($user->customer->id === $car->id_vendedor) {
            return false;
        }

        if ($car->id_estado !== 1) {
            return false;
        }

        return true;
    }

    public function update(User $user, Offer $offer): bool
    {
        if (!$user->customer) return false;

        if ($user->customer->id === $offer->id_comprador) {
            return $offer->estado === 'pending';
        }

        if ($user->customer->id === $offer->id_vendedor) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Offer $offer): bool
    {
        if (!$user->customer) return false;

        return $user->customer->id === $offer->id_comprador && $offer->estado === 'pending';
    }

    public function accept(User $user, Offer $offer): bool
    {
        return $user->customer && $user->customer->id === $offer->id_vendedor;
    }

    public function reject(User $user, Offer $offer): bool
    {
        return $user->customer && $user->customer->id === $offer->id_vendedor;
    }
}
