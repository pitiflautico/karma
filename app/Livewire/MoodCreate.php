<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MoodCreate extends Component
{
    public $moodScore = 5;
    public $note = '';
    public $calendarEventId = null;
    public $calendarEvent = null;

    public function mount($eventId = null)
    {
        // Load calendar event context if provided via query param
        if ($eventId) {
            $this->calendarEventId = $eventId;
            $this->calendarEvent = CalendarEvent::where('id', $eventId)
                ->where('user_id', Auth::id())
                ->first();
        }
    }

    public function save()
    {
        $this->validate([
            'moodScore' => 'required|integer|min:1|max:10',
            'note' => 'nullable|string|max:500',
        ]);

        // Create new mood entry
        MoodEntry::create([
            'user_id' => Auth::id(),
            'mood_score' => $this->moodScore,
            'note' => $this->note,
            'calendar_event_id' => $this->calendarEventId,
            'is_manual' => !$this->calendarEventId,
        ]);

        session()->flash('success', 'Mood entry saved successfully!');

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }

    public function cancel()
    {
        return redirect()->route('dashboard');
    }

    public function getMoodCategory()
    {
        return match (true) {
            $this->moodScore <= 3 => ['label' => 'Low', 'color' => 'red'],
            $this->moodScore <= 6 => ['label' => 'Medium', 'color' => 'yellow'],
            $this->moodScore <= 8 => ['label' => 'Good', 'color' => 'green'],
            default => ['label' => 'Excellent', 'color' => 'blue'],
        };
    }

    public function render()
    {
        return view('livewire.mood-create')->layout('layouts.app');
    }
}
