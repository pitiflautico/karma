<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use App\Models\MoodPrompt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MoodPrompts extends Component
{
    public $prompts = [];
    public $selectedPrompt = null;
    public $moodScore = 5;
    public $note = '';
    public $showModal = false;

    public function mount()
    {
        $this->loadPrompts();
    }

    public function loadPrompts()
    {
        $this->prompts = MoodPrompt::where('user_id', Auth::id())
            ->where('is_completed', false)
            ->orderBy('event_end_time', 'desc')
            ->get();
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

        // Create mood entry
        $moodEntry = MoodEntry::create([
            'user_id' => Auth::id(),
            'mood_score' => $this->moodScore,
            'note' => $this->note,
            'is_manual' => false,
            'calendar_event_id' => $this->selectedPrompt->calendar_event_id,
        ]);

        // Mark prompt as completed
        $this->selectedPrompt->update([
            'is_completed' => true,
            'completed_at' => now(),
            'mood_entry_id' => $moodEntry->id,
        ]);

        $this->closeModal();
        $this->loadPrompts();
        session()->flash('success', 'Mood logged successfully!');
    }

    public function dismissPrompt($promptId)
    {
        $prompt = MoodPrompt::find($promptId);
        if ($prompt && $prompt->user_id === Auth::id()) {
            $prompt->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        $this->loadPrompts();
        session()->flash('success', 'Prompt dismissed.');
    }

    public function render()
    {
        return view('livewire.mood-prompts')->layout('layouts.app');
    }
}
