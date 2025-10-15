<?php

namespace App\Livewire;

use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CalendarEvents extends Component
{
    public $events = [];
    public $syncing = false;
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        // Set default date range to next 30 days
        $this->dateFrom = Carbon::now()->format('Y-m-d');
        $this->dateTo = Carbon::now()->addDays(30)->format('Y-m-d');

        $this->loadEvents();
    }

    public function syncEvents()
    {
        $this->syncing = true;

        $service = new GoogleCalendarService();
        $this->events = $service->syncEvents(Auth::user(), 50);

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
            ->get();
    }

    private function isMobileDevice()
    {
        // 1. Session variable
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        // 2. Query parameter ?mobile=1
        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        // 3. User-Agent
        $userAgent = request()->header('User-Agent');
        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getEventsByDate()
    {
        $query = Auth::user()->calendarEvents();

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('start_time', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('start_time', '<=', $this->dateTo);
        }

        return $query->orderBy('start_time', 'asc')->get()->groupBy(function($event) {
            if ($event->start_time->isToday()) {
                return 'Today';
            }
            if ($event->start_time->isTomorrow()) {
                return 'Tomorrow';
            }
            return $event->start_time->format('M d, Y');
        });
    }

    public function render()
    {
        // Mobile view
        if ($this->isMobileDevice()) {
            $eventsByDate = $this->getEventsByDate();

            return view('livewire.calendar-events-mobile', [
                'eventsByDate' => $eventsByDate,
            ])->layout('layouts.app-mobile');
        }

        return view('livewire.calendar-events', [
            'events' => $this->events,
        ])->layout('layouts.app');
    }
}
