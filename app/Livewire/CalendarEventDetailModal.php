<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use Livewire\Component;

class CalendarEventDetailModal extends Component
{
    public $showModal = false;
    public $event = null;

    protected $listeners = ['viewEventDetails' => 'showEventDetails'];

    public function showEventDetails($eventId)
    {
        $this->event = CalendarEvent::where('id', $eventId)
            ->where('user_id', auth()->id())
            ->first();

        if ($this->event) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->event = null;
    }

    public function render()
    {
        return view('livewire.calendar-event-detail-modal');
    }
}
