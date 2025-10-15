<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\MoodEntry;
use App\Models\MoodPrompt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedPrompt = null;
    public $moodScore = 5;
    public $note = '';
    public $showModal = false;


    #[On('moodEntrySaved')]
    public function refreshData()
    {
        // This will trigger a re-render
    }

    #[On('view-mood')]
    public function viewMood($id)
    {
        $this->dispatch('openMoodDetailModal', moodId: $id);
    }

    public function openMoodEntryModal()
    {
        $this->dispatch('openMoodEntryModal');
    }

    public function openPrompt($promptId)
    {
        $this->selectedPrompt = MoodPrompt::find($promptId);
        $this->moodScore = 5;
        $this->note = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPrompt = null;
        $this->moodScore = 5;
        $this->note = '';
    }

    public function submitMood()
    {
        $this->validate([
            'moodScore' => 'required|integer|min:1|max:10',
            'note' => 'nullable|string|max:1000',
        ]);

        $moodEntry = MoodEntry::create([
            'user_id' => Auth::id(),
            'mood_score' => $this->moodScore,
            'note' => $this->note,
            'is_manual' => false,
            'calendar_event_id' => $this->selectedPrompt->calendar_event_id,
        ]);

        $this->selectedPrompt->update([
            'is_completed' => true,
            'completed_at' => now(),
            'mood_entry_id' => $moodEntry->id,
        ]);

        $this->closeModal();
        session()->flash('success', 'Mood logged successfully!');
    }

    public function skipPrompt($promptId)
    {
        $prompt = MoodPrompt::find($promptId);
        if ($prompt && $prompt->user_id === Auth::id()) {
            $prompt->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        session()->flash('success', 'Prompt skipped.');
    }

    /**
     * Calculate mood streak (consecutive days with at least one mood entry)
     */
    private function calculateMoodStreak()
    {
        $userId = Auth::id();
        $streak = 0;
        $currentDate = Carbon::today();

        while (true) {
            $hasEntry = MoodEntry::where('user_id', $userId)
                ->whereDate('created_at', $currentDate)
                ->exists();

            if (!$hasEntry) {
                break;
            }

            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Get mood goal data for the last 7 days
     */
    private function getMoodGoalData()
    {
        $userId = Auth::id();
        $last7Days = [];

        // Get last 7 days including today
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Get average mood for this day
            $avgMood = MoodEntry::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->avg('mood_score');

            $last7Days[] = [
                'date' => $date,
                'avg_mood' => $avgMood,
                'has_mood' => $avgMood !== null,
            ];
        }

        // Calculate happy streak (days with mood >= 7)
        $happyStreak = 0;
        foreach (array_reverse($last7Days) as $day) {
            if ($day['has_mood'] && $day['avg_mood'] >= 7) {
                $happyStreak++;
            } else {
                break;
            }
        }

        return [
            'days' => $last7Days,
            'happyStreak' => $happyStreak,
        ];
    }

    /**
     * Get contextual message based on mood comparison
     */
    private function getMoodContextMessage($currentAvgMood)
    {
        $userId = Auth::id();

        // Get average from previous 7 days (days 8-14 ago)
        $previousAvgMood = MoodEntry::where('user_id', $userId)
            ->whereDate('created_at', '>=', now()->subDays(14))
            ->whereDate('created_at', '<', now()->subDays(7))
            ->avg('mood_score');

        if (!$previousAvgMood) {
            return 'Keep tracking your mood!';
        }

        $diff = $currentAvgMood - $previousAvgMood;

        if ($diff > 1) {
            return 'You are more joyful than usual';
        } elseif ($diff < -1) {
            return 'Your mood has been lower recently';
        } else {
            return 'Your mood is stable';
        }
    }

    /**
     * Get the representative mood name for an average score
     */
    private function getMoodNameForAverage($avgScore)
    {
        return match (true) {
            $avgScore <= 2 => 'Depressed',
            $avgScore <= 4 => 'Sad',
            $avgScore <= 6 => 'Neutral',
            $avgScore <= 8 => 'Happy',
            default => 'Overjoyed',
        };
    }

    /**
     * Get the mood icon for an average score
     */
    private function getMoodIconForAverage($avgScore)
    {
        return match (true) {
            $avgScore <= 2 => 'depressed_icon.svg',
            $avgScore <= 4 => 'Sad_icon.svg',
            $avgScore <= 6 => 'Normal_icon.svg',
            $avgScore <= 8 => 'Happy_icon.svg',
            default => 'Great_icon.svg',
        };
    }

    /**
     * Detect if the request is from a mobile device or native app
     */
    private function isMobileDevice()
    {
        // Check if there's a session variable indicating mobile/native app
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        // Check for mobile query parameter (can be set by native app on first load)
        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        // Check user agent for mobile devices
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render()
    {
        $user = Auth::user();

        // Get last mood entry today
        $todaysMood = MoodEntry::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->first();

        // Get recent mood entries (last 3 for preview)
        $recentMoods = MoodEntry::where('user_id', $user->id)
            ->with('calendarEvent')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Calculate average mood for last 7 days
        $averageMood = MoodEntry::where('user_id', $user->id)
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->avg('mood_score');

        $averageMood = $averageMood ? round($averageMood, 1) : null;

        // Mood context data
        $moodData = null;
        if ($averageMood) {
            $moodData = [
                'score' => $averageMood,
                'name' => $this->getMoodNameForAverage($averageMood),
                'icon' => $this->getMoodIconForAverage($averageMood),
                'message' => $this->getMoodContextMessage($averageMood),
                'logged_time' => $todaysMood ? $todaysMood->created_at->format('g:i A') : null,
            ];
        }

        // Calculate mood streak
        $moodStreak = $this->calculateMoodStreak();

        // Get mood goal data
        $moodGoalData = $this->getMoodGoalData();

        // Get next upcoming event/reminder
        $nextReminder = CalendarEvent::where('user_id', $user->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->first();

        // Get pending mood prompts (past events not rated)
        $pendingPrompts = MoodPrompt::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderBy('event_end_time', 'desc')
            ->take(3)
            ->get();

        // Detect if mobile device and render appropriate view/layout
        if ($this->isMobileDevice()) {
            return view('livewire.dashboard-mobile', [
                'user' => $user,
                'moodData' => $moodData,
                'recentMoods' => $recentMoods,
                'moodStreak' => $moodStreak,
                'moodGoalData' => $moodGoalData,
                'nextReminder' => $nextReminder,
                'pendingPrompts' => $pendingPrompts,
            ])->layout('layouts.app-mobile');
        }

        return view('livewire.dashboard', [
            'user' => $user,
            'recentMoods' => $recentMoods,
            'averageMood' => $averageMood,
            'nextEvent' => $nextReminder,
            'pendingPrompts' => $pendingPrompts,
        ])->layout('layouts.app');
    }
}
