<?php

namespace App\Livewire;

use App\Jobs\SyncGoogleCalendar;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CalendarSettings extends Component
{
    public $calendarSyncEnabled;
    public $quietHoursStart;
    public $quietHoursEnd;
    public $isSyncing = false;
    public $lastSyncAt;

    public function mount()
    {
        $user = Auth::user();
        $this->calendarSyncEnabled = $user->calendar_sync_enabled;
        $this->quietHoursStart = $user->quiet_hours_start ? $user->quiet_hours_start : '22:00';
        $this->quietHoursEnd = $user->quiet_hours_end ? $user->quiet_hours_end : '08:00';
        $this->lastSyncAt = $user->last_calendar_sync_at;
    }

    public function toggleCalendarSync()
    {
        $user = Auth::user();
        $user->update([
            'calendar_sync_enabled' => !$this->calendarSyncEnabled
        ]);

        $this->calendarSyncEnabled = !$this->calendarSyncEnabled;

        if ($this->calendarSyncEnabled) {
            session()->flash('success', 'Calendar sync enabled!');
        } else {
            session()->flash('success', 'Calendar sync disabled.');
        }
    }

    public function saveQuietHours()
    {
        $this->validate([
            'quietHoursStart' => 'required|date_format:H:i',
            'quietHoursEnd' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();
        $user->update([
            'quiet_hours_start' => $this->quietHoursStart,
            'quiet_hours_end' => $this->quietHoursEnd,
        ]);

        session()->flash('success', 'Quiet hours updated successfully!');
    }

    public function syncNow()
    {
        $user = Auth::user();

        if (!$user->calendar_sync_enabled || !$user->google_calendar_token) {
            session()->flash('error', 'Calendar sync is not enabled or you need to connect your Google Calendar first.');
            return;
        }

        $this->isSyncing = true;

        try {
            $service = new GoogleCalendarService();
            $events = $service->syncEvents($user, 50);

            $this->lastSyncAt = now();
            session()->flash('success', 'Calendar synced successfully! ' . count($events) . ' events synced.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to sync calendar: ' . $e->getMessage());
            \Log::error('Manual calendar sync failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->isSyncing = false;
    }

    public function disconnectCalendar()
    {
        $user = Auth::user();
        $user->update([
            'google_calendar_token' => null,
            'google_calendar_refresh_token' => null,
            'google_calendar_sync_token' => null,
            'calendar_sync_enabled' => false,
        ]);

        $this->calendarSyncEnabled = false;

        session()->flash('success', 'Google Calendar disconnected successfully!');
    }

    public function render()
    {
        $user = Auth::user();
        $isConnected = !empty($user->google_calendar_token);

        return view('livewire.calendar-settings', [
            'isConnected' => $isConnected,
        ])->layout('layouts.app');
    }
}
