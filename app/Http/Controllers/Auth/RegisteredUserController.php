<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\Dealerships;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:individual,dealership,admin,supervisor,soporte'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'id_entidad' => ['required_if:type,individual,dealership', 'nullable', 'exists:entity_types,id'],
            'telefono' => ['required_if:type,individual,dealership', 'nullable', 'string', 'regex:/^[0-9]{9}$/', 'unique:customers,telefono'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->input('id_entidad') == 1) {
            $rules['dni'] = ['required', 'string', 'max:9', 'unique:individuals,dni'];
            $rules['fecha_nacimiento'] = ['required', 'date'];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                // 'type' => $request->type, // Removed
            ]);

            // Assign Role
            if ($request->type === 'individual') {
                $user->assignRole('individual');
            } elseif ($request->type === 'dealership') {
                $user->assignRole('dealership');
            } elseif ($request->type === 'admin') {
                $user->assignRole('admin');
            } elseif ($request->type === 'supervisor') {
                $user->assignRole('supervisor');
            } elseif ($request->type === 'soporte') {
                $user->assignRole('soporte');
            }

            if (in_array($request->type, ['individual', 'dealership'])) {
                $customer = Customers::create([
                    'id_usuario'      => $user->id,
                    'nombre_contacto' => $request->name,
                    'telefono'        => $request->telefono,
                    'id_entidad'      => $request->id_entidad, // 1 = individual, 2 = dealership
                ]);
                if ($customer->id_entidad == 2) {

                    $customer->dealership()->create([
                        'id_cliente'      => $customer->id,
                        'nombre_empresa'  => $request->nombre_empresa,
                        'nif'             => $request->nif,
                        'direccion'       => $request->direccion
                    ]);
                }

                // 4. Si es individual → crear registro en individuals
                if ($customer->id_entidad == 1) {

                    $customer->individual()->create([
                        'id_cliente'       => $customer->id,
                        'dni'              => $request->dni,
                        'fecha_nacimiento' => $request->fecha_nacimiento
                    ]);
                }
            }

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect(route('dashboard', absolute: false));
    }
    public function update(Request $request, User $user)
    {
        // Validación básica
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:normal,empresa',
        ]);

        // Actualiza los datos del usuario
        $user->update($data);

        // Si el tipo es empresa y no existe una empresa asociada...
        if ($data['type'] === 'empresa') {

            // Comprobar si ya tiene registro en empresas (opcional)
            if (!$user->empresa) {

                // Crear registro en tabla empresas
                \App\Models\Dealerships::create([
                    'user_id' => $user->id,
                    // puedes añadir más campos obligatorios aquí
                ]);
            }
        }

        return redirect()->route('users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

}
