<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Karma
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view mood entries',
            'view calendar events',
            'view emotional selfies',
            'view groups',
            'create groups',
            'edit groups',
            'delete groups',
            'view subscriptions',
            'manage subscriptions',
            'view analytics',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], [
                'id' => Str::uuid(),
            ]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], [
            'id' => Str::uuid(),
        ]);
        $adminRole->givePermissionTo(Permission::all());


        // Regular user role - no admin permissions
        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ], [
            'id' => Str::uuid(),
        ]);
    }
}
