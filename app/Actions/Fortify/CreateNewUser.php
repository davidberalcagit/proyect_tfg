<?php

namespace App\Actions\Fortify;

use App\Jobs\SendWelcomeEmailJob;
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
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

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
            // Validaciones para concesionario
            'nombre_empresa' => ['required_if:type,dealership', 'nullable', 'string', 'max:255'],
            'nif' => ['required_if:type,dealership', 'nullable', 'string', 'max:20'],
            'direccion' => ['required_if:type,dealership', 'nullable', 'string', 'max:255'],
        ])->validate();

        $user = DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // Assign Role based on type input
            if ($input['type'] === 'individual') {
                $user->assignRole('individual');

                // Crear cliente particular
                $customer = Customers::create([
                    'id_usuario' => $user->id,
                    'id_entidad' => $input['id_entidad'],
                    'nombre_contacto' => $input['name'],
                    'telefono' => $input['telefono'],
                ]);

                app(IndividualService::class)->createForCustomer($customer, $input);

            } elseif ($input['type'] === 'dealership') {
                 $user->assignRole('dealership');

                 $dealershipId = null;

                 // Lógica para unirse o crear empresa por NIF
                 if (isset($input['nif'])) {
                     $nif = trim($input['nif']);

                     try {
                         // Intentamos firstOrCreate
                         $dealership = Dealerships::firstOrCreate(
                             ['nif' => $nif],
                             [
                                 'nombre_empresa' => $input['nombre_empresa'],
                                 'direccion' => $input['direccion']
                             ]
                         );
                         $dealershipId = $dealership->id;
                     } catch (\Exception $e) {
                         // Capturamos cualquier excepción para verificar si es duplicado
                         // MySQL: 1062, SQLite: 19 (o mensaje string)
                         $msg = $e->getMessage();
                         if (Str::contains($msg, ['Duplicate entry', 'UNIQUE constraint failed', 'Integrity constraint violation'])) {
                             $existing = Dealerships::where('nif', $nif)->first();
                             if ($existing) {
                                 $dealershipId = $existing->id;
                             } else {
                                 // Si sigue sin aparecer, es un error real
                                 throw $e;
                             }
                         } else {
                             throw $e;
                         }
                     }
                 }

                 // Crear cliente asociado al concesionario
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

        Log::info('Intentando despachar SendWelcomeEmailJob para: ' . $user->email);

        // Despachar Job de Bienvenida FUERA de la transacción
        SendWelcomeEmailJob::dispatch($user);

        return $user;
    }
}
