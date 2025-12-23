<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Gears;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsGearTest extends TestCase
{
    use RefreshDatabase;

    public function test_car_creation_page_contains_gears()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $gear = Gears::factory()->create(['tipo' => 'Super-Manual']);

        $response = $this->get(route('cars.create'));

        $response->assertStatus(200);
        $response->assertSee('Super-Manual');
    }
}
