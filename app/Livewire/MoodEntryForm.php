<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MoodEntryForm extends Component
{
    public $isOpen = false;
    public $moodScore = 5;
    public $note = '';

    #[On('openMoodEntryModal')]
    public function openModal()
    {
        $this->isOpen = true;
        $this->moodScore = 5;
        $this->note = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['moodScore', 'note']);
    }

    public function save()
    {
        $this->validate([
            'moodScore' => 'required|integer|min:1|max:10',
            'note' => 'nullable|string|max:1000',
        ]);

        MoodEntry::create([
            'user_id' => Auth::id(),
            'mood_score' => $this->moodScore,
            'note' => $this->note,
            'is_manual' => true,
        ]);

        $this->closeModal();

        session()->flash('success', 'Mood entry saved successfully!');

        $this->dispatch('moodEntrySaved');
    }

    public function render()
    {
        return view('livewire.mood-entry-form');
    }
}
