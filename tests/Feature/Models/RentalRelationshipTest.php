<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Rental;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalRelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_car_has_renters_relationship()
    {
        $car = Cars::factory()->create(['id_estado' => 3]); // En Alquiler
        $customer = Customers::factory()->create();

        // Crear alquiler
        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => $customer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'precio_total' => 500,
            'id_estado' => 1
        ]);

        // Verificar relación
        $this->assertTrue($car->renters->contains($customer));
        $this->assertEquals(1, $car->renters->count());

        // Verificar datos pivot
        $pivot = $car->renters->first()->pivot;
        $this->assertEquals(500, $pivot->precio_total); // assertEquals maneja tipos flexiblemente
    }

    public function test_customer_has_rented_cars_relationship()
    {
        $car = Cars::factory()->create(['id_estado' => 3]);
        $customer = Customers::factory()->create();

        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => $customer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'precio_total' => 500,
            'id_estado' => 1
        ]);

        $this->assertTrue($customer->rentedCars->contains($car));
        $this->assertEquals(1, $customer->rentedCars->count());
    }

    public function test_sales_index_view_shows_rentals()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create(['title' => 'Coche Alquilado Test']);

        // Crear alquiler donde el usuario es el cliente
        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => $customer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'precio_total' => 1234,
            'id_estado' => 1
        ]);

        $response = $this->actingAs($user)->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSee('My Rentals');
        $response->assertSee('Coche Alquilado Test');
        $response->assertSee('1,234.00');
    }

    public function test_sales_index_view_shows_leases_as_owner()
    {
        $ownerUser = User::factory()->create();
        $ownerUser->assignRole('individual');
        $ownerCustomer = Customers::factory()->create(['id_usuario' => $ownerUser->id]);

        $renterCustomer = Customers::factory()->create();

        $car = Cars::factory()->create([
            'id_vendedor' => $ownerCustomer->id,
            'title' => 'Mi Coche Arrendado'
        ]);

        // Crear alquiler donde el usuario es el dueño del coche
        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => $renterCustomer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'precio_total' => 5678,
            'id_estado' => 1
        ]);

        $response = $this->actingAs($ownerUser)->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSee('My Leases');
        $response->assertSee('Mi Coche Arrendado');
        $response->assertSee('5,678.00');
    }
}
