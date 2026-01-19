<?php

namespace Tests\Feature;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Customers;
use App\Models\ListingType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsCreateTest extends TestCase
{
    use RefreshDatabase;

    protected $listingType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        // Obtener o crear un tipo de listado para los tests
        $this->listingType = ListingType::firstOrCreate(['nombre' => 'Venta']);
    }

    // --- Happy Paths ---

    public function test_create_car_with_new_brand()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $response = $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'NuevaMarcaTest',
            'temp_model' => 'NuevoModeloTest',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cars', [
            'temp_brand' => 'NuevaMarcaTest',
            'id_estado' => 4
        ]);
    }

    public function test_create_car_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $response = $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'NuevaMarcaTestShow',
            'temp_model' => 'NuevoModeloTestShow',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => 'SHOW123',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $car = Cars::where('temp_brand', 'NuevaMarcaTestShow')->first();

        $response->assertRedirect(route('cars.show', $car));
    }

    public function test_created_car_appears_in_my_cars_but_not_in_public_index()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaPrivada',
            'temp_model' => 'ModeloPrivado',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '9999ZZZ',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $car = Cars::where('temp_brand', 'MarcaPrivada')->first();

        $responseMyCars = $this->actingAs($user)->get(route('cars.my_cars'));
        $responseMyCars->assertStatus(200);
        $responseMyCars->assertSee($car->title);
        $responseMyCars->assertSee('Pending Review');

        $responseIndex = $this->get(route('cars.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertDontSee($car->title);
    }

    // --- Validaciones de Duplicados ---

    public function test_cannot_create_car_with_existing_brand_name_in_temp_brand()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        Brands::create(['nombre' => 'MarcaExistente']);

        $response = $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaExistente',
            'temp_model' => 'ModeloCualquiera',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ], ['Accept' => 'text/html']);

        $response->assertSessionHasErrors(['temp_brand']);
        $this->assertDatabaseMissing('cars', ['temp_brand' => 'MarcaExistente']);
    }

    public function test_cannot_create_car_with_existing_model_name_in_temp_model_for_existing_brand()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $brand = Brands::first();
        CarModels::create(['nombre' => 'ModeloExistente', 'id_marca' => $brand->id]);

        $response = $this->actingAs($user)->post('/cars', [
            'id_marca' => $brand->id,
            'temp_model' => 'ModeloExistente',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ], ['Accept' => 'text/html']);

        $response->assertSessionHasErrors(['temp_model']);
    }

    public function test_cannot_create_car_with_existing_color_name_in_temp_color()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        Color::create(['nombre' => 'RojoFuego']);

        $brand = Brands::first();
        $model = CarModels::where('id_marca', $brand->id)->first();

        $response = $this->actingAs($user)->post('/cars', [
            'id_marca' => $brand->id,
            'id_modelo' => $model->id,
            'id_marcha' => 1,
            'id_combustible' => 1,
            'temp_color' => 'RojoFuego',
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ], ['Accept' => 'text/html']);

        $response->assertSessionHasErrors(['temp_color']);
    }

    // --- Validaciones de Campos ---

    public function test_cannot_create_car_with_invalid_year()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $brand = Brands::first();
        $model = CarModels::where('id_marca', $brand->id)->first();

        $response = $this->actingAs($user)->post('/cars', [
            'id_marca' => $brand->id,
            'id_modelo' => $model->id,
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 1800,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $response->assertSessionHasErrors(['anyo_matri']);
    }

    public function test_cannot_create_car_with_negative_price()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $brand = Brands::first();
        $model = CarModels::where('id_marca', $brand->id)->first();

        $response = $this->actingAs($user)->post('/cars', [
            'id_marca' => $brand->id,
            'id_modelo' => $model->id,
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2020,
            'km' => 100,
            'precio' => -500,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $response->assertSessionHasErrors(['precio']);
    }

    // --- Seguridad ---

    public function test_unauthenticated_user_cannot_create_car()
    {
        $response = $this->post('/cars', [
            'temp_brand' => 'Marca',
            'temp_model' => 'Modelo',
            'precio' => 1000,
            'id_listing_type' => 1, // AÑADIDO
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_customer_profile_cannot_create_car()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/cars', [
            'temp_brand' => 'Marca',
            'temp_model' => 'Modelo',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'id_listing_type' => $this->listingType->id, // AÑADIDO
        ]);

        $response->assertStatus(403);
    }
}
