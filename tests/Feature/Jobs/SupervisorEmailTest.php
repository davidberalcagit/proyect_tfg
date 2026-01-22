<?php

use App\Mail\CarApproved;
use App\Mail\CarRejected;
use App\Models\Cars;
use App\Models\Customers;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);
});

test('approval email is sent', function () {
    Mail::fake();

    // Crear coche pendiente
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4,
        'title' => 'Coche Pendiente'
    ]);

    // Supervisor aprueba
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $this->actingAs($supervisor)->post(route('supervisor.approve', $car->id));

    // Verificar correo en cola (porque implementa ShouldQueue)
    Mail::assertQueued(CarApproved::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('rejection email is sent with reason', function () {
    Mail::fake();

    // Crear coche pendiente
    $user = User::factory()->create();
    $user->assignRole('individual');
    $customer = Customers::factory()->create(['id_usuario' => $user->id]);

    $car = Cars::factory()->create([
        'id_vendedor' => $customer->id,
        'id_estado' => 4,
        'title' => 'Coche Malo'
    ]);

    // Supervisor rechaza
    $supervisor = User::factory()->create();
    $supervisor->assignRole('supervisor');

    $this->actingAs($supervisor)->post(route('supervisor.reject', $car->id), [
        'reason' => 'Fotos borrosas'
    ]);

    // Verificar correo en cola
    Mail::assertQueued(CarRejected::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->reason === 'Fotos borrosas';
    });
});
