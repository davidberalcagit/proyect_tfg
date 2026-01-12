<?php

namespace App\Actions\Fortify;
use App\Models\User;
use App\Models\Customers;
class CustomerService
{
    public function createForUser(User $user, array $data): Customers
    {
        return $user->customer()->create([
            'id_usuario' => $user->id,
            'id_entidad' => $data['id_entidad'],
            'nombre_contacto' => $user->name,
            'telefono' => $data['telefono'],
        ]);
    }
}
