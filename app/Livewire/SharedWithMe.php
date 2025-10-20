<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use App\Models\SharedAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SharedWithMe extends Component
{
    use WithPagination;

    public $selectedOwnerId = null;
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatedSelectedOwnerId()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Get all users who have shared data with me
        $sharedAccesses = SharedAccess::where('shared_with_user_id', $user->id)
            ->with('owner')
            ->get();

        // Get mood entries from selected user or all users who shared with me
        $moodEntries = collect();

        if ($sharedAccesses->isNotEmpty()) {
            $query = MoodEntry::query()
                ->with(['calendarEvent', 'user']);

            if ($this->selectedOwnerId) {
                // Get specific user's shared data
                $access = $sharedAccesses->where('owner_id', $this->selectedOwnerId)->first();

                if ($access) {
                    $query->where('user_id', $this->selectedOwnerId);

                    // Apply privacy filters based on permissions
                    $query->when(!$access->can_view_notes, function ($q) {
                        $q->select([
                            'id', 'user_id', 'calendar_event_id', 'mood_score',
                            'created_at', 'updated_at'
                        ]);
                    });
                }
            } else {
                // Get data from all users who shared with me
                $ownerIds = $sharedAccesses->pluck('owner_id');
                $query->whereIn('user_id', $ownerIds);
            }

            // Apply date filters
            if ($this->dateFrom) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            }

            $moodEntries = $query->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('livewire.shared-with-me', [
            'sharedAccesses' => $sharedAccesses,
            'moodEntries' => $moodEntries,
        ])->layout('layouts.app-mobile');
    }

    /**
     * Check if current user can view notes for a specific mood entry.
     */
    public function canViewNotes($ownerId): bool
    {
        $access = SharedAccess::where('owner_id', $ownerId)
            ->where('shared_with_user_id', Auth::id())
            ->first();

        return $access && $access->can_view_notes;
    }

    /**
     * Check if current user can view selfies for a specific owner.
     */
    public function canViewSelfies($ownerId): bool
    {
        $access = SharedAccess::where('owner_id', $ownerId)
            ->where('shared_with_user_id', Auth::id())
            ->first();

        return $access && $access->can_view_selfies;
    }
}
