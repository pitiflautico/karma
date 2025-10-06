<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'hello@cloudstudio.es')->first();

        if (!$adminUser) {
            // Fallback to first user if admin not found
            $adminUser = User::first();
        }

        if ($adminUser) {
            // Create clients for the admin user
            Client::factory(10)->create([
                'user_id' => $adminUser->id,
                'organization_id' => $adminUser->organization_id,
            ]);
        }
    }
}
