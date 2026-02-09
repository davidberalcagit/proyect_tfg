<?php

use App\Jobs\SendCarApprovedNotificationJob;
use App\Jobs\SendOfferRejectedJob;
use App\Jobs\SendRentalReturnReminderJob;
use App\Models\Brands;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\ListingType;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Sales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

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
    $this->artisan('users:create')
        ->expectsQuestion('¿Qué tipo de usuario quieres crear?', 'individual')
        ->expectsQuestion('Nombre completo (Usuario)', 'Test Individual')
        ->expectsQuestion('Correo electrónico', 'individual@example.com')
        ->expectsQuestion('Contraseña', 'password')
        ->expectsQuestion('Confirmar contraseña', 'password')
        ->expectsQuestion('Nombre de Contacto (Dejar vacío para usar Nombre completo)', 'Test Contact')
        ->expectsQuestion('Teléfono', '111222333')
        ->expectsQuestion('DNI', '12345678X')
        ->expectsQuestion('Fecha de Nacimiento (YYYY-MM-DD)', '1990-01-01')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', ['email' => 'individual@example.com']);
    $user = User::where('email', 'individual@example.com')->first();
    expect($user->hasRole('individual'))->toBeTrue();
    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id, 'telefono' => '111222333', 'nombre_contacto' => 'Test Contact']);
    $this->assertDatabaseHas('individuals', ['id_cliente' => $user->customer->id, 'dni' => '12345678X']);

    $this->artisan('users:create dealership')
        ->expectsQuestion('Nombre completo (Usuario)', 'Test Dealership')
        ->expectsQuestion('Correo electrónico', 'dealership@example.com')
        ->expectsQuestion('Contraseña', 'password')
        ->expectsQuestion('Confirmar contraseña', 'password')
        ->expectsQuestion('Nombre de Contacto (Dejar vacío para usar Nombre completo)', '')
        ->expectsQuestion('Teléfono', '444555666')
        ->expectsQuestion('Nombre de la Empresa', 'Test Motors')
        ->expectsQuestion('NIF', 'B12345678')
        ->expectsQuestion('Dirección', 'Calle Falsa 123')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', ['email' => 'dealership@example.com']);
    $user = User::where('email', 'dealership@example.com')->first();
    expect($user->hasRole('dealership'))->toBeTrue();
    $this->assertDatabaseHas('dealerships', ['nif' => 'B12345678']);
    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id, 'telefono' => '444555666', 'nombre_contacto' => 'Test Dealership']);
});

test('cars approve command', function () {
    Bus::fake();

    $saleType = ListingType::where('nombre', 'Venta')->first();
    if (!$saleType) {
        $saleType = ListingType::factory()->create(['nombre' => 'Venta']);
    }

    $car = Cars::factory()->create([
        'id_estado' => 4,
        'id_listing_type' => $saleType->id
    ]);

    $this->artisan('cars:approve', ['car_id' => $car->id])
         ->assertSuccessful();

    $car->refresh();
    expect($car->id_estado)->toBe(1);
    Bus::assertDispatched(SendCarApprovedNotificationJob::class);
});

test('sales export command', function () {
    Storage::fake('public');
    $seller = User::factory()->create();
    $seller->assignRole('individual');
    $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

    $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);
    $buyer = Customers::factory()->create();

    Sales::create([
        'id_vehiculo' => $car->id,
        'id_vendedor' => $sellerCustomer->id,
        'id_comprador' => $buyer->id,
        'precio' => 10000,
        'id_estado' => 1
    ]);

    $this->artisan('sales:export', ['user_id' => $seller->id])
         ->assertSuccessful();

    $files = Storage::disk('public')->files('exports');
    expect(count($files))->toBeGreaterThan(0);
});

test('cars cleanup images command', function () {
    Storage::fake('public');

    Storage::disk('public')->put('cars/orphan.jpg', 'content');

    $car = Cars::factory()->create(['image' => 'cars/used.jpg']);
    Storage::disk('public')->put('cars/used.jpg', 'content');

    $this->artisan('cars:cleanup-images')
         ->assertSuccessful();

    Storage::disk('public')->assertMissing('cars/orphan.jpg');
    Storage::disk('public')->assertExists('cars/used.jpg');
});

test('users inactive notify command', function () {
    $inactiveUser = User::factory()->create(['updated_at' => now()->subMonths(7)]);
    $activeUser = User::factory()->create(['updated_at' => now()->subMonths(1)]);

    $this->artisan('users:inactive-notify', ['months' => 6])
         ->assertSuccessful()
         ->expectsOutputToContain("Notificando a: {$inactiveUser->email}");
});

test('system stats command', function () {
    $this->artisan('system:stats')
         ->assertSuccessful()
         ->expectsOutputToContain('Total Usuarios');
});
