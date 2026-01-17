<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarRentalApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_car_created_for_rent_is_approved_as_for_rent()
    {
        // 1. Usuario crea coche para alquilar
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $response = $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaAlquiler',
            'temp_model' => 'ModeloAlquiler',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => 'RENT123',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 50, // Precio diario
            'descripcion' => 'Coche para alquilar',
            'listing_type' => 'rent' // INTENCIÓN: ALQUILER
        ]);

        $response->assertRedirect();

        $car = Cars::where('matricula', 'RENT123')->first();
        $this->assertEquals(4, $car->id_estado); // Pendiente
        $this->assertEquals('rent', $car->listing_type);

        // 2. Supervisor aprueba
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

        // 3. Verificar estado final
        $car->refresh();
        $this->assertEquals(3, $car->id_estado); // 3 = En Alquiler
    }

    public function test_car_created_for_sale_is_approved_as_for_sale()
    {
        // 1. Usuario crea coche para vender
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaVenta',
            'temp_model' => 'ModeloVenta',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => 'SALE123',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 20000,
            'descripcion' => 'Coche para vender',
            'listing_type' => 'sale' // INTENCIÓN: VENTA
        ]);

        $car = Cars::where('matricula', 'SALE123')->first();
        $this->assertEquals(4, $car->id_estado);
        $this->assertEquals('sale', $car->listing_type);

        // 2. Supervisor aprueba
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

        // 3. Verificar estado final
        $car->refresh();
        $this->assertEquals(1, $car->id_estado); // 1 = En Venta
    }
}
