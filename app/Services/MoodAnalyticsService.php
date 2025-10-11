<?php

namespace App\Services;

use App\Models\MoodEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MoodAnalyticsService
{
    /**
     * Get correlation analysis between event types and mood scores.
     */
    public function getCorrelationsByEventType(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = MoodEntry::where('mood_entries.user_id', $user->id)
            ->whereHas('calendarEvent');

        if ($startDate) {
            $query->where('mood_entries.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('mood_entries.created_at', '<=', $endDate);
        }

        $results = $query->join('calendar_events', 'mood_entries.calendar_event_id', '=', 'calendar_events.id')
            ->select(
                'calendar_events.event_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(mood_entries.mood_score) as avg_mood'),
                DB::raw('MIN(mood_entries.mood_score) as min_mood'),
                DB::raw('MAX(mood_entries.mood_score) as max_mood')
            )
            ->groupBy('calendar_events.event_type')
            ->orderBy('avg_mood', 'desc')
            ->get();

        return $results->map(function ($item) {
            return [
                'event_type' => $item->event_type ?? 'Unknown',
                'count' => $item->count,
                'avg_mood' => round($item->avg_mood, 2),
                'min_mood' => $item->min_mood,
                'max_mood' => $item->max_mood,
                'category' => $this->getMoodCategory($item->avg_mood),
            ];
        })->toArray();
    }

    /**
     * Get mood trends over time (daily, weekly, or monthly).
     */
    public function getMoodTrendsByTimeRange(User $user, string $period = 'daily', int $limit = 30): array
    {
        $groupFormat = match ($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%W',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $results = MoodEntry::where('user_id', $user->id)
            ->select(
                DB::raw("strftime('{$groupFormat}', created_at) as period"),
                DB::raw('AVG(mood_score) as avg_mood'),
                DB::raw('COUNT(*) as entry_count'),
                DB::raw('MIN(mood_score) as min_mood'),
                DB::raw('MAX(mood_score) as max_mood')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        return $results->map(function ($item) use ($period) {
            return [
                'period' => $item->period,
                'formatted_period' => $this->formatPeriod($item->period, $period),
                'avg_mood' => round($item->avg_mood, 2),
                'entry_count' => $item->entry_count,
                'min_mood' => $item->min_mood,
                'max_mood' => $item->max_mood,
                'category' => $this->getMoodCategory($item->avg_mood),
            ];
        })->toArray();
    }

    /**
     * Get summary statistics for a user.
     */
    public function getSummaryStatistics(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = MoodEntry::where('user_id', $user->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as total_entries'),
            DB::raw('AVG(mood_score) as avg_mood'),
            DB::raw('MIN(mood_score) as min_mood'),
            DB::raw('MAX(mood_score) as max_mood')
        )->first();

        // Get most common mood category
        $moodDistribution = $this->getMoodDistribution($user, $startDate, $endDate);
        $mostCommonCategory = collect($moodDistribution)->sortByDesc('count')->first();

        // Get streak information
        $currentStreak = $this->getCurrentStreak($user);
        $longestStreak = $this->getLongestStreak($user);

        return [
            'total_entries' => $stats->total_entries ?? 0,
            'avg_mood' => $stats->avg_mood ? round($stats->avg_mood, 2) : 0,
            'min_mood' => $stats->min_mood ?? 0,
            'max_mood' => $stats->max_mood ?? 0,
            'mood_category' => $this->getMoodCategory($stats->avg_mood ?? 0),
            'most_common_category' => $mostCommonCategory['category'] ?? 'N/A',
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
        ];
    }

    /**
     * Get mood distribution by category.
     */
    public function getMoodDistribution(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = MoodEntry::where('user_id', $user->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $entries = $query->select('mood_score')->get();

        $distribution = [
            'low' => 0,
            'medium' => 0,
            'good' => 0,
            'excellent' => 0,
        ];

        foreach ($entries as $entry) {
            $category = strtolower($this->getMoodCategory($entry->mood_score));
            if (isset($distribution[$category])) {
                $distribution[$category]++;
            }
        }

        return collect($distribution)->map(function ($count, $category) use ($entries) {
            return [
                'category' => ucfirst($category),
                'count' => $count,
                'percentage' => $entries->count() > 0 ? round(($count / $entries->count()) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }

    /**
     * Get best and worst days/events.
     */
    public function getBestAndWorstMoods(User $user, int $limit = 5): array
    {
        $best = MoodEntry::where('user_id', $user->id)
            ->with('calendarEvent')
            ->orderBy('mood_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'mood_score' => $entry->mood_score,
                    'event_title' => $entry->calendarEvent->title ?? 'Manual Entry',
                    'date' => $entry->created_at->format('Y-m-d'),
                    'note' => $entry->note,
                ];
            });

        $worst = MoodEntry::where('user_id', $user->id)
            ->with('calendarEvent')
            ->orderBy('mood_score', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'mood_score' => $entry->mood_score,
                    'event_title' => $entry->calendarEvent->title ?? 'Manual Entry',
                    'date' => $entry->created_at->format('Y-m-d'),
                    'note' => $entry->note,
                ];
            });

        return [
            'best' => $best->toArray(),
            'worst' => $worst->toArray(),
        ];
    }

    /**
     * Get current tracking streak (consecutive days with entries).
     */
    private function getCurrentStreak(User $user): int
    {
        $entries = MoodEntry::where('user_id', $user->id)
            ->select(DB::raw("strftime('%Y-%m-%d', created_at) as entry_date"))
            ->distinct()
            ->orderBy('entry_date', 'desc')
            ->get()
            ->pluck('entry_date');

        if ($entries->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($entries as $entryDate) {
            $date = Carbon::parse($entryDate);

            if ($date->isSameDay($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif ($date->isSameDay($currentDate->copy()->addDay())) {
                // Already counted, skip
                continue;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get longest tracking streak.
     */
    private function getLongestStreak(User $user): int
    {
        $entries = MoodEntry::where('user_id', $user->id)
            ->select(DB::raw("strftime('%Y-%m-%d', created_at) as entry_date"))
            ->distinct()
            ->orderBy('entry_date', 'asc')
            ->get()
            ->pluck('entry_date');

        if ($entries->isEmpty()) {
            return 0;
        }

        $longestStreak = 1;
        $currentStreak = 1;
        $previousDate = Carbon::parse($entries->first());

        foreach ($entries->skip(1) as $entryDate) {
            $date = Carbon::parse($entryDate);

            if ($date->diffInDays($previousDate) === 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }

            $previousDate = $date;
        }

        return $longestStreak;
    }

    /**
     * Get mood category based on score.
     */
    private function getMoodCategory(float $score): string
    {
        return match (true) {
            $score >= 8 => 'Excellent',
            $score >= 6 => 'Good',
            $score >= 4 => 'Medium',
            default => 'Low',
        };
    }

    /**
     * Format period string for display.
     */
    private function formatPeriod(string $period, string $periodType): string
    {
        return match ($periodType) {
            'daily' => Carbon::parse($period)->format('M d, Y'),
            'weekly' => 'Week ' . substr($period, -2) . ', ' . substr($period, 0, 4),
            'monthly' => Carbon::parse($period . '-01')->format('M Y'),
            default => $period,
        };
    }

    /**
     * Get time-of-day analysis (morning, afternoon, evening).
     */
    public function getMoodByTimeOfDay(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = MoodEntry::where('user_id', $user->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $entries = $query->get();

        $timeRanges = [
            'morning' => ['start' => 6, 'end' => 12],
            'afternoon' => ['start' => 12, 'end' => 18],
            'evening' => ['start' => 18, 'end' => 24],
            'night' => ['start' => 0, 'end' => 6],
        ];

        $results = [];

        foreach ($timeRanges as $name => $range) {
            $filtered = $entries->filter(function ($entry) use ($range) {
                $hour = (int) $entry->created_at->format('H');
                return $hour >= $range['start'] && $hour < $range['end'];
            });

            $results[] = [
                'time_of_day' => ucfirst($name),
                'count' => $filtered->count(),
                'avg_mood' => $filtered->count() > 0 ? round($filtered->avg('mood_score'), 2) : 0,
            ];
        }

        return $results;
    }
}
