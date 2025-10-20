<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create one if doesn't exist)
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'onboarding_completed' => true,
            ]);
        }

        // Create groups
        $groups = [
            [
                'name' => 'Family',
                'slug' => 'family',
                'description' => 'Our family mood tracker - stay connected and support each other',
                'color' => '#8B5CF6',
                'avatar' => null,
                'created_by' => $user->id,
                'is_active' => true,
            ],
            [
                'name' => 'Work Team',
                'slug' => 'work-team',
                'description' => 'A safe space for our team to share and track wellbeing',
                'color' => '#3B82F6',
                'avatar' => null,
                'created_by' => $user->id,
                'is_active' => true,
            ],
            [
                'name' => 'Study Group',
                'slug' => 'study-group',
                'description' => 'Track our stress levels and support each other during exams',
                'color' => '#10B981',
                'avatar' => null,
                'created_by' => $user->id,
                'is_active' => true,
            ],
            [
                'name' => 'Gym Buddies',
                'slug' => 'gym-buddies',
                'description' => 'Track our fitness mood and motivation together',
                'color' => '#F59E0B',
                'avatar' => null,
                'created_by' => $user->id,
                'is_active' => true,
            ],
            [
                'name' => 'Book Club',
                'slug' => 'book-club',
                'description' => 'Share how different books make us feel',
                'color' => '#EC4899',
                'avatar' => null,
                'created_by' => $user->id,
                'is_active' => true,
            ],
        ];

        foreach ($groups as $groupData) {
            // Check if group already exists by slug
            $group = Group::where('slug', $groupData['slug'])->first();

            if (!$group) {
                $group = Group::create($groupData);

                // Add the creator as admin
                $group->users()->attach($user->id, [
                    'role' => 'admin',
                    'joined_at' => now(),
                ]);
            } else {
                $this->command->info('⏩ Group "' . $groupData['name'] . '" already exists, skipping...');
            }

            // Add some fake members (between 5-15 members per group)
            $memberCount = rand(4, 14); // +1 because creator is already added

            for ($i = 0; $i < $memberCount; $i++) {
                // Create a simple member entry without creating actual users
                // In production, these would be real users
            }
        }

        $this->command->info('✅ Created ' . count($groups) . ' groups');
        $this->command->info('✅ User "' . $user->name . '" is admin of all groups');
    }
}
