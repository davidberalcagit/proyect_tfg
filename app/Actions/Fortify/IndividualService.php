<?php

namespace App\Actions\Fortify;

use App\Models\Customers;
use App\Models\Individuals;

class IndividualService
{
    public function createForCustomer(Customers $customer, array $data): Individuals
    {
        return $customer->individual()->create([
            'dni' => $data['dni'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
        ]);
    }
}
