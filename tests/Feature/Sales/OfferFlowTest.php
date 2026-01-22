<?php

namespace Tests\Feature;

use App\Jobs\SendOfferAcceptedJob;
use App\Jobs\SendOfferNotificationJob;
use App\Jobs\SendSaleProcessedJob;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\Sales;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class OfferFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_full_offer_flow_create_accept_pay()
    {
        Bus::fake();

        // 1. Preparar usuarios y coche
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1, // En Venta
            'precio' => 20000
        ]);

        // 2. Comprador crea oferta
        $response = $this->actingAs($buyerUser)->post(route('offers.store', $car), [
            'cantidad' => 18000
        ]);

        $response->assertRedirect();

        $offer = Offer::where('id_vehiculo', $car->id)->first();
        $this->assertNotNull($offer);
        $this->assertEquals('pending', $offer->estado);
        $this->assertEquals(18000, $offer->cantidad);

        // Verificar Job de notificación al vendedor
        Bus::assertDispatched(SendOfferNotificationJob::class);

        // 3. Vendedor acepta oferta
        $response = $this->actingAs($sellerUser)->post(route('offers.accept', $offer));
        $response->assertRedirect();

        $offer->refresh();
        $this->assertEquals('accepted_by_seller', $offer->estado);

        // Verificar Job de notificación al comprador
        Bus::assertDispatched(SendOfferAcceptedJob::class);

        // 4. Comprador paga
        $response = $this->actingAs($buyerUser)->post(route('offers.pay', $offer));
        $response->assertRedirect();

        $offer->refresh();
        $this->assertEquals('completed', $offer->estado);

        // Verificar que se creó la venta
        $this->assertDatabaseHas('sales', [
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyerCustomer->id,
            'id_vendedor' => $sellerCustomer->id,
            'precio' => 18000
        ]);

        // Verificar que el coche cambió de estado
        $car->refresh();
        $this->assertEquals(2, $car->id_estado); // Vendido

        // Verificar Job de venta procesada
        Bus::assertDispatched(SendSaleProcessedJob::class);
    }

    public function test_seller_can_reject_offer()
    {
        Bus::fake();

        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1
        ]);

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'cantidad' => 15000,
            'estado' => 'pending'
        ]);

        // Vendedor rechaza
        $response = $this->actingAs($sellerUser)->post(route('offers.reject', $offer));
        $response->assertRedirect();

        $offer->refresh();
        $this->assertEquals('rejected', $offer->estado);
    }

    public function test_buyer_cannot_pay_if_not_accepted()
    {
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1
        ]);

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'cantidad' => 15000,
            'estado' => 'pending' // Aún no aceptada
        ]);

        // Comprador intenta pagar
        $response = $this->actingAs($buyerUser)->post(route('offers.pay', $offer));

        // Debería fallar o redirigir con error
        $response->assertSessionHas('error');

        $offer->refresh();
        $this->assertNotEquals('completed', $offer->estado);
    }
}
