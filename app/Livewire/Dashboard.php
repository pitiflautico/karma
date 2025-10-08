<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    #[On('moodEntrySaved')]
    public function refreshData()
    {
        // This will trigger a re-render
    }

    public function render()
    {
        $user = Auth::user();

        // Get recent mood entries
        $recentMoods = MoodEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        // Calculate average mood
        $averageMood = MoodEntry::where('user_id', $user->id)
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->avg('mood_score');

        // Get next upcoming event
        $nextEvent = null;
        if ($user->calendar_sync_enabled) {
            $nextEvent = $user->calendarEvents()
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->first();
        }

        return view('livewire.dashboard', [
            'user' => $user,
            'recentMoods' => $recentMoods,
            'averageMood' => $averageMood,
            'nextEvent' => $nextEvent,
        ])->layout('layouts.app');
    }
}
