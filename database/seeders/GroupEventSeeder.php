<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GroupEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $groups = Group::all();

        if ($groups->isEmpty()) {
            $this->command->error('❌ No groups found. Run GroupSeeder first.');
            return;
        }

        $eventTemplates = [
            // Family events
            'family' => [
                ['title' => 'Family Dinner', 'description' => 'Weekly family dinner at home', 'is_custom' => true],
                ['title' => 'Movie Night', 'description' => 'Watch a movie together', 'is_custom' => true],
                ['title' => 'Sunday Brunch', 'description' => 'Relaxing brunch at the cafe', 'is_custom' => true],
                ['title' => 'Game Night', 'description' => 'Board games and fun', 'is_custom' => true],
                ['title' => 'Beach Day', 'description' => 'Family trip to the beach', 'is_custom' => true],
            ],
            // Work events
            'work' => [
                ['title' => 'Team Meeting', 'description' => 'Weekly team sync', 'is_custom' => false],
                ['title' => 'Sprint Planning', 'description' => 'Plan the next sprint', 'is_custom' => false],
                ['title' => 'Team Lunch', 'description' => 'Casual team lunch', 'is_custom' => true],
                ['title' => 'Project Kickoff', 'description' => 'New project kickoff meeting', 'is_custom' => false],
                ['title' => 'Retrospective', 'description' => 'Sprint retrospective', 'is_custom' => false],
                ['title' => 'Coffee Break', 'description' => 'Team coffee and chat', 'is_custom' => true],
            ],
            // Study events
            'study' => [
                ['title' => 'Study Session', 'description' => 'Group study at library', 'is_custom' => true],
                ['title' => 'Exam Prep', 'description' => 'Preparing for finals together', 'is_custom' => true],
                ['title' => 'Project Work', 'description' => 'Working on group project', 'is_custom' => true],
                ['title' => 'Review Session', 'description' => 'Review before exam', 'is_custom' => true],
            ],
            // Gym events
            'gym' => [
                ['title' => 'Morning Workout', 'description' => 'Early morning gym session', 'is_custom' => true],
                ['title' => 'Yoga Class', 'description' => 'Group yoga session', 'is_custom' => true],
                ['title' => 'Cardio Day', 'description' => 'Running and cardio', 'is_custom' => true],
                ['title' => 'Strength Training', 'description' => 'Weight lifting session', 'is_custom' => true],
            ],
            // Book club events
            'book' => [
                ['title' => 'Book Discussion', 'description' => 'Discuss this month\'s book', 'is_custom' => true],
                ['title' => 'Author Q&A', 'description' => 'Virtual author meet and greet', 'is_custom' => false],
                ['title' => 'Reading Session', 'description' => 'Silent reading together', 'is_custom' => true],
            ],
        ];

        $totalEvents = 0;

        foreach ($groups as $group) {
            // Determine which template to use based on group name
            $templates = match(true) {
                str_contains(strtolower($group->name), 'family') => $eventTemplates['family'],
                str_contains(strtolower($group->name), 'work') => $eventTemplates['work'],
                str_contains(strtolower($group->name), 'study') => $eventTemplates['study'],
                str_contains(strtolower($group->name), 'gym') => $eventTemplates['gym'],
                str_contains(strtolower($group->name), 'book') => $eventTemplates['book'],
                default => $eventTemplates['family'],
            };

            // Create events for this group
            foreach ($templates as $index => $template) {
                // Mix of past, present, and future events
                $daysOffset = match(true) {
                    $index < 2 => rand(-14, -7), // Past events
                    $index < 3 => rand(-3, -1),   // Recent past
                    $index < 4 => 0,              // Today
                    default => rand(1, 14),       // Future events
                };

                $eventDate = Carbon::now()->addDays($daysOffset)->setTime(
                    rand(9, 20), // Hour between 9 AM and 8 PM
                    [0, 15, 30, 45][rand(0, 3)] // Minutes: 0, 15, 30, or 45
                );

                GroupEvent::create([
                    'group_id' => $group->id,
                    'calendar_event_id' => null,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'event_date' => $eventDate,
                    'created_by' => $user->id,
                    'is_custom' => $template['is_custom'],
                ]);

                $totalEvents++;
            }
        }

        $this->command->info('✅ Created ' . $totalEvents . ' events across ' . $groups->count() . ' groups');
    }
}
