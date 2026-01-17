<?php

namespace Tests\Feature;

use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsFormValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_create_car_form_displays_validation_errors()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        // Enviar formulario vacío
        $response = $this->actingAs($user)->post('/cars', []);

        // Verificar redirección (vuelta al formulario)
        $response->assertSessionHasErrors(['precio', 'matricula', 'descripcion']);
    }

    public function test_create_car_form_displays_duplicate_error_for_temp_brand()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        // Crear marca existente
        \App\Models\Brands::create(['nombre' => 'MarcaExistente']);

        $response = $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaExistente',
            // Otros campos requeridos para aislar el error de marca
            'temp_model' => 'ModeloNuevo',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test',
        ]);

        $response->assertSessionHasErrors(['temp_brand']);
    }
}
