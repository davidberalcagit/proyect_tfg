<?php

use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('index displays available cars', function () {
    // Use a unique name to avoid collision with seeder
    $brand = Brands::factory()->create(['nombre' => 'Toyota Test ' . uniqid()]);
    $car = Cars::factory()->create([
        'id_marca' => $brand->id,
        'precio' => 10000,
        'id_estado' => 1 // Available
    ]);

    $response = $this->get(route('cars.index'));

    $response->assertStatus(200);
    $response->assertViewIs('cars.index');
    $response->assertSee($car->title);
});

test('index filters by price', function () {
    $cheapCar = Cars::factory()->create(['precio' => 10000, 'id_estado' => 1]);
    $expensiveCar = Cars::factory()->create(['precio' => 50000, 'id_estado' => 1]);

    $response = $this->get(route('cars.index', ['max_price' => 20000]));

    $response->assertStatus(200);
    $response->assertSee($cheapCar->title);
    $response->assertDontSee($expensiveCar->title);
});

test('show page displays car details', function () {
    $car = Cars::factory()->create(['id_estado' => 1]);

    $response = $this->get(route('cars.show', $car));

    $response->assertStatus(200);
    $response->assertViewIs('cars.show');
    $response->assertSee($car->title);
});

test('myCars displays seller cars', function () {
    $user = User::factory()->create();
    // Corrected user_id to id_usuario
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    $user->setRelation('customer', $customer);

    $car = Cars::factory()->create(['id_vendedor' => $customer->id]);

    $this->actingAs($user);
    $response = $this->get(route('cars.my_cars')); // Corrected route name

    $response->assertStatus(200);
    $response->assertSee($car->title);
});
