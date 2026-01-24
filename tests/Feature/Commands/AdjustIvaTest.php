<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('adjust iva command adds iva to specific car', function () {
    $car = Cars::factory()->create(['precio' => 100, 'id_estado' => 1]);

    $this->artisan('prices:adjust-iva', ['action' => 'give', 'target' => $car->id])
         ->expectsOutput("Aplicando acción 'give' al coche ID: {$car->id}")
         ->assertExitCode(0);

    $car->refresh();
    expect($car->precio)->toEqual(121); // 100 * 1.21
});

test('adjust iva command removes iva from specific car', function () {
    $car = Cars::factory()->create(['precio' => 121, 'id_estado' => 1]);

    $this->artisan('prices:adjust-iva', ['action' => 'remove', 'target' => $car->id])
         ->assertExitCode(0);

    $car->refresh();
    expect($car->precio)->toEqual(100); // 121 / 1.21
});

test('adjust iva command handles invalid action', function () {
    $this->artisan('prices:adjust-iva', ['action' => 'invalid', 'target' => 1])
         ->expectsOutput('La acción debe ser "give" (sumar IVA) o "remove" (quitar IVA).')
         ->assertExitCode(1);
});

test('adjust iva command handles invalid target', function () {
    $this->artisan('prices:adjust-iva', ['action' => 'give', 'target' => 'invalid'])
         ->expectsOutput('El objetivo debe ser "individual", "dealership" o un ID numérico.')
         ->assertExitCode(1);
});

test('adjust iva command updates cars by role', function () {
    $role = Role::firstOrCreate(['name' => 'individual']);
    $user = User::factory()->create();
    $user->assignRole($role);
    // Corrected user_id to id_usuario
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'precio' => 100,
        'id_estado' => 1
    ]);

    $this->artisan('prices:adjust-iva', ['action' => 'give', 'target' => 'individual'])
         ->assertExitCode(0);

    $car->refresh();
    expect($car->precio)->toEqual(121);
});
