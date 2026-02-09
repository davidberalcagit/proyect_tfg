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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'create cars',
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

        $role = Role::firstOrCreate(['name' => 'individual', 'guard_name' => 'web']);
        $role->syncPermissions([
            'create cars',
            'crud own cars',
            'buy cars',
            'offers for individuals',
            'view customers data',
            'view cars'
        ]);

        $role = Role::firstOrCreate(['name' => 'dealership', 'guard_name' => 'web']);
        $role->syncPermissions([
            'create cars',
            'crud own cars',
            'buy cars',
            'offers for companies',
            'view customers data',
            'view cars'
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());

        $role = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $role->syncPermissions([
            'crud all cars',
            'view customers data',
            'view cars'
        ]);

        $role = Role::firstOrCreate(['name' => 'soporte', 'guard_name' => 'web']);
        $role->syncPermissions([
            'view cars',
            'view users data',
            'view customers data'
        ]);
    }
}
