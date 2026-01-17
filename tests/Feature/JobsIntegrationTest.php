<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Jobs\ProcessCarImageJob;
use App\Jobs\SendOfferNotificationJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class JobsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_process_car_image_job_is_dispatched_when_car_is_created()
    {
        $this->withoutExceptionHandling(); // VER ERROR REAL

        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $user->id, 'id_entidad' => 1]);

        $file = UploadedFile::fake()->image('car.jpg');

        $this->actingAs($user)->post('/cars', [
            'temp_brand' => 'MarcaJob',
            'temp_model' => 'ModeloJob',
            'id_marcha' => 1,
            'id_combustible' => 1,
            'id_color' => 1,
            'matricula' => 'JOB123',
            'anyo_matri' => 2024,
            'km' => 100,
            'precio' => 25000,
            'descripcion' => 'Test description',
            'image' => $file
        ]);

        $this->assertDatabaseHas('cars', ['matricula' => 'JOB123']);

        Bus::assertDispatched(ProcessCarImageJob::class);
    }

    public function test_send_offer_notification_job_is_dispatched_when_offer_is_created()
    {
        Bus::fake();

        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 1
        ]);

        $buyerUser = User::factory()->create();
        $buyerUser->assignRole('individual');
        Customers::factory()->create(['id_usuario' => $buyerUser->id]);

        $this->actingAs($buyerUser)->post(route('offers.store', $car), [
            'cantidad' => 15000
        ]);

        Bus::assertDispatched(SendOfferNotificationJob::class);
    }

    public function test_send_welcome_email_job_is_dispatched_when_user_registers_via_action()
    {
        Bus::fake();

        $action = new CreateNewUser();

        $action->create([
            'name' => 'Nuevo Usuario Job',
            'email' => 'jobuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? 'on' : '',
            'type' => 'individual',
            'telefono' => '600999888',
            'id_entidad' => 1,
            'dni' => '99999999Z',
            'fecha_nacimiento' => '1990-01-01'
        ]);

        Bus::assertDispatched(SendWelcomeEmailJob::class);
    }
}
