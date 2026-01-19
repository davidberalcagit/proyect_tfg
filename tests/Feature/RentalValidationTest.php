<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_rental_end_date_must_be_after_start_date()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_estado' => 3, // En Alquiler
            'precio' => 50
        ]);

        $today = now()->format('Y-m-d');

        // Intentar alquilar con misma fecha inicio y fin
        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'fecha_inicio' => $today,
            'fecha_fin' => $today, // Misma fecha
        ]);

        $response->assertSessionHasErrors(['fecha_fin']);
    }

    public function test_rental_start_date_cannot_be_in_past()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create(['id_estado' => 3]);

        $yesterday = now()->subDay()->format('Y-m-d');
        $today = now()->format('Y-m-d');

        $response = $this->actingAs($user)->post(route('rentals.store', $car), [
            'fecha_inicio' => $yesterday,
            'fecha_fin' => $today,
        ]);

        $response->assertSessionHasErrors(['fecha_inicio']);
    }
}
