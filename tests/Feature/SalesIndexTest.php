<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Rental;
use App\Models\Sales;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sales_index_shows_correct_sections_without_duplication()
    {
        // 1. Crear usuarios
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        // 2. Crear datos

        // A. Oferta Pendiente (Debe salir en Received Offers del vendedor)
        $carOffer = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Oferta']);
        Offer::create([
            'id_vehiculo' => $carOffer->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'cantidad' => 10000,
            'estado' => 'pending'
        ]);

        // B. Venta Completada (Debe salir en My Sales del vendedor y My Purchases del comprador)
        $carSold = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Vendido']);
        Sales::create([
            'id_vehiculo' => $carSold->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'precio' => 20000,
            'id_estado' => 1
        ]);

        // C. Alquiler (Debe salir en My Leases del vendedor y My Rentals del comprador)
        $carRented = Cars::factory()->create(['id_vendedor' => $sellerCustomer->id, 'title' => 'Coche Alquilado']);
        Rental::create([
            'id_vehiculo' => $carRented->id,
            'id_cliente' => $buyerCustomer->id,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'precio_total' => 500,
            'id_estado' => 1
        ]);

        // 3. Verificar vista del VENDEDOR
        $responseSeller = $this->actingAs($sellerUser)->get(route('sales.index'));
        $responseSeller->assertStatus(200);

        // Debe ver Oferta Pendiente
        $responseSeller->assertSee('Coche Oferta');
        // Debe ver Venta (My Sales)
        $responseSeller->assertSee('Coche Vendido');
        // Debe ver Arrendamiento (My Leases)
        $responseSeller->assertSee('Coche Alquilado');

        // NO debe ver Compra (My Purchases) - A menos que haya comprado algo, que no lo hizo
        // Pero como la vista muestra todas las secciones, verificamos que la lista de compras esté vacía o no contenga este coche
        // La mejor forma es verificar los datos pasados a la vista
        $purchases = $responseSeller->viewData('purchases');
        $this->assertTrue($purchases->isEmpty());

        $sales = $responseSeller->viewData('sales');
        $this->assertEquals(1, $sales->count());
        $this->assertEquals('Coche Vendido', $sales->first()->vehiculo->title);

        // 4. Verificar vista del COMPRADOR
        $responseBuyer = $this->actingAs($buyerUser)->get(route('sales.index'));
        $responseBuyer->assertStatus(200);

        // NO debe ver Oferta Pendiente (es recibida, no enviada)
        $receivedOffers = $responseBuyer->viewData('receivedOffers');
        $this->assertTrue($receivedOffers->isEmpty());

        // Debe ver Compra (My Purchases)
        $purchasesBuyer = $responseBuyer->viewData('purchases');
        $this->assertEquals(1, $purchasesBuyer->count());
        $this->assertEquals('Coche Vendido', $purchasesBuyer->first()->vehiculo->title);

        // Debe ver Alquiler (My Rentals)
        $rentalsBuyer = $responseBuyer->viewData('rentals');
        $this->assertEquals(1, $rentalsBuyer->count());
        $this->assertEquals('Coche Alquilado', $rentalsBuyer->first()->car->title);
    }
}
