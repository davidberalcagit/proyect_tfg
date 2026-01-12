<?php

namespace App\Actions\Fortify;

use App\Models\Customers;
use App\Models\Individuals;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'type' => ['required', 'string', 'in:individual,dealership,admin,supervisor,soporte'],
            'telefono' => ['required_if:type,individual,dealership', 'nullable', 'string', 'max:15', 'unique:customers,telefono'],
            'id_entidad' => ['required_if:type,individual,dealership', 'nullable', 'exists:entity_types,id'],
            'dni' => ['required_if:type,individual', 'string', 'max:9', 'unique:individuals,dni'],
            'fecha_nacimiento' => ['required_if:type,individual', 'date'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                // 'type' => $input['type'], // Removed
            ]);

            // Assign Role based on type input
            if ($input['type'] === 'individual') {
                $user->assignRole('individual');
                $customer = app(CustomerService::class)->createForUser($user, $input);
                app(IndividualService::class)->createForCustomer($customer, $input);
            } elseif ($input['type'] === 'dealership') {
                 $user->assignRole('dealership');
                 $customer = app(CustomerService::class)->createForUser($user, $input);

                 if (isset($input['nombre_empresa'])) {
                     $customer->dealership()->create([
                        'id_cliente' => $customer->id,
                        'nombre_empresa' => $input['nombre_empresa'],
                        'nif' => $input['nif'],
                        'direccion' => $input['direccion']
                     ]);
                 }
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
