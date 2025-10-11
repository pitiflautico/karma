<?php

namespace App\Livewire;

use App\Models\AIUsageLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public $aiInsightsEnabled = true;
    public $aiInsightsFrequency = 'manual'; // manual, daily, weekly
    public $calendarSyncFrequency = 60; // minutes
    public $moodRemindersEnabled = true;
    public $moodReminderTime = '09:00';

    public function mount()
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];

        $this->aiInsightsEnabled = $settings['ai_insights_enabled'] ?? true;
        $this->aiInsightsFrequency = $settings['ai_insights_frequency'] ?? 'manual';
        $this->calendarSyncFrequency = $settings['calendar_sync_frequency'] ?? 60;
        $this->moodRemindersEnabled = $settings['mood_reminders_enabled'] ?? true;
        $this->moodReminderTime = $settings['mood_reminder_time'] ?? '09:00';
    }

    public function saveSettings()
    {
        $user = Auth::user();

        $settings = [
            'ai_insights_enabled' => $this->aiInsightsEnabled,
            'ai_insights_frequency' => $this->aiInsightsFrequency,
            'calendar_sync_frequency' => $this->calendarSyncFrequency,
            'mood_reminders_enabled' => $this->moodRemindersEnabled,
            'mood_reminder_time' => $this->moodReminderTime,
        ];

        $user->update(['settings' => $settings]);

        session()->flash('success', '¡Configuración guardada correctamente!');
    }

    public function getAiUsageStats()
    {
        return AIUsageLog::getUsageStatsForUser(Auth::id(), 30);
    }

    public function getRecentAiLogs()
    {
        return AIUsageLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $aiUsageStats = $this->getAiUsageStats();
        $recentAiLogs = $this->getRecentAiLogs();
        $totalCost = AIUsageLog::getTotalCostForUser(Auth::id());

        return view('livewire.settings', [
            'aiUsageStats' => $aiUsageStats,
            'recentAiLogs' => $recentAiLogs,
            'totalCost' => $totalCost,
        ])->layout('layouts.app');
    }
}
