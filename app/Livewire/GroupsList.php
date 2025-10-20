<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class GroupsList extends Component
{
    public function mount()
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        // Get all groups the user belongs to
        $groups = auth()->user()->groups()->with(['users', 'events'])->get();

        // Calculate stats for each group
        $groups = $groups->map(function ($group) {
            // Get today's mood entries count for activity
            $todayMoods = $group->users()
                ->join('mood_entries', 'users.id', '=', 'mood_entries.user_id')
                ->whereDate('mood_entries.created_at', today())
                ->count();

            // Calculate activity rate (percentage of members who logged today)
            $memberCount = $group->users->count();
            $activityRate = $memberCount > 0 ? round(($todayMoods / $memberCount) * 100) : 0;

            // Get average mood score for today
            $avgMood = $group->users()
                ->join('mood_entries', 'users.id', '=', 'mood_entries.user_id')
                ->whereDate('mood_entries.created_at', today())
                ->avg('mood_entries.mood_score');

            // Generate mood emoji based on average score
            $moodEmoji = $this->getMoodEmoji($avgMood);

            // Add calculated properties to group
            $group->mood_today = $avgMood ? round($avgMood, 1) : null;
            $group->mood_emoji = $moodEmoji;
            $group->activity_rate = $activityRate;
            $group->member_count = $memberCount;

            return $group;
        });

        return view('livewire.groups-list', [
            'groups' => $groups,
        ])->layout('layouts.app-mobile');
    }

    /**
     * Get emoji based on mood score (1-10 scale)
     */
    private function getMoodEmoji(?float $score): string
    {
        if ($score === null) {
            return '😶';
        }

        return match(true) {
            $score >= 9 => '🤩',
            $score >= 8 => '😄',
            $score >= 7 => '😊',
            $score >= 6 => '🙂',
            $score >= 5 => '😶',
            $score >= 4 => '😐',
            $score >= 3 => '😕',
            $score >= 2 => '☹️',
            default => '😢',
        };
    }
}
