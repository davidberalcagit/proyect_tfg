<?php

namespace Tests\Feature;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Fuels;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_car_form_retains_old_input_on_validation_error()
    {
        $user = User::factory()->create();
        $brand = Brands::factory()->create();
        $model = CarModels::factory()->create(['id_marca' => $brand->id]);
        $fuel = Fuels::factory()->create();

        $response = $this->actingAs($user)->post(route('cars.store'), [
            'title' => 'Test Car',
            'id_marca' => $brand->id,
            'id_modelo' => $model->id,
            'id_combustible' => $fuel->id,
            'precio' => 'not-a-number', // Invalid price to trigger validation error
            'anyo_matri' => 2022,
            'km' => 10000,
        ]);

        $response->assertSessionHasErrors('precio');
        $response->assertRedirect();
        $response->assertSessionHasInput('title', 'Test Car');
        $response->assertSessionHasInput('id_marca', $brand->id);
        $response->assertSessionHasInput('id_modelo', $model->id);
    }
}
