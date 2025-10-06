<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Setting::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'organization_id' => $user->organization_id,
                'company_name' => 'My Company',
                'default_currency' => 'EUR',
                'invoice_prefix' => 'INV-',
                'invoice_sequence' => 1,
            ]);
        });
    }
}
