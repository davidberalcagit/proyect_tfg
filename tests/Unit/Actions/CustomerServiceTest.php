<?php

use App\Actions\Fortify\CustomerService;
use App\Models\Customers;
use App\Models\EntityType;
use App\Models\User;

test('createForUser creates customer record', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    $entityType = EntityType::factory()->create();

    $service = new CustomerService();

    $data = [
        'id_entidad' => $entityType->id,
        'telefono' => '123456789'
    ];

    $customer = $service->createForUser($user, $data);

    expect($customer)->toBeInstanceOf(Customers::class);
    expect($customer->id_usuario)->toBe($user->id);
    expect($customer->nombre_contacto)->toBe('Test User');
    expect($customer->telefono)->toBe('123456789');

    $this->assertDatabaseHas('customers', ['id_usuario' => $user->id]);
});
