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
            'buy cars',
            'view cars',
            'view users data',
            'view customers data',
            'offers for companies',
            'offers for individuals',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'individual', 'guard_name' => 'web']);
        $role->syncPermissions([
            'create cars',
            'buy cars',
            'view customers data',
            'view cars',
            'offers for companies',
            'offers for individuals'
        ]);

        $role = Role::firstOrCreate(['name' => 'dealership', 'guard_name' => 'web']);
        $role->syncPermissions([
            'create cars',
            'buy cars',
            'view customers data',
            'view cars',
            'offers for companies',
            'offers for individuals'
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());

        $role = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $role->syncPermissions([
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
