<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\RentalStatus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalStatusIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutamos el seeder completo para asegurar que los estados existen
        $this->seed(DatabaseSeeder::class);
    }

    public function test_rental_can_be_created_with_status_zero()
    {
        // Verificar que el estado 0 existe
        $this->assertDatabaseHas('rental_statuses', ['id' => 0]);

        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_estado' => 3, // En Alquiler
            'precio' => 100
        ]);

        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'fecha_inicio' => now()->addDay()->format('Y-m-d'),
            'fecha_fin' => now()->addDays(2)->format('Y-m-d'),
            'terms' => 'on'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('rentals', [
            'id_vehiculo' => $car->id,
            'id_estado' => 0 // Pendiente
        ]);
    }
}
