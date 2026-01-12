<?php

namespace Tests\Feature;

use App\Models\Cars;
use Database\Seeders\StatusesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_cars_can_be_created_with_different_statuses()
    {
        // Seed statuses first
        $this->seed(StatusesSeeder::class);

        // Create cars with specific statuses
        $carForSale = Cars::factory()->create(['id_estado' => 1]);
        $carSold = Cars::factory()->create(['id_estado' => 2]);
        $carForRent = Cars::factory()->create(['id_estado' => 3]);
        $carRented = Cars::factory()->create(['id_estado' => 4]);

        // Assert statuses are correct
        $this->assertEquals(1, $carForSale->id_estado);
        $this->assertEquals('En venta', $carForSale->status->nombre);

        $this->assertEquals(2, $carSold->id_estado);
        $this->assertEquals('Vendido', $carSold->status->nombre);

        $this->assertEquals(3, $carForRent->id_estado);
        $this->assertEquals('En alquiler', $carForRent->status->nombre);

        $this->assertEquals(4, $carRented->id_estado);
        $this->assertEquals('Alquilado', $carRented->status->nombre);
    }

    public function test_factory_creates_random_statuses()
    {
        $this->seed(StatusesSeeder::class);

        // Create 10 cars using the factory
        $cars = Cars::factory()->count(10)->create();

        // Check that we have valid statuses
        foreach ($cars as $car) {
            $this->assertContains($car->id_estado, [1, 2, 3, 4]);
            $this->assertNotNull($car->status);
        }
    }
}
