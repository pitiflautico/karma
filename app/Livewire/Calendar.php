<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\MoodEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Calendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $daysInMonth = [];
    public $selectedDate = null;
    public $selectedDateMoods = [];
    public $selectedDateEvents = [];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->generateCalendar();
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->generateCalendar();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $selectedDate = Carbon::parse($date);

        // Get moods for selected date
        $this->selectedDateMoods = MoodEntry::where('user_id', Auth::id())
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get events for selected date
        $this->selectedDateEvents = CalendarEvent::where('user_id', Auth::id())
            ->whereDate('start_time', '<=', $selectedDate)
            ->whereDate('end_time', '>=', $selectedDate)
            ->orderBy('start_time')
            ->get();
    }

    #[On('moodEntrySaved')]
    public function refreshCalendar()
    {
        $this->generateCalendar();
        if ($this->selectedDate) {
            $this->selectDate($this->selectedDate);
        }
    }

    private function generateCalendar()
    {
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        $this->daysInMonth = [];

        // Add empty cells for days before the first day of the month
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->daysInMonth[] = null;
        }

        // Get all moods for this month
        $monthMoods = MoodEntry::where('user_id', Auth::id())
            ->whereYear('created_at', $this->currentYear)
            ->whereMonth('created_at', $this->currentMonth)
            ->get()
            ->groupBy(function($mood) {
                return Carbon::parse($mood->created_at)->format('Y-m-d');
            });

        // Add days of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $day);
            $dateKey = $date->format('Y-m-d');

            $dayMoods = $monthMoods->get($dateKey, collect());
            $avgMood = $dayMoods->avg('mood_score');

            $this->daysInMonth[] = [
                'day' => $day,
                'date' => $dateKey,
                'isToday' => $date->isToday(),
                'moodCount' => $dayMoods->count(),
                'avgMood' => $avgMood,
            ];
        }
    }

    public function render()
    {
        return view('livewire.calendar')
            ->layout('layouts.app');
    }
}
