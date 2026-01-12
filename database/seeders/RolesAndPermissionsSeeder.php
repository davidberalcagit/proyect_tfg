<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 1. Crear Permisos ---
        $permissions = [
            'crud own cars',
            'buy cars',
            'crud all cars',
            'all access',
            'offers for companies',
            'offers for individuals',
            'view cars',
            'view users data',
            'view customers data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- 2. Crear Roles y Asignar Permisos ---

        // -Individuals: 1,2,8,11 (y 9 ver coches)
        $role = Role::firstOrCreate(['name' => 'individual', 'guard_name' => 'web']);
        $role->syncPermissions([
            'crud own cars',
            'buy cars',
            'offers for individuals',
            'view customers data',
            'view cars'
        ]);

        // -Dealerships: 1,2,7,11 (y 9 ver coches)
        $role = Role::firstOrCreate(['name' => 'dealership', 'guard_name' => 'web']);
        $role->syncPermissions([
            'crud own cars',
            'buy cars',
            'offers for companies',
            'view customers data',
            'view cars'
        ]);

        // -Admin: 6
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());

        // -Supervisores: 3,11,4 (4 es no comprar, así que no le damos 'buy cars')
        $role = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $role->syncPermissions([
            'crud all cars',
            'view customers data',
            'view cars'
        ]);

        // -Soporte: 5,9,10,11 (5 es no crud, así que no le damos permisos de crud)
        $role = Role::firstOrCreate(['name' => 'soporte', 'guard_name' => 'web']);
        $role->syncPermissions([
            'view cars',
            'view users data',
            'view customers data'
        ]);
    }
}
