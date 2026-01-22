<?php

use App\Jobs\SendOfferRejectedJob;
use App\Jobs\SendRentalReturnReminderJob;
use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
    RentalStatus::firstOrCreate(['id' => 7], ['nombre' => 'Aceptado por dueño (Esperando pago)']);
});

test('offers auto reject low command', function () {
    Bus::fake();

    $car = Cars::factory()->create(['precio' => 1000, 'id_estado' => 1]);
    $buyer = Customers::factory()->create();
    $seller = Customers::factory()->create();

    $lowOffer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyer->id,
        'id_vendedor' => $seller->id,
        'cantidad' => 400,
        'estado' => 'pending'
    ]);

    $highOffer = Offer::create([
        'id_vehiculo' => $car->id,
        'id_comprador' => $buyer->id,
        'id_vendedor' => $seller->id,
        'cantidad' => 600,
        'estado' => 'pending'
    ]);

    Artisan::call('offers:auto-reject-low', ['--percentage' => 50]);

    $lowOffer->refresh();
    $highOffer->refresh();

    expect($lowOffer->estado)->toBe('rejected');
    expect($highOffer->estado)->toBe('pending');

    Bus::assertDispatched(SendOfferRejectedJob::class, fn ($job) => $job->offer->id === $lowOffer->id);
    Bus::assertNotDispatched(SendOfferRejectedJob::class, fn ($job) => $job->offer->id === $highOffer->id);
});

test('rentals process daily command', function () {
    Bus::fake();
    Carbon::setTestNow(Carbon::create(2024, 1, 15));

    $car = Cars::factory()->create(['id_estado' => 3]);
    $customer = Customers::factory()->create();

    $rentalStartingToday = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => Carbon::today(),
        'fecha_fin' => Carbon::today()->addDays(5),
        'precio_total' => 500,
        'id_estado' => 2
    ]);

    $rentalEndingToday = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => Carbon::today()->subDays(5),
        'fecha_fin' => Carbon::today(),
        'precio_total' => 500,
        'id_estado' => 3
    ]);

    $rentalOverdue = Rental::create([
        'id_vehiculo' => $car->id,
        'id_cliente' => $customer->id,
        'fecha_inicio' => Carbon::today()->subDays(10),
        'fecha_fin' => Carbon::today()->subDays(1),
        'precio_total' => 500,
        'id_estado' => 2
    ]);

    Artisan::call('rentals:process-daily');

    $rentalStartingToday->refresh();
    $rentalEndingToday->refresh();
    $rentalOverdue->refresh();

    expect($rentalStartingToday->id_estado)->toBe(3);
    expect($rentalEndingToday->id_estado)->toBe(2);
    expect($rentalOverdue->id_estado)->toBe(4);

    Bus::assertDispatched(SendRentalReturnReminderJob::class, fn ($job) => $job->rental->id === $rentalEndingToday->id);

    Carbon::setTestNow(null);
});

test('prices modify command increase decrease', function () {
    $brand = Brands::factory()->create(['nombre' => 'TestBrand']);
    $individualCustomer = Customers::factory()->create();
    $individualCustomer->user->assignRole('individual');
    $dealershipCustomer = Customers::factory()->create();
    $dealershipCustomer->user->assignRole('dealership');

    $car1 = Cars::factory()->create(['precio' => 100, 'id_estado' => 1, 'id_marca' => $brand->id, 'id_vendedor' => $individualCustomer->id]);
    $car2 = Cars::factory()->create(['precio' => 200, 'id_estado' => 1, 'id_marca' => $brand->id, 'id_vendedor' => $dealershipCustomer->id]);
    $car3 = Cars::factory()->create(['precio' => 300, 'id_estado' => 1, 'id_marca' => $brand->id, 'id_vendedor' => $individualCustomer->id]);

    Artisan::call('prices:modify', ['percentage' => 10, 'target' => 'all', 'mode' => 'decrease']);
    $car1->refresh(); $car2->refresh(); $car3->refresh();

    expect($car1->precio)->toEqual(90);
    expect($car2->precio)->toEqual(180);
    expect($car3->precio)->toEqual(270);

    Artisan::call('prices:modify', ['percentage' => 5, 'target' => 'individual', 'mode' => 'increase']);
    $car1->refresh(); $car2->refresh(); $car3->refresh();
    expect($car1->precio)->toEqual(94.5);
    expect($car2->precio)->toEqual(180);
    expect($car3->precio)->toEqual(283.5);

    Artisan::call('prices:modify', ['percentage' => 20, 'target' => $car1->id, 'mode' => 'decrease']);
    $car1->refresh();
    expect($car1->precio)->toEqual(75.6);

    Artisan::call('prices:modify', ['percentage' => 10, 'target' => $brand->nombre, 'mode' => 'increase']);
    $car1->refresh(); $car2->refresh(); $car3->refresh();
    expect(round($car1->precio, 2))->toEqual(83.16);
    expect($car2->precio)->toEqual(198.0);
    expect(round($car3->precio, 2))->toEqual(311.85);
});

test('users create command', function () {
    // Test Individual
    $this->artisan('users:create')
        ->expectsQuestion('¿Qué tipo de usuario quieres crear?', 'individual')
        ->expectsQuestion('Nombre completo', 'Test Individual')
        ->expectsQuestion('Correo electrónico', 'individual@example.com')
        ->expectsQuestion('Contraseña', 'password')
        ->expectsQuestion('Confirmar contraseña', 'password')
        ->expectsQuestion('Teléfono', '111222333')
        ->expectsQuestion('DNI', '12345678X')
        ->expectsQuestion('Fecha de Nacimiento (YYYY-MM-DD)', '1990-01-01')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', ['email' => 'individual@example.com']);
    $user = User::where('email', 'individual@example.com')->first();
    expect($user->hasRole('individual'))->toBeTrue();
    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id, 'telefono' => '111222333']);
    $this->assertDatabaseHas('individuals', ['id_cliente' => $user->customer->id, 'dni' => '12345678X']);

    // Test Dealership (Restaurado)
    $this->artisan('users:create dealership')
        ->expectsQuestion('Nombre completo', 'Test Dealership')
        ->expectsQuestion('Correo electrónico', 'dealership@example.com')
        ->expectsQuestion('Contraseña', 'password')
        ->expectsQuestion('Confirmar contraseña', 'password')
        ->expectsQuestion('Teléfono', '444555666')
        ->expectsQuestion('Nombre de la Empresa', 'Test Motors')
        ->expectsQuestion('NIF', 'B12345678')
        ->expectsQuestion('Dirección', 'Calle Falsa 123')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', ['email' => 'dealership@example.com']);
    $user = User::where('email', 'dealership@example.com')->first();
    expect($user->hasRole('dealership'))->toBeTrue();
    $this->assertDatabaseHas('dealerships', ['nif' => 'B12345678']);
    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id, 'telefono' => '444555666']);
});
