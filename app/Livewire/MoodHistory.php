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

    public function mount()
    {
        // Set default date range to last 30 days
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
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
        $this->dispatch('openMoodEntryModal', moodEntryId: $moodId);
    }

    public function confirmDelete($moodId)
    {
        $this->moodToDelete = $moodId;
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

    #[On('moodEntrySaved')]
    public function refreshData()
    {
        // This will trigger a re-render
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

        return view('livewire.mood-history', [
            'moodEntries' => $moodEntries,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
