<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('rental can be created with custom status', function () {
    $status = RentalStatus::create(['nombre' => 'Estado Personalizado']);
    $statusId = $status->id;

    $this->assertDatabaseHas('rental_statuses', ['id' => $statusId]);

    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create(['id_estado' => 3]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => now(),
        'fecha_fin' => now()->addDays(1),
        'precio_total' => 100,
        'id_estado' => $statusId
    ]);

    $this->assertDatabaseHas('rentals', [
        'id' => $rental->id,
        'id_estado' => $statusId
    ]);
});
