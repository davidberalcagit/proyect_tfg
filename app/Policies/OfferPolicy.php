<?php

namespace App\Policies;

use App\Models\Cars;
use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Filtrado en controlador
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

        // No puede ofertar por su propio coche
        if ($user->customer->id === $car->id_vendedor) {
            return false;
        }

        // El coche debe estar en venta (1)
        if ($car->id_estado !== 1) {
            return false;
        }

        return true;
    }

    public function update(User $user, Offer $offer): bool
    {
        if (!$user->customer) return false;

        // Comprador puede editar si está pendiente
        if ($user->customer->id === $offer->id_comprador) {
            return $offer->estado === 'pending';
        }

        // Vendedor puede "editar" (aceptar/rechazar) si es el dueño
        if ($user->customer->id === $offer->id_vendedor) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Offer $offer): bool
    {
        if (!$user->customer) return false;

        // Solo el comprador puede borrar (cancelar) su oferta
        return $user->customer->id === $offer->id_comprador && $offer->estado === 'pending';
    }

    // Métodos específicos para aceptar/rechazar (opcional, pero limpio)
    public function accept(User $user, Offer $offer): bool
    {
        return $user->customer && $user->customer->id === $offer->id_vendedor;
    }

    public function reject(User $user, Offer $offer): bool
    {
        return $user->customer && $user->customer->id === $offer->id_vendedor;
    }
}
