<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create {role? : El rol del usuario (individual, dealership, admin, supervisor, soporte)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo usuario interactivamente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->argument('role');
        $validRoles = ['individual', 'dealership', 'admin', 'supervisor', 'soporte'];

        if (!$role || !in_array($role, $validRoles)) {
            $role = $this->choice('¿Qué tipo de usuario quieres crear?', $validRoles, 0);
        }

        $this->info("Creando usuario con rol: {$role}");

        // Datos Básicos
        $name = $this->ask('Nombre completo');
        $email = $this->ask('Correo electrónico');
        $password = $this->secret('Contraseña');
        $passwordConfirmation = $this->secret('Confirmar contraseña');

        $input = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'type' => $role,
            'terms' => 'on', // Aceptar términos por defecto en consola
        ];

        // Datos Específicos
        if (in_array($role, ['individual', 'dealership'])) {
            $input['telefono'] = $this->ask('Teléfono');

            // Entidad (1: Particular, 2: Empresa) - Simplificación basada en rol
            $input['id_entidad'] = ($role === 'dealership') ? 2 : 1;

            if ($role === 'individual') {
                $input['dni'] = $this->ask('DNI');
                $input['fecha_nacimiento'] = $this->ask('Fecha de Nacimiento (YYYY-MM-DD)');
            } elseif ($role === 'dealership') {
                $input['nombre_empresa'] = $this->ask('Nombre de la Empresa');
                $input['nif'] = $this->ask('NIF');
                $input['direccion'] = $this->ask('Dirección');
            }
        }

        // Validación Previa (Opcional, CreateNewUser ya valida, pero mejor fallar rápido)
        $validator = Validator::make($input, [
            'email' => 'unique:users,email',
            'password' => 'confirmed|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        try {
            $creator = new CreateNewUser();
            $user = $creator->create($input);

            $this->info("Usuario '{$user->name}' ({$user->email}) creado exitosamente con rol '{$role}'.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error al crear usuario: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
