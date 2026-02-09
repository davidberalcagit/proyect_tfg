<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

test('customer show displays profile and cars', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $response = $this->get(route('seller.show', $customer));

    $response->assertStatus(200);
    $response->assertViewIs('seller.show');
    $response->assertSee($customer->nombre_contacto);
    $response->assertSee($car->title);
});
