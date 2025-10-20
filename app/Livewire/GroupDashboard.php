<?php

namespace App\Livewire;

use App\Models\Group;
use Carbon\Carbon;
use Livewire\Component;

class GroupDashboard extends Component
{
    public $groupId;
    public $group;
    public $period = '7d'; // Default period: 7 days
    public $tab = 'events'; // Default tab: events or members
    public $statsTab = 'events'; // Sub-tab for stats section

    public function mount($groupId)
    {
        $this->groupId = $groupId;

        // Load group and verify user is a member
        $this->group = Group::with(['users', 'events'])->findOrFail($groupId);

        // Verify user is a member of this group
        if (!$this->group->users->contains(auth()->id())) {
            abort(403, 'No eres miembro de este grupo');
        }
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function setStatsTab($tab)
    {
        $this->statsTab = $tab;
    }

    public function render()
    {
        $eventStats = $this->calculateEventStats();
        $memberStats = $this->calculateMemberStats();

        return view('livewire.group-dashboard', [
            'eventStats' => $eventStats,
            'memberStats' => $memberStats,
        ])->layout('layouts.app-mobile');
    }

    private function calculateEventStats()
    {
        $startDate = $this->getStartDate();

        // Get all group event moods (ratings) within period
        $groupEventMoods = \App\Models\GroupEventMood::whereHas('event', function($query) use ($startDate) {
                $query->where('group_id', $this->groupId)
                      ->where('event_date', '>=', $startDate);
            })
            ->with('event')
            ->get();

        // Calculate average mood from group event ratings
        $averageMood = $groupEventMoods->avg('mood_score') ?? 0;

        // Calculate detailed mood distribution
        $distribution = [
            'excellent' => $groupEventMoods->whereBetween('mood_score', [9, 10])->count(),
            'happy' => $groupEventMoods->whereBetween('mood_score', [7, 8])->count(),
            'neutral' => $groupEventMoods->whereBetween('mood_score', [5, 6])->count(),
            'sad' => $groupEventMoods->whereBetween('mood_score', [3, 4])->count(),
            'depressed' => $groupEventMoods->whereBetween('mood_score', [1, 2])->count(),
        ];

        // Calculate activity today - count unique users who rated today
        $todayRatings = $groupEventMoods->where('created_at', '>=', today())
            ->pluck('user_id')
            ->unique()
            ->count();
        $memberCount = $this->group->users->count();
        $activityPercentage = $memberCount > 0 ? round(($todayRatings / $memberCount) * 100) : 0;

        return [
            'average_mood' => round($averageMood, 1),
            'mood_emoji' => $this->getMoodEmoji($averageMood),
            'activity_today' => [
                'members_logged' => $todayRatings,
                'total_members' => $memberCount,
                'percentage' => $activityPercentage,
            ],
            'distribution' => $distribution,
            'total_entries' => $groupEventMoods->count(),
            'mood_distribution' => [
                ['range' => '9-10', 'icon' => 'Great_icon.svg', 'count' => $distribution['excellent']],
                ['range' => '7-8', 'icon' => 'Happy_icon.svg', 'count' => $distribution['happy']],
                ['range' => '5-6', 'icon' => 'Normal_icon.svg', 'count' => $distribution['neutral']],
                ['range' => '3-4', 'icon' => 'Sad_icon.svg', 'count' => $distribution['sad']],
                ['range' => '1-2', 'icon' => 'depressed_icon.svg', 'count' => $distribution['depressed']],
            ],
            'total_count' => $groupEventMoods->count(),
        ];
    }

    private function calculateMemberStats()
    {
        $startDate = $this->getStartDate();

        // Get all personal mood entries from group members within period
        $moodEntries = $this->group->users()
            ->join('mood_entries', 'users.id', '=', 'mood_entries.user_id')
            ->where('mood_entries.created_at', '>=', $startDate)
            ->select('mood_entries.*')
            ->get();

        // Calculate average mood
        $averageMood = $moodEntries->avg('mood_score') ?? 0;

        // Calculate detailed mood distribution
        $distribution = [
            'excellent' => $moodEntries->whereBetween('mood_score', [9, 10])->count(),
            'happy' => $moodEntries->whereBetween('mood_score', [7, 8])->count(),
            'neutral' => $moodEntries->whereBetween('mood_score', [5, 6])->count(),
            'sad' => $moodEntries->whereBetween('mood_score', [3, 4])->count(),
            'depressed' => $moodEntries->whereBetween('mood_score', [1, 2])->count(),
        ];

        // Calculate activity today
        $todayEntries = $moodEntries->where('created_at', '>=', today())->count();
        $memberCount = $this->group->users->count();
        $activityPercentage = $memberCount > 0 ? round(($todayEntries / $memberCount) * 100) : 0;

        return [
            'average_mood' => round($averageMood, 1),
            'mood_emoji' => $this->getMoodEmoji($averageMood),
            'activity_today' => [
                'members_logged' => $todayEntries,
                'total_members' => $memberCount,
                'percentage' => $activityPercentage,
            ],
            'distribution' => $distribution,
            'total_entries' => $moodEntries->count(),
            'mood_distribution' => [
                ['range' => '9-10', 'icon' => 'Great_icon.svg', 'count' => $distribution['excellent']],
                ['range' => '7-8', 'icon' => 'Happy_icon.svg', 'count' => $distribution['happy']],
                ['range' => '5-6', 'icon' => 'Normal_icon.svg', 'count' => $distribution['neutral']],
                ['range' => '3-4', 'icon' => 'Sad_icon.svg', 'count' => $distribution['sad']],
                ['range' => '1-2', 'icon' => 'depressed_icon.svg', 'count' => $distribution['depressed']],
            ],
            'total_count' => $moodEntries->count(),
        ];
    }

    private function getStartDate()
    {
        return match($this->period) {
            'today' => today(),
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            default => Carbon::now()->subDays(7),
        };
    }

    private function getMoodEmoji(?float $score): string
    {
        if ($score === null || $score == 0) {
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
