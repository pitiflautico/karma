<?php

namespace App\Livewire;

use App\Models\GroupEvent;
use App\Models\GroupEventMood;
use Livewire\Component;

class RateGroupEvent extends Component
{
    public $eventId;
    public $event;
    public $selectedMood = null;
    public $note = '';
    public $userRating = null;

    // Mood data (1-10 scale)
    public $moods = [
        ['score' => 1, 'icon' => '😢', 'name' => 'Terrible'],
        ['score' => 2, 'icon' => '☹️', 'name' => 'Very Bad'],
        ['score' => 3, 'icon' => '😕', 'name' => 'Bad'],
        ['score' => 4, 'icon' => '😐', 'name' => 'Poor'],
        ['score' => 5, 'icon' => '😶', 'name' => 'Okay'],
        ['score' => 6, 'icon' => '🙂', 'name' => 'Fine'],
        ['score' => 7, 'icon' => '😊', 'name' => 'Good'],
        ['score' => 8, 'icon' => '😄', 'name' => 'Great'],
        ['score' => 9, 'icon' => '😁', 'name' => 'Amazing'],
        ['score' => 10, 'icon' => '🤩', 'name' => 'Perfect'],
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;

        // Load event with group and moods
        $this->event = GroupEvent::with(['group.users', 'moods'])->findOrFail($eventId);

        // Verify user is a member of this group
        if (!$this->event->group->users->contains(auth()->id())) {
            abort(403, 'No eres miembro de este grupo');
        }

        // Load existing rating if user has already rated
        $this->userRating = $this->event->getUserRating(auth()->user());

        if ($this->userRating) {
            $this->selectedMood = $this->userRating->mood_score;
            $this->note = $this->userRating->note ?? '';
        }
    }

    public function selectMood($score)
    {
        $this->selectedMood = $score;
    }

    public function submitRating()
    {
        // Validate
        if (!$this->selectedMood) {
            session()->flash('error', 'Por favor selecciona un mood');
            return;
        }

        // Get mood data
        $moodData = collect($this->moods)->firstWhere('score', $this->selectedMood);

        if ($this->userRating) {
            // Update existing rating
            $this->userRating->update([
                'mood_score' => $this->selectedMood,
                'mood_icon' => $moodData['icon'],
                'mood_name' => $moodData['name'],
                'note' => $this->note,
            ]);

            session()->flash('success', 'Valoración actualizada exitosamente');
        } else {
            // Create new rating
            GroupEventMood::create([
                'group_event_id' => $this->eventId,
                'user_id' => auth()->id(),
                'mood_score' => $this->selectedMood,
                'mood_icon' => $moodData['icon'],
                'mood_name' => $moodData['name'],
                'note' => $this->note,
            ]);

            session()->flash('success', 'Valoración guardada exitosamente');
        }

        // Reload event to update stats
        $this->event = GroupEvent::with(['group.users', 'moods'])->findOrFail($this->eventId);
        $this->userRating = $this->event->getUserRating(auth()->user());
    }

    public function render()
    {
        $groupStats = $this->calculateGroupStats();

        return view('livewire.rate-group-event', [
            'groupStats' => $groupStats,
        ])->layout('layouts.app-mobile');
    }

    private function calculateGroupStats()
    {
        $moods = $this->event->moods;

        if ($moods->isEmpty()) {
            return [
                'average_mood' => null,
                'mood_emoji' => '😶',
                'rating_count' => 0,
                'distribution' => [],
                'has_ratings' => false,
            ];
        }

        $averageMood = $moods->avg('mood_score');

        // Calculate distribution
        $distribution = [];
        foreach (range(1, 10) as $score) {
            $count = $moods->where('mood_score', $score)->count();
            $percentage = $moods->count() > 0 ? round(($count / $moods->count()) * 100) : 0;
            $distribution[] = [
                'score' => $score,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return [
            'average_mood' => round($averageMood, 1),
            'mood_emoji' => $this->getMoodEmoji($averageMood),
            'rating_count' => $moods->count(),
            'distribution' => $distribution,
            'has_ratings' => true,
        ];
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
