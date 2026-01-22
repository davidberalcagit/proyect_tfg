<?php

namespace App\Actions\Fortify;

use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\Individuals;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'type' => ['required', 'string', 'in:individual,dealership,admin,supervisor,soporte'], // Restaurado dealership
            'telefono' => ['required_if:type,individual,dealership', 'nullable', 'string', 'max:15', 'unique:customers,telefono'],
            'id_entidad' => ['required_if:type,individual,dealership', 'nullable', 'exists:entity_types,id'],

            // Individual
            'dni' => ['required_if:type,individual', 'string', 'max:9', 'unique:individuals,dni'],
            'fecha_nacimiento' => ['required_if:type,individual', 'date'],

            // Dealership (Restaurado)
            'nombre_empresa' => ['required_if:type,dealership', 'nullable', 'string', 'max:255'],
            'nif' => ['required_if:type,dealership', 'nullable', 'string', 'max:20'],
            'direccion' => ['required_if:type,dealership', 'nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            Log::info("Usuario creado: {$user->id} - Rol: {$input['type']}");

            if ($input['type'] === 'individual') {
                $user->assignRole('individual');

                $customer = Customers::create([
                    'id_usuario' => $user->id,
                    'id_entidad' => $input['id_entidad'],
                    'nombre_contacto' => $input['name'],
                    'telefono' => $input['telefono'],
                ]);

                if (class_exists(IndividualService::class)) {
                    app(IndividualService::class)->createForCustomer($customer, $input);
                } else {
                    Individuals::create([
                        'id_cliente' => $customer->id,
                        'dni' => $input['dni'],
                        'fecha_nacimiento' => $input['fecha_nacimiento'],
                    ]);
                }

            } elseif ($input['type'] === 'dealership') {
                 $user->assignRole('dealership');

                 $dealershipId = null;

                 if (isset($input['nif'])) {
                     $nif = trim($input['nif']);
                     // Crear o recuperar concesionario por NIF
                     $dealership = Dealerships::firstOrCreate(
                         ['nif' => $nif],
                         [
                             'nombre_empresa' => $input['nombre_empresa'],
                             'direccion' => $input['direccion']
                         ]
                     );
                     $dealershipId = $dealership->id;
                 }

                 Customers::create([
                    'id_usuario' => $user->id,
                    'id_entidad' => $input['id_entidad'],
                    'nombre_contacto' => $input['name'],
                    'telefono' => $input['telefono'],
                    'dealership_id' => $dealershipId
                ]);

            } elseif ($input['type'] === 'admin') {
                $user->assignRole('admin');
            } elseif ($input['type'] === 'supervisor') {
                $user->assignRole('supervisor');
            } elseif ($input['type'] === 'soporte') {
                $user->assignRole('soporte');
            }

            return $user;
        });
    }
}
