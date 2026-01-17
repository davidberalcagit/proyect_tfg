<?php

namespace Tests\Feature;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_car_created_with_existing_brand_is_pending_approval()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $brand = Brands::first();
        $model = CarModels::where('id_marca', $brand->id)->first();

        $response = $this->actingAs($user)->postJson(route('cars.store'), [
            'id_marca' => $brand->id,
            'id_modelo' => $model->id,
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '1234ABC',
            'anyo_matri' => 2020,
            'km' => 10000,
            'precio' => 15000,
            'descripcion' => 'Descripción de prueba', // Añadido
        ]);

        $response->assertStatus(201);

        $expectedTitle = "{$brand->nombre} {$model->nombre} 2020";

        $this->assertDatabaseHas('cars', [
            'title' => $expectedTitle,
            'id_estado' => 4
        ]);
    }

    public function test_car_created_with_new_brand_is_pending_approval()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $response = $this->actingAs($user)->postJson(route('cars.store'), [
            'temp_brand' => 'MarcaFantasma',
            'temp_model' => 'ModeloX',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => '9999XYZ',
            'anyo_matri' => 2022,
            'km' => 5000,
            'precio' => 20000,
            'descripcion' => 'Descripción de prueba', // Añadido
        ]);

        $response->assertStatus(201);

        $expectedTitle = "MarcaFantasma ModeloX 2022";

        $this->assertDatabaseHas('cars', [
            'title' => $expectedTitle,
            'id_estado' => 4, // Pendiente
            'temp_brand' => 'MarcaFantasma'
        ]);
    }

    public function test_supervisor_can_approve_pending_car_and_create_brand()
    {
        // 1. Crear coche pendiente
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $car = Cars::create([
            'title' => 'Coche Pendiente',
            'id_vendedor' => $customer->id,
            'temp_brand' => 'NuevaMarcaTest',
            'temp_model' => 'NuevoModeloTest',
            'id_estado' => 4, // Pendiente
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => 'TEST001',
            'anyo_matri' => 2023,
            'km' => 100,
            'precio' => 30000,
            'descripcion' => 'Descripción de prueba', // Añadido
        ]);

        // 2. Crear Supervisor
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        // 3. Aprobar coche
        $response = $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

        $response->assertRedirect();

        // 4. Verificar que el coche está aprobado (Estado 1)
        $car->refresh();
        $this->assertEquals(1, $car->id_estado);
        $this->assertNull($car->temp_brand);

        // 5. Verificar que la marca y modelo se crearon
        $this->assertDatabaseHas('brands', ['nombre' => 'NuevaMarcaTest']);
        $this->assertDatabaseHas('car_models', ['nombre' => 'NuevoModeloTest']);

        $this->assertNotNull($car->id_marca);
        $this->assertNotNull($car->id_modelo);
    }
}
