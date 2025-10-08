<?php

namespace App\Livewire;

use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CalendarEvents extends Component
{
    public $events = [];
    public $syncing = false;

    public function mount()
    {
        $this->loadEvents();
    }

    public function syncEvents()
    {
        $this->syncing = true;

        $service = new GoogleCalendarService();
        $this->events = $service->syncEvents(Auth::user(), 10);

        $this->syncing = false;

        session()->flash('success', 'Events synchronized successfully!');
    }

    public function loadEvents()
    {
        // Load already synced events
        $this->events = Auth::user()
            ->calendarEvents()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.calendar-events')
            ->layout('layouts.app');
    }
}
