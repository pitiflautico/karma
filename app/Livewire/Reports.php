<?php

namespace App\Livewire;

use App\Services\MoodAnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Reports extends Component
{
    public $periodType = 'daily';
    public $dateFrom;
    public $dateTo;
    public $trendLimit = 30;

    // Summary statistics
    public $summaryStats = [];
    public $moodDistribution = [];
    public $bestAndWorst = [];
    public $correlations = [];
    public $trends = [];
    public $timeOfDayStats = [];

    protected $analyticsService;

    public function boot(MoodAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function mount()
    {
        // Default to last 30 days
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');

        $this->loadAnalytics();
    }

    public function updatedPeriodType()
    {
        $this->loadAnalytics();
    }

    public function updatedDateFrom()
    {
        $this->loadAnalytics();
    }

    public function updatedDateTo()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $user = Auth::user();
        $startDate = $this->dateFrom ? Carbon::parse($this->dateFrom) : null;
        $endDate = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null;

        // Load all analytics data
        $this->summaryStats = $this->analyticsService->getSummaryStatistics($user, $startDate, $endDate);
        $this->moodDistribution = $this->analyticsService->getMoodDistribution($user, $startDate, $endDate);
        $this->bestAndWorst = $this->analyticsService->getBestAndWorstMoods($user, 5);
        $this->correlations = $this->analyticsService->getCorrelationsByEventType($user, $startDate, $endDate);
        $this->trends = $this->analyticsService->getMoodTrendsByTimeRange($user, $this->periodType, $this->trendLimit);
        $this->timeOfDayStats = $this->analyticsService->getMoodByTimeOfDay($user, $startDate, $endDate);
    }

    public function exportData()
    {
        // TODO: Implement CSV/PDF export functionality
        session()->flash('info', 'Export functionality coming soon!');
    }

    public function render()
    {
        return view('livewire.reports')->layout('layouts.app');
    }
}
