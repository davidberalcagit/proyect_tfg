<?php

namespace Tests\Feature\Cars;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_cannot_delete_car_with_completed_sale()
    {
        $seller = User::factory()->create();
        $seller->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

        $buyerCustomer = Customers::factory()->create();

        $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

        // Crear venta asociada
        Sales::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'precio' => 10000,
            'id_estado' => 1
        ]);

        $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }

    public function test_cannot_delete_car_with_rental()
    {
        $seller = User::factory()->create();
        $seller->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

        $renterCustomer = Customers::factory()->create();

        $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

        // Crear alquiler asociado
        Rental::create([
            'id_vehiculo' => $car->id,
            'id_cliente' => $renterCustomer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(1),
            'precio_total' => 100,
            'id_estado' => 1
        ]);

        $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }

    public function test_cannot_delete_car_with_accepted_offer()
    {
        $seller = User::factory()->create();
        $seller->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

        $buyerCustomer = Customers::factory()->create();

        $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

        // Crear oferta aceptada
        Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'cantidad' => 10000,
            'estado' => 'accepted_by_seller'
        ]);

        $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }

    public function test_can_delete_car_without_transactions()
    {
        $seller = User::factory()->create();
        $seller->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $seller->id]);

        $car = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id]);

        $response = $this->actingAs($seller)->delete(route('cars.destroy', $car));

        $response->assertRedirect(route('cars.index'));
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }
}
