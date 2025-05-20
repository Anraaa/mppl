<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create or update the permission
        Permission::firstOrCreate([
            'name' => 'view_studios',
            'guard_name' => 'web'
        ]);

        // Create or update the user role
        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        // Assign permission to role
        $userRole->givePermissionTo('view_studios', 'view_any_studios');
    }
}