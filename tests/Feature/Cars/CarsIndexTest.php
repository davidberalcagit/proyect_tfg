<?php

use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\CarFilter;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('index displays available cars', function () {
    $brand = Brands::factory()->create(['nombre' => 'Toyota Test ' . uniqid()]);
    $car = Cars::factory()->create([
        'id_marca' => $brand->id,
        'precio' => 10000,
        'id_estado' => 1
    ]);

    Livewire::test(CarFilter::class)
        ->set('search', $car->title)
        ->assertSee($car->title);

    $response = $this->get(route('cars.index'));
    $response->assertStatus(200);
    $response->assertSeeLivewire('car-filter');
});

test('index filters by price', function () {
    $cheapCar = Cars::factory()->create(['title' => 'Cheap Car', 'precio' => 10000, 'id_estado' => 1]);
    $expensiveCar = Cars::factory()->create(['title' => 'Expensive Car', 'precio' => 50000, 'id_estado' => 1]);

    Livewire::test(CarFilter::class)
        ->set('max_price', 20000)
        ->assertSee('Cheap Car')
        ->assertDontSee('Expensive Car');
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
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);
    $user->setRelation('customer', $customer);

    $car = Cars::factory()->create(['id_vendedor' => $customer->id]);

    $this->actingAs($user);
    $response = $this->get(route('cars.my_cars'));

    $response->assertStatus(200);
    $response->assertSee($car->title);
});
