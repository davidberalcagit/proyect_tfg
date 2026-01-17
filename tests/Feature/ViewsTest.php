<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_cars_index_view_renders_correctly()
    {
        $car = Cars::factory()->create([
            'title' => 'Coche Público',
            'id_estado' => 1
        ]);

        $response = $this->get(route('cars.index'));

        $response->assertStatus(200);
        $response->assertSee('Coche Público');
    }

    public function test_cars_create_view_renders_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id]);

        $response = $this->actingAs($user)->get(route('cars.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Car');
    }

    public function test_cars_my_cars_view_renders_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'title' => 'Mi Coche Privado',
            'id_estado' => 4
        ]);

        $response = $this->actingAs($user)->get(route('cars.my_cars'));

        $response->assertStatus(200);
        $response->assertSee('Mi Coche Privado');
        $response->assertSee('Pending Review');
    }

    public function test_car_show_view_renders_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'title' => 'Coche Detalle',
            'precio' => 12345,
            'id_estado' => 1
        ]);

        $response = $this->get(route('cars.show', $car));

        $response->assertStatus(200);
        $response->assertSee('Coche Detalle');
        $response->assertSee('12,345.00');
        $response->assertSee('Back to list');
        // Verificar que el botón de volver usa JS
        $response->assertSee('javascript:history.back()');
    }

    public function test_car_show_view_shows_rent_button_for_rental_cars()
    {
        // Vendedor
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        // Comprador (para ver el botón)
        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'title' => 'Coche Alquiler',
            'id_estado' => 3 // En Alquiler
        ]);

        $response = $this->actingAs($buyerUser)->get(route('cars.show', $car));

        $response->assertStatus(200);
        $response->assertSee('Rent Car'); // Botón de alquilar
        $response->assertDontSee('Make Offer'); // No debe ver oferta
    }

    public function test_offers_index_view_renders_correctly()
    {
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        $buyerCustomer = Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'title' => 'Coche con Oferta',
            'precio' => 20000,
            'id_estado' => 1
        ]);

        Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $sellerCustomer->id,
            'id_comprador' => $buyerCustomer->id,
            'cantidad' => 18000,
            'estado' => 'pending'
        ]);

        $response = $this->actingAs($sellerUser)->get(route('offers.index'));

        $response->assertStatus(200);
        $response->assertSee('Received Offers');
        $response->assertSee('Coche con Oferta');
        $response->assertSee('18000');
    }
}
