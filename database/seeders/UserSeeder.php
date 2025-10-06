<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the default organization
        $organization = Organization::where('slug', 'cloud-studio')->first();

        // Create admin user with organization
        $admin = User::firstOrCreate([
            'email' => 'hello@cloudstudio.es',
        ], [
            'name' => 'Toni Soriano',
            'password' => bcrypt('123123123'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');
    }
}
