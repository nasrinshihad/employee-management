<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view employees',
            'edit employees', 
            'delete employees',
            'view own profile',
            'edit own profile',
            'view all profiles',
            'edit all profiles',
            'delete users',
            'manage roles',
            'manage permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $admin = Role::where('name', 'Admin')->first();
        $manager = Role::where('name', 'Manager')->first();
        $employee = Role::where('name', 'Employee')->first();

        $admin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'view employees',
            'edit employees',
            'view all profiles',
            'view own profile',
        ]);

        $employee->givePermissionTo([
            'view own profile'
        ]);
    }
}
