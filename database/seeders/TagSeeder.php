<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Tags for low moods (1-4) - More negative/challenging situations
            ['name' => 'Work Stress', 'emoji' => '💼', 'category' => 'trigger', 'mood_associations' => [1, 2, 3, 4]],
            ['name' => 'Anxiety', 'emoji' => '😰', 'category' => 'feeling', 'mood_associations' => [1, 2, 3, 4]],
            ['name' => 'Fatigue', 'emoji' => '😴', 'category' => 'physical', 'mood_associations' => [1, 2, 3, 4, 5]],
            ['name' => 'Conflict', 'emoji' => '💔', 'category' => 'trigger', 'mood_associations' => [1, 2, 3, 4]],
            ['name' => 'Overwhelmed', 'emoji' => '🤯', 'category' => 'feeling', 'mood_associations' => [1, 2, 3, 4]],
            ['name' => 'Lonely', 'emoji' => '😔', 'category' => 'feeling', 'mood_associations' => [1, 2, 3, 4]],

            // Tags for neutral moods (4-7) - Mixed or transitional states
            ['name' => 'Busy Day', 'emoji' => '📅', 'category' => 'activity', 'mood_associations' => [4, 5, 6, 7]],
            ['name' => 'Routine', 'emoji' => '🔄', 'category' => 'activity', 'mood_associations' => [5, 6, 7]],
            ['name' => 'Thinking', 'emoji' => '🤔', 'category' => 'activity', 'mood_associations' => [4, 5, 6, 7]],
            ['name' => 'Weather', 'emoji' => '🌤️', 'category' => 'environment', 'mood_associations' => [3, 4, 5, 6, 7]],

            // Tags for positive moods (7-10) - Good experiences
            ['name' => 'Exercise', 'emoji' => '🏃', 'category' => 'activity', 'mood_associations' => [6, 7, 8, 9, 10]],
            ['name' => 'Social Time', 'emoji' => '👥', 'category' => 'activity', 'mood_associations' => [7, 8, 9, 10]],
            ['name' => 'Achievement', 'emoji' => '🎯', 'category' => 'trigger', 'mood_associations' => [7, 8, 9, 10]],
            ['name' => 'Relaxation', 'emoji' => '🧘', 'category' => 'activity', 'mood_associations' => [6, 7, 8, 9, 10]],
            ['name' => 'Nature', 'emoji' => '🌳', 'category' => 'environment', 'mood_associations' => [6, 7, 8, 9, 10]],
            ['name' => 'Music', 'emoji' => '🎵', 'category' => 'activity', 'mood_associations' => [6, 7, 8, 9, 10]],
            ['name' => 'Good Food', 'emoji' => '🍽️', 'category' => 'physical', 'mood_associations' => [6, 7, 8, 9, 10]],
            ['name' => 'Grateful', 'emoji' => '🙏', 'category' => 'feeling', 'mood_associations' => [7, 8, 9, 10]],
            ['name' => 'Productive', 'emoji' => '✅', 'category' => 'feeling', 'mood_associations' => [7, 8, 9, 10]],
            ['name' => 'Excited', 'emoji' => '🎉', 'category' => 'feeling', 'mood_associations' => [8, 9, 10]],

            // Universal tags (applicable to any mood)
            ['name' => 'Sleep', 'emoji' => '😴', 'category' => 'physical', 'mood_associations' => null],
            ['name' => 'Family', 'emoji' => '👨‍👩‍👧‍👦', 'category' => 'social', 'mood_associations' => null],
            ['name' => 'Work', 'emoji' => '💻', 'category' => 'activity', 'mood_associations' => null],
            ['name' => 'Health', 'emoji' => '⚕️', 'category' => 'physical', 'mood_associations' => null],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['name' => $tag['name'], 'is_custom' => false],
                $tag
            );
        }
    }
}
