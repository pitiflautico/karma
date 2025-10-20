<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\GroupEvent;
use Carbon\Carbon;
use Livewire\Component;

class GroupEvents extends Component
{
    public $groupId;
    public $group;
    public $filter = 'all';

    public function mount($groupId)
    {
        $this->groupId = $groupId;

        // Load group and verify user is a member
        $this->group = Group::findOrFail($groupId);

        // Verify user is a member of this group
        if (!$this->group->users->contains(auth()->id())) {
            abort(403, 'No eres miembro de este grupo');
        }
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $events = $this->getFilteredEvents();

        return view('livewire.group-events', [
            'events' => $events,
        ])->layout('layouts.app-mobile');
    }

    private function getFilteredEvents()
    {
        $query = GroupEvent::where('group_id', $this->groupId)
            ->with(['moods', 'creator']);

        // Apply filters
        switch ($this->filter) {
            case 'upcoming':
                $query->where('event_date', '>=', now())
                    ->orderBy('event_date', 'asc');
                break;

            case 'past':
                $query->where('event_date', '<', now())
                    ->orderBy('event_date', 'desc');
                break;

            case 'my_ratings':
                $query->whereHas('moods', function ($q) {
                    $q->where('user_id', auth()->id());
                })->orderBy('event_date', 'desc');
                break;

            default: // 'all'
                $query->orderBy('event_date', 'desc');
                break;
        }

        $events = $query->get();

        // Add calculated properties to each event
        $events = $events->map(function ($event) {
            $event->average_mood = $event->getAverageMood();
            $event->rating_count = $event->getRatingCount();
            $event->user_rated = $event->hasUserRated(auth()->user());
            $event->mood_emoji = $this->getMoodEmoji($event->average_mood);
            $event->is_past = $event->event_date < now();

            return $event;
        });

        return $events;
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
