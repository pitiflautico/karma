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

        // Create permissions
        $permissions = [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view domains',
            'create domains',
            'edit domains',
            'delete domains',
            'view hostings',
            'create hostings',
            'edit hostings',
            'delete hostings',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            'view fiscal documents',
            'create fiscal documents',
            'edit fiscal documents',
            'delete fiscal documents',
            'view investments',
            'create investments',
            'edit investments',
            'delete investments',
            'view settings',
            'edit settings',
            'view timetracker',
            'create timetracker',
            'edit timetracker',
            'delete timetracker',
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

        $managerRole = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'web',
        ], [
            'id' => Str::uuid(),
        ]);
        $managerRole->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view domains',
            'create domains',
            'edit domains',
            'view hostings',
            'create hostings',
            'edit hostings',
            'view invoices',
            'create invoices',
            'edit invoices',
            'view projects',
            'create projects',
            'edit projects',
            'view fiscal documents',
            'view investments',
            'view settings',
            'view timetracker',
            'create timetracker',
            'edit timetracker',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ], [
            'id' => Str::uuid(),
        ]);
        $userRole->givePermissionTo([
            'view clients',
            'view domains',
            'view hostings',
            'view invoices',
            'view projects',
            'view timetracker',
            'create timetracker',
            'edit timetracker',
        ]);
    }
}
