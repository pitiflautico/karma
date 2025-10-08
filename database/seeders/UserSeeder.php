<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@karma.app',
        ], [
            'name' => 'Karma Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'calendar_sync_enabled' => false,
            'adaptive_ui_enabled' => true,
        ]);

        $admin->assignRole('admin');

        // Create test user
        $user = User::firstOrCreate([
            'email' => 'user@karma.app',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'calendar_sync_enabled' => false,
            'adaptive_ui_enabled' => true,
            'selfie_mode' => 'random',
        ]);

        $user->assignRole('user');
    }
}
