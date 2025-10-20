<?php

namespace Database\Seeders;

use App\Models\GroupEvent;
use App\Models\GroupEventMood;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupEventMoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $events = GroupEvent::all();

        if ($events->isEmpty()) {
            $this->command->error('❌ No events found. Run GroupEventSeeder first.');
            return;
        }

        // Define mood data (1-10 scale)
        $moods = [
            ['score' => 1, 'icon' => '😢', 'name' => 'Terrible'],
            ['score' => 2, 'icon' => '☹️', 'name' => 'Very Bad'],
            ['score' => 3, 'icon' => '😕', 'name' => 'Bad'],
            ['score' => 4, 'icon' => '😐', 'name' => 'Poor'],
            ['score' => 5, 'icon' => '😶', 'name' => 'Okay'],
            ['score' => 6, 'icon' => '🙂', 'name' => 'Fine'],
            ['score' => 7, 'icon' => '😊', 'name' => 'Good'],
            ['score' => 8, 'icon' => '😄', 'name' => 'Great'],
            ['score' => 9, 'icon' => '😁', 'name' => 'Amazing'],
            ['score' => 10, 'icon' => '🤩', 'name' => 'Perfect'],
        ];

        $notes = [
            'Had a great time!',
            'Really enjoyed this',
            'Could have been better',
            'Not my favorite activity',
            'Felt amazing after this',
            'A bit stressful but worth it',
            'Perfect way to spend time together',
            'Looking forward to the next one',
            'Needed this!',
            null, // Some ratings without notes
            null,
            null,
        ];

        $totalRatings = 0;

        foreach ($events as $event) {
            // Determine if this event should have ratings
            // 20% chance of no ratings (upcoming events)
            // 30% chance of partial ratings (1-3 ratings)
            // 50% chance of multiple ratings (4-8 ratings)

            $ratingType = rand(1, 10);

            if ($ratingType <= 2) {
                // No ratings (20%)
                continue;
            } elseif ($ratingType <= 5) {
                // Partial ratings (30%)
                $ratingCount = rand(1, 3);
            } else {
                // Multiple ratings (50%)
                $ratingCount = rand(4, 8);
            }

            // Create ratings for this event
            for ($i = 0; $i < $ratingCount; $i++) {
                // For now, all ratings are from the first user
                // In production, these would be from different group members

                // Select a mood (weighted towards positive)
                $moodIndex = match(rand(1, 10)) {
                    1 => rand(0, 2),        // 10% negative (1-3)
                    2, 3 => rand(3, 4),     // 20% neutral (4-5)
                    default => rand(5, 9),  // 70% positive (6-10)
                };

                $selectedMood = $moods[$moodIndex];

                // Only create one rating per user per event
                if ($i === 0) {
                    GroupEventMood::create([
                        'group_event_id' => $event->id,
                        'user_id' => $user->id,
                        'mood_score' => $selectedMood['score'],
                        'mood_icon' => $selectedMood['icon'],
                        'mood_name' => $selectedMood['name'],
                        'note' => $notes[array_rand($notes)],
                    ]);

                    $totalRatings++;
                }
            }
        }

        $ratedEvents = GroupEvent::has('moods')->count();
        $unratedEvents = $events->count() - $ratedEvents;

        $this->command->info('✅ Created ' . $totalRatings . ' mood ratings');
        $this->command->info('📊 Rated events: ' . $ratedEvents);
        $this->command->info('📭 Unrated events: ' . $unratedEvents);
    }
}
