<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single default organization for all records
        Organization::firstOrCreate([
            'slug' => 'cloud-studio',
        ], [
            'id' => Str::uuid(),
            'name' => 'Cloud Studio',
        ]);
    }
}
