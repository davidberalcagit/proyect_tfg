<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\TestDatabaseSeeder::class);
});

test('seller can set car status to sale', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 3
    ]);

    $response = $this->actingAs($user)->post(route('cars.status.sale', $car->id));

    $response->assertRedirect();
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'id_estado' => 1]);
});

test('seller can set car status to rent', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($user)->post(route('cars.status.rent', $car->id));

    $response->assertRedirect();
    $this->assertDatabaseHas('cars', ['id' => $car->id, 'id_estado' => 3]);
});

test('non-seller cannot change car status', function () {
    $owner = User::factory()->create();
    $owner->assignRole('individual');
    $ownerCustomer = Customers::factory()->create(['id_usuario' => $owner->id]);

    $otherUser = User::factory()->create();
    $otherUser->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $otherUser->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $ownerCustomer->id,
        'id_estado' => 1
    ]);

    $response = $this->actingAs($otherUser)->post(route('cars.status.rent', $car->id));
    $response->assertStatus(403);
});

test('cannot set status sale if already invalid state', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4
    ]);

    $response = $this->actingAs($user)->post(route('cars.status.sale', $car->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('cannot set status rent if already invalid state', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4
    ]);

    $response = $this->actingAs($user)->post(route('cars.status.rent', $car->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});
