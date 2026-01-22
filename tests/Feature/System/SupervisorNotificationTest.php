<?php

namespace Tests\Feature;

use App\Jobs\SendCarApprovedNotificationJob;
use App\Jobs\SendCarRejectedNotificationJob;
use App\Mail\CarApproved;
use App\Mail\CarRejected;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupervisorNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_approval_email_is_sent_to_seller()
    {
        Mail::fake();

        // Vendedor
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 4
        ]);

        // Ejecutar el Job directamente
        $job = new SendCarApprovedNotificationJob($car);
        $job->handle();

        // Verificar que el correo se encoló para el vendedor
        Mail::assertQueued(CarApproved::class, function ($mail) use ($sellerUser) {
            return $mail->hasTo($sellerUser->email);
        });
    }

    public function test_rejection_email_is_sent_to_seller_with_reason()
    {
        Mail::fake();

        // Vendedor
        $sellerUser = User::factory()->create();
        $sellerUser->assignRole('individual');
        $sellerCustomer = Customers::factory()->create(['id_usuario' => $sellerUser->id]);

        $car = Cars::factory()->create([
            'id_vendedor' => $sellerCustomer->id,
            'id_estado' => 4
        ]);

        $reason = 'Fotos borrosas';

        // Ejecutar el Job directamente
        $job = new SendCarRejectedNotificationJob($car, $reason);
        $job->handle();

        // Verificar que el correo se encoló para el vendedor con la razón
        Mail::assertQueued(CarRejected::class, function ($mail) use ($sellerUser, $reason) {
            return $mail->hasTo($sellerUser->email) && $mail->reason === $reason;
        });
    }
}
