<?php

use App\Models\Brands;
use App\Models\CarModels; // Importar CarModels
use App\Models\Cars;
use App\Models\Color; // Importar Color
use App\Models\Customers;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('create car with new brand', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'NuevaMarca',
        'temp_model' => 'NuevoModelo',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'ABC1234',
        'anyo_matri' => 2023,
        'km' => 1000,
        'precio' => 25000,
        'descripcion' => 'Descripción de prueba',
        'image' => UploadedFile::fake()->image('car.jpg'),
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cars', ['matricula' => 'ABC1234', 'id_estado' => 4]);
});

test('create car redirects to show page', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'MarcaRedirect',
        'temp_model' => 'ModeloRedirect',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'DEF5678',
        'anyo_matri' => 2024,
        'km' => 2000,
        'precio' => 30000,
        'descripcion' => 'Descripción de prueba',
        'image' => UploadedFile::fake()->image('car2.jpg'),
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $car = Cars::where('matricula', 'DEF5678')->first();
    $response->assertRedirect(route('cars.show', $car));
});

test('created car appears in my cars but not in public index', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'MarcaTest',
        'temp_model' => 'ModeloTest',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'GHI9012',
        'anyo_matri' => 2025,
        'km' => 3000,
        'precio' => 35000,
        'descripcion' => 'Descripción de prueba',
        'image' => UploadedFile::fake()->image('car3.jpg'),
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $car = Cars::where('matricula', 'GHI9012')->first();

    // Debe aparecer en "Mis Coches"
    $responseMyCars = $this->actingAs($user)->get(route('cars.my_cars'));
    $responseMyCars->assertSee($car->title);

    // NO debe aparecer en el índice público (porque está pendiente de aprobación)
    $responseIndex = $this->get(route('cars.index'));
    $responseIndex->assertDontSee($car->title);
});

test('cannot create car with existing brand name in temp brand', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    Brands::factory()->create(['nombre' => 'ExistingBrand']);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'ExistingBrand',
        'temp_model' => 'Modelo',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'JKL3456',
        'anyo_matri' => 2020,
        'km' => 10000,
        'precio' => 15000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertSessionHasErrors('temp_brand');
});

test('cannot create car with existing model name in temp model for existing brand', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $brand = Brands::factory()->create(['nombre' => 'BrandWithModel']);
    CarModels::factory()->create(['nombre' => 'ExistingModel', 'id_marca' => $brand->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'id_marca' => $brand->id,
        'temp_model' => 'ExistingModel',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'MNO7890',
        'anyo_matri' => 2021,
        'km' => 12000,
        'precio' => 18000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertSessionHasErrors('temp_model');
});

test('cannot create car with existing color name in temp color', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    Color::factory()->create(['nombre' => 'ExistingColor']);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'Brand',
        'temp_model' => 'Model',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'temp_color' => 'ExistingColor',
        'matricula' => 'PQR1234',
        'anyo_matri' => 2022,
        'km' => 15000,
        'precio' => 20000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertSessionHasErrors('temp_color');
});

test('cannot create car with invalid year', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'Brand',
        'temp_model' => 'Model',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'STU5678',
        'anyo_matri' => 1800, // Año inválido
        'km' => 1000,
        'precio' => 10000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertSessionHasErrors('anyo_matri');
});

test('cannot create car with negative price', function () {
    $user = User::factory()->create();
    $user->assignRole('individual');
    Customers::factory()->create(['id_usuario' => $user->id]);

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'Brand',
        'temp_model' => 'Model',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'VWX9012',
        'anyo_matri' => 2023,
        'km' => 1000,
        'precio' => -100, // Precio negativo
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertSessionHasErrors('precio');
});

test('unauthenticated user cannot create car', function () {
    $response = $this->post(route('cars.store'), [
        'temp_brand' => 'Brand',
        'temp_model' => 'Model',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'YZA3456',
        'anyo_matri' => 2023,
        'km' => 1000,
        'precio' => 10000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertRedirect('/login');
});

test('user without customer profile cannot create car', function () {
    $user = User::factory()->create(); // Sin customer
    $user->assignRole('admin'); // O cualquier rol sin customer

    $response = $this->actingAs($user)->post(route('cars.store'), [
        'temp_brand' => 'Brand',
        'temp_model' => 'Model',
        'id_marcha' => 1,
        'id_combustible' => 1,
        'id_color' => 1,
        'matricula' => 'BCD7890',
        'anyo_matri' => 2023,
        'km' => 1000,
        'precio' => 10000,
        'descripcion' => 'Descripción de prueba',
        'id_listing_type' => ListingType::where('nombre', 'Venta')->first()->id,
    ]);

    $response->assertStatus(403); // Forbidden por el controlador
});
