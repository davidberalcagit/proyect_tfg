<?php

namespace Tests\Feature;

use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarsEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_edit_car_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'id_estado' => 4 // Pendiente (editable)
        ]);

        $response = $this->actingAs($user)->get(route('cars.edit', $car));

        $response->assertStatus(200);
        $response->assertSee($car->title); // Verificar que carga datos
        $response->assertSee('Update'); // Botón de actualizar
    }

    public function test_car_can_be_updated()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'id_estado' => 4,
            'precio' => 10000
        ]);

        $response = $this->actingAs($user)->put(route('cars.update', $car), [
            'id_marca' => $car->id_marca,
            'id_modelo' => $car->id_modelo,
            'id_marcha' => $car->id_marcha,
            'id_combustible' => $car->id_combustible,
            'id_color' => $car->id_color,
            'matricula' => $car->matricula,
            'anyo_matri' => $car->anyo_matri,
            'km' => $car->km,
            'precio' => 15000, // Cambio de precio
            'descripcion' => 'Updated description',
        ]);

        $response->assertRedirect(route('cars.index'));

        $car->refresh();
        $this->assertEquals(15000, $car->precio);
        $this->assertEquals('Updated description', $car->descripcion);
    }

    public function test_cannot_edit_approved_car_unless_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('individual');
        $customer = Customers::factory()->create(['id_usuario' => $user->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $customer->id,
            'id_estado' => 1 // Aprobado
        ]);

        // Intentar acceder a la vista
        $response = $this->actingAs($user)->get(route('cars.edit', $car));
        $response->assertRedirect(); // Redirige con error
        $response->assertSessionHas('error');

        // Intentar actualizar
        $response = $this->actingAs($user)->put(route('cars.update', $car), [
            'precio' => 20000
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_edit_approved_car()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Coche de otro usuario
        $car = Cars::factory()->create([
            'id_estado' => 1 // Aprobado
        ]);

        $response = $this->actingAs($admin)->get(route('cars.edit', $car));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->put(route('cars.update', $car), [
            'id_marca' => $car->id_marca,
            'id_modelo' => $car->id_modelo,
            'id_marcha' => $car->id_marcha,
            'id_combustible' => $car->id_combustible,
            'id_color' => $car->id_color,
            'matricula' => $car->matricula,
            'anyo_matri' => $car->anyo_matri,
            'km' => $car->km,
            'precio' => 99999,
            'descripcion' => 'Admin edit',
        ]);

        $response->assertRedirect(route('cars.index'));

        $car->refresh();
        $this->assertEquals(99999, $car->precio);
    }
}
