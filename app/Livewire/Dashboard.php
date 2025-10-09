<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use App\Models\MoodPrompt;
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

        // Get pending mood prompts (past events not rated)
        $pendingPrompts = MoodPrompt::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderBy('event_end_time', 'desc')
            ->take(3)
            ->get();

        return view('livewire.dashboard', [
            'user' => $user,
            'recentMoods' => $recentMoods,
            'averageMood' => $averageMood,
            'nextEvent' => $nextEvent,
            'pendingPrompts' => $pendingPrompts,
        ])->layout('layouts.app');
    }
}
