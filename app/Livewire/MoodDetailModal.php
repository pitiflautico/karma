<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MoodDetailModal extends Component
{
    public $isOpen = false;
    public $mood = null;

    #[On('openMoodDetailModal')]
    public function openModal($moodId)
    {
        $this->mood = MoodEntry::where('id', $moodId)
            ->where('user_id', Auth::id())
            ->with('calendarEvent')
            ->first();

        if ($this->mood) {
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->mood = null;
    }

    public function editMood()
    {
        if ($this->mood) {
            $this->closeModal();
            $this->dispatch('openMoodEntryModal', moodEntryId: $this->mood->id);
        }
    }

    public function render()
    {
        return view('livewire.mood-detail-modal');
    }
}
