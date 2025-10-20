<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MoodHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $moodLevel = '';
    public $showDeleteConfirm = false;
    public $moodToDelete = null;

    // New: View mode (list or calendar)
    public $activeView = 'list';

    // Calendar navigation
    public $calendarMonth;
    public $calendarYear;

    // Pagination for list view (show 3 days initially)
    public $showAllDays = false;

    public function mount()
    {
        // Set default date range to last 30 days
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');

        // Initialize calendar to current month
        $this->calendarMonth = Carbon::now()->month;
        $this->calendarYear = Carbon::now()->year;
    }

    public function switchView($view)
    {
        $this->activeView = $view;
        // Reset "show all" when switching views
        $this->showAllDays = false;
    }

    public function loadMore()
    {
        $this->showAllDays = true;
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
    }

    public function goToToday()
    {
        $this->calendarMonth = Carbon::now()->month;
        $this->calendarYear = Carbon::now()->year;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMoodLevel()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->moodLevel = '';
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->resetPage();
    }

    public function editMood($moodId)
    {
        $this->dispatch('openMoodDetailModal', moodId: $moodId);
    }

    #[On('view-mood')]
    public function viewMood($id)
    {
        $this->dispatch('openMoodDetailModal', moodId: $id);
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->moodToDelete = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->moodToDelete = null;
    }

    public function deleteMood()
    {
        if ($this->moodToDelete) {
            $mood = MoodEntry::where('id', $this->moodToDelete)
                ->where('user_id', Auth::id())
                ->first();

            if ($mood) {
                $mood->delete();
                session()->flash('success', 'Mood entry deleted successfully!');
                $this->dispatch('moodEntrySaved');
            }
        }

        $this->showDeleteConfirm = false;
        $this->moodToDelete = null;
    }

    public function redirectTo($url)
    {
        return redirect($url);
    }

    #[On('moodEntrySaved')]
    public function refreshData()
    {
        // This will trigger a re-render
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

    private function getCalendarData()
    {
        $startOfMonth = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->endOfMonth();

        // Get all moods for this month
        $moods = MoodEntry::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($mood) {
                return $mood->created_at->format('Y-m-d');
            });

        // Build calendar grid starting on Monday
        $calendar = [];
        $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Adjust for Monday start (0 = Monday, 6 = Sunday)
        $adjustedStartDay = $startDayOfWeek === 0 ? 6 : $startDayOfWeek - 1;
        $daysInMonth = $startOfMonth->daysInMonth;

        // Add empty cells for days before month starts
        for ($i = 0; $i < $adjustedStartDay; $i++) {
            $calendar[] = null;
        }

        // Add days of month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->addDays($day - 1);
            $dateKey = $date->format('Y-m-d');

            $dayData = [
                'day' => $day,
                'date' => $dateKey,
                'isToday' => $date->isToday(),
                'moods' => $moods->get($dateKey, collect()),
            ];

            $calendar[] = $dayData;
        }

        return $calendar;
    }

    private function getMoodsByDate()
    {
        // Get all moods grouped by date for list view
        $query = MoodEntry::where('user_id', Auth::id())
            ->with('calendarEvent');

        // Search filter
        if ($this->search) {
            $query->where('note', 'like', '%' . $this->search . '%');
        }

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Mood level filter
        if ($this->moodLevel) {
            switch ($this->moodLevel) {
                case 'low':
                    $query->whereBetween('mood_score', [1, 3]);
                    break;
                case 'medium':
                    $query->whereBetween('mood_score', [4, 6]);
                    break;
                case 'good':
                    $query->whereBetween('mood_score', [7, 8]);
                    break;
                case 'excellent':
                    $query->whereBetween('mood_score', [9, 10]);
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->get()->groupBy(function($mood) {
            if ($mood->created_at->isToday()) {
                return 'Today';
            }
            return $mood->created_at->format('M d, Y');
        });
    }

    public function render()
    {
        $query = MoodEntry::where('user_id', Auth::id())
            ->with('calendarEvent');

        // Search filter
        if ($this->search) {
            $query->where('note', 'like', '%' . $this->search . '%');
        }

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Mood level filter
        if ($this->moodLevel) {
            switch ($this->moodLevel) {
                case 'low':
                    $query->whereBetween('mood_score', [1, 3]);
                    break;
                case 'medium':
                    $query->whereBetween('mood_score', [4, 6]);
                    break;
                case 'good':
                    $query->whereBetween('mood_score', [7, 8]);
                    break;
                case 'excellent':
                    $query->whereBetween('mood_score', [9, 10]);
                    break;
            }
        }

        $moodEntries = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => MoodEntry::where('user_id', Auth::id())->count(),
            'average' => round(MoodEntry::where('user_id', Auth::id())->avg('mood_score'), 1),
            'thisWeek' => MoodEntry::where('user_id', Auth::id())
                ->where('created_at', '>=', Carbon::now()->startOfWeek())
                ->count(),
        ];

        // Mobile view
        if ($this->isMobileDevice()) {
            $allMoodsByDate = $this->getMoodsByDate();

            // Limit to 3 days if not showing all
            $moodsByDate = $allMoodsByDate;
            $hasMoreDays = false;

            if (!$this->showAllDays && $allMoodsByDate->count() > 3) {
                $moodsByDate = $allMoodsByDate->take(3);
                $hasMoreDays = true;
            }

            $calendarData = $this->getCalendarData();
            $currentMonthName = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->format('F Y');

            return view('livewire.mood-history-mobile', [
                'moodsByDate' => $moodsByDate,
                'calendarData' => $calendarData,
                'currentMonthName' => $currentMonthName,
                'stats' => $stats,
                'hasMoreDays' => $hasMoreDays,
            ])->layout('layouts.app-mobile');
        }

        return view('livewire.mood-history', [
            'moodEntries' => $moodEntries,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
