<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MoodEntryForm extends Component
{
    public $isOpen = false;
    public $moodScore = 5;
    public $note = '';
    public $calendarEventId = null;
    public $calendarEvent = null;
    public $editMode = false;
    public $moodEntryId = null;

    #[On('openMoodEntryModal')]
    public function openModal($calendarEventId = null, $moodEntryId = null)
    {
        $this->isOpen = true;
        $this->moodScore = 5;
        $this->note = '';
        $this->calendarEventId = $calendarEventId;
        $this->moodEntryId = $moodEntryId;
        $this->editMode = false;

        // Load calendar event context if provided
        if ($calendarEventId) {
            $this->calendarEvent = CalendarEvent::find($calendarEventId);
        }

        // Load existing mood entry for editing
        if ($moodEntryId) {
            $moodEntry = MoodEntry::where('id', $moodEntryId)
                ->where('user_id', Auth::id())
                ->first();

            if ($moodEntry) {
                $this->editMode = true;
                $this->moodScore = $moodEntry->mood_score;
                $this->note = $moodEntry->note ?? '';
                $this->calendarEventId = $moodEntry->calendar_event_id;

                if ($this->calendarEventId) {
                    $this->calendarEvent = $moodEntry->calendarEvent;
                }
            }
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['moodScore', 'note', 'calendarEventId', 'calendarEvent', 'editMode', 'moodEntryId']);
    }

    public function save()
    {
        $this->validate([
            'moodScore' => 'required|integer|min:1|max:10',
            'note' => 'nullable|string|max:500',
        ]);

        if ($this->editMode && $this->moodEntryId) {
            // Update existing mood entry
            $moodEntry = MoodEntry::where('id', $this->moodEntryId)
                ->where('user_id', Auth::id())
                ->first();

            if ($moodEntry) {
                $moodEntry->update([
                    'mood_score' => $this->moodScore,
                    'note' => $this->note,
                ]);

                session()->flash('success', 'Mood entry updated successfully!');
            }
        } else {
            // Create new mood entry
            MoodEntry::create([
                'user_id' => Auth::id(),
                'mood_score' => $this->moodScore,
                'note' => $this->note,
                'calendar_event_id' => $this->calendarEventId,
                'is_manual' => !$this->calendarEventId,
            ]);

            session()->flash('success', 'Mood entry saved successfully!');
        }

        $this->closeModal();
        $this->dispatch('moodEntrySaved');
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
        return view('livewire.mood-entry-form');
    }
}
