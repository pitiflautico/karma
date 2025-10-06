<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role and permission seeder first
        $this->call(RoleAndPermissionSeeder::class);

        // Run the organization seeder before users
        $this->call(OrganizationSeeder::class);

        // Run the user seeder to create admin and test users
        $this->call(UserSeeder::class);

        // Run the client seeder to generate clients
        $this->call(ClientSeeder::class);

        // Financial data
        $this->call(InvoiceSeeder::class);
        $this->call(InvoiceItemSeeder::class);

        // Run the setting seeder
        $this->call(SettingSeeder::class);
    }
}
