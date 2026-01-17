<?php

namespace Tests\Feature;

use App\Mail\NewOfferReceived;
use App\Mail\OfferAccepted;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\StatusesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OfferEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_sent_to_seller_when_offer_is_made()
    {
        Mail::fake();
        $this->seed(StatusesSeeder::class); // Seed statuses

        // Crear vendedor
        $sellerUser = User::factory()->create();
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        // Crear coche del vendedor
        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1 // En venta
        ]);

        // Crear comprador
        $buyerUser = User::factory()->create();
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        // Actuar como comprador
        $this->actingAs($buyerUser);

        // Hacer oferta
        $response = $this->post(route('offers.store', $car), [
            'cantidad' => 15000
        ]);

        // Verificar redirección
        $response->assertRedirect();

        // Verificar que se envió el correo al vendedor
        Mail::assertSent(NewOfferReceived::class, function ($mail) use ($sellerUser) {
            return $mail->hasTo($sellerUser->email);
        });
    }

    public function test_email_is_sent_to_buyer_when_offer_is_accepted()
    {
        Mail::fake();
        $this->seed(StatusesSeeder::class); // Seed statuses

        // Crear vendedor
        $sellerUser = User::factory()->create();
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        // Crear coche
        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1
        ]);

        // Crear comprador
        $buyerUser = User::factory()->create();
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        // Crear oferta existente
        $offer = Offer::factory()->create([
            'id_vehiculo' => $car->id,
            'id_comprador' => $buyerCustomer->id,
            'id_vendedor' => $sellerCustomer->id,
            'estado' => 'pending'
        ]);

        // Actuar como vendedor
        $this->actingAs($sellerUser);

        // Aceptar oferta
        $response = $this->post(route('offers.accept', $offer));

        // Verificar redirección
        $response->assertRedirect();

        // Verificar que se envió el correo al comprador
        Mail::assertSent(OfferAccepted::class, function ($mail) use ($buyerUser) {
            return $mail->hasTo($buyerUser->email);
        });
    }
}
