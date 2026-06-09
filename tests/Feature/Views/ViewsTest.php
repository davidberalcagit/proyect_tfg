<?php

use App\Models\Cars;
use App\Models\Customers;
use App\Models\ListingType;
use App\Models\Offer;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\CarFilter;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->seed(Database\Seeders\TestDatabaseSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('individual');
});

test('cars index view renders correctly', function () {
    $car = Cars::factory()->create(['title' => 'Coche Visible', 'id_estado' => 1]);


    Livewire::test(CarFilter::class)
        ->set('search', 'Coche Visible')
        ->assertSee('Coche Visible');


    $response = $this->get(route('cars.index'));
    $response->assertStatus(200);
    $response->assertSeeLivewire('car-filter');
});

test('cars create view renders correctly', function () {
    Customers::factory()->create(['id_usuario' => $this->user->id]);

    $response = $this->actingAs($this->user)->get(route('cars.create'));

    $response->assertStatus(200);
    $response->assertSee('Create');
});

test('cars my cars view renders correctly', function () {
    $customer = Customers::factory()->create(['id_usuario' => $this->user->id]);

    $car = Cars::factory()->create(['title' => 'Mi Coche', 'id_vendedor' => $customer->id]);

    $response = $this->actingAs($this->user)->get(route('cars.my_cars'));

    $response->assertStatus(200);
    $response->assertSee('Mi Coche');
});

test('car show view renders correctly', function () {
    $seller = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);
    $car = Cars::factory()->create(['title' => 'Detalle Coche', 'id_vendedor' => $sellerCustomer->id, 'id_estado' => 1]);

    $response = $this->actingAs($this->user)->get(route('cars.show', $car));

    $response->assertStatus(200);
    $response->assertSee('Detalle Coche');
});

test('car show view shows rent button for rental cars', function () {
    Customers::factory()->create(['id_usuario' => $this->user->id]);

    $rentType = ListingType::where('nombre', 'Alquiler')->first();
    if (!$rentType) $rentType = ListingType::factory()->create(['id' => 2, 'nombre' => 'Alquiler']);

    $seller = User::factory()->create();
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $sellerCustomer->id,
        'id_estado' => 3,
        'id_listing_type' => $rentType->id
    ]);

    $response = $this->actingAs($this->user)->get(route('cars.show', $car));

    $response->assertStatus(200);

    $response->assertSee(__('Rent Car'));
});

test('sales index view renders correctly', function () {
    $sellerUser = User::factory()->create();
    $sellerUser->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

    $buyerUser = User::factory()->create();
    $buyerUser->assignRole('individual');
    $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

    $car = Cars::factory()->create(['title' => 'Coche con Oferta', 'id_vendedor' => $sellerCustomer->id]);

    Offer::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyerCustomer->id,
        'cantidad' => 18000,
        'estado' => 'pending'
    ]);

    $response = $this->actingAs($sellerUser)->get(route('sales.index'));

    $response->assertStatus(200);
    $response->assertSee('Received Offers');
    $response->assertSee('Coche con Oferta');
    $response->assertSee('18,000.00');
});

test('profile view shows two factor authentication option', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two factor authentication is not enabled.');
    }

    $response = $this->actingAs($this->user)->get(route('profile.show'));

    $response->assertStatus(200);
    $response->assertSee('Two Factor Authentication');
    $response->assertSeeLivewire('profile.two-factor-authentication-form');
});
