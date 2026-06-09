<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\TestDatabaseSeeder::class);
});

test('offer policy authorization', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

    $otherUser = User::factory()->create();
    $otherUser->assignRole('individual');
    $otherCustomer = Customers::factory()->create(['id_usuario' => $otherUser->id, 'id_entidad' => 1]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $offer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $otherCustomer->id,
        'id_vendedor' => $customer->id,
        'cantidad' => 5000,
        'estado' => 'pending'
    ]);

    expect($user->can('viewAny', Offer::class))->toBeTrue();

    expect($user->can('view', $offer))->toBeTrue();
    expect($otherUser->can('view', $offer))->toBeTrue();

    expect($otherUser->can('create', [Offer::class, $car]))->toBeTrue();

    expect($user->can('delete', $offer))->toBeFalse();
    expect($otherUser->can('delete', $offer))->toBeTrue();

    expect($user->can('accept', $offer))->toBeTrue();
    expect($otherUser->can('accept', $offer))->toBeFalse();

    expect($user->can('reject', $offer))->toBeTrue();
    expect($otherUser->can('reject', $offer))->toBeFalse();
});

test('customer policy authorization', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    expect($admin->can('viewAny', Customers::class))->toBeTrue();

    expect($user->can('view', $customer))->toBeTrue();
    expect($admin->can('view', $customer))->toBeTrue();

    expect($user->can('update', $customer))->toBeTrue();
    expect($admin->can('update', $customer))->toBeTrue();
});

test('user policy authorization', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create();

    expect($admin->can('viewAny', User::class))->toBeTrue();
    expect($user->can('viewAny', User::class))->toBeFalse();

    expect($admin->can('view', $user))->toBeTrue();
    expect($user->can('view', $user))->toBeTrue();

    expect($admin->can('create', User::class))->toBeTrue();

    expect($admin->can('update', $user))->toBeTrue();
    expect($user->can('update', $user))->toBeTrue();

    expect($admin->can('delete', $user))->toBeTrue();
    expect($user->can('delete', $admin))->toBeFalse();

    expect($admin->can('ban', $user))->toBeTrue();
});

test('rental policy authorization', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $owner = User::factory()->create();
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $car = Cars::factory()->create(['id_vendedor' => $ownerCustomer->id]);

    $rental = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => now()->addDay(),
        'fecha_fin' => now()->addDays(3),
        'precio_total' => 100,
        'id_estado' => 1
    ]);

    expect($user->can('viewAny', Rental::class))->toBeTrue();
    expect($user->can('view', $rental))->toBeTrue();
    expect($owner->can('view', $rental))->toBeTrue();
    expect($user->can('create', Rental::class))->toBeTrue();
});

test('sales policy authorization', function () {
    $user = User::factory()->create();
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $owner = User::factory()->create();
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $car = Cars::factory()->create(['id_vendedor' => $ownerCustomer->id]);

    $sale = Sales::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $ownerCustomer->id,
        'id_comprador' => $customer->id,
        'precio' => 10000,
        'id_estado' => 1
    ]);

    expect($user->can('viewAny', Sales::class))->toBeTrue();
    expect($user->can('view', $sale))->toBeTrue();
    expect($owner->can('view', $sale))->toBeTrue();
});
