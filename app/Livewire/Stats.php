<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Stats extends Component
{
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        // Default to current month and year
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }

    /**
     * Get mood entries count by mood score (1-10)
     */
    private function getMostLoggedMoods()
    {
        $moods = MoodEntry::where('user_id', auth()->id())
            ->whereYear('created_at', $this->selectedYear)
            ->whereMonth('created_at', $this->selectedMonth)
            ->select('mood_score', DB::raw('count(*) as count'))
            ->groupBy('mood_score')
            ->orderBy('mood_score', 'desc')
            ->get();

        // Map mood scores to mood names and icons
        $moodData = [];
        foreach ($moods as $mood) {
            $moodData[] = [
                'score' => $mood->mood_score,
                'count' => $mood->count,
                'name' => $this->getMoodName($mood->mood_score),
                'emoji' => $this->getMoodEmoji($mood->mood_score),
                'icon' => $this->getMoodIcon($mood->mood_score),
                'color' => $this->getMoodColor($mood->mood_score),
            ];
        }

        return $moodData;
    }

    /**
     * Get mood over time (daily average for the month)
     */
    private function getMoodOverTime()
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $moods = MoodEntry::where('user_id', auth()->id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(mood_score) as avg_score')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Create array with all days of the month
        $daysInMonth = $endDate->day;
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, $day);
            $entry = $moods->firstWhere('date', $date->toDateString());

            $data[] = [
                'day' => $day,
                'dayName' => $date->format('D'), // Mon, Tue, etc.
                'score' => $entry ? round($entry->avg_score, 1) : null,
            ];
        }

        return $data;
    }

    /**
     * Get the happiest day of the month
     */
    private function getHappiestDay()
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $happiestDay = MoodEntry::where('user_id', auth()->id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(mood_score) as avg_score')
            )
            ->groupBy('date')
            ->orderBy('avg_score', 'desc')
            ->first();

        if ($happiestDay) {
            $date = Carbon::parse($happiestDay->date);
            return [
                'dayName' => $date->format('l'), // Full day name
                'date' => $date,
                'score' => round($happiestDay->avg_score, 1),
            ];
        }

        return null;
    }

    /**
     * Get total mood entries for the month
     */
    private function getTotalEntries()
    {
        return MoodEntry::where('user_id', auth()->id())
            ->whereYear('created_at', $this->selectedYear)
            ->whereMonth('created_at', $this->selectedMonth)
            ->count();
    }

    /**
     * Get most logged tags (triggers)
     */
    private function getMostLoggedTags()
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $tags = DB::table('mood_entry_tag')
            ->join('tags', 'mood_entry_tag.tag_id', '=', 'tags.id')
            ->join('mood_entries', 'mood_entry_tag.mood_entry_id', '=', 'mood_entries.id')
            ->where('mood_entries.user_id', auth()->id())
            ->whereBetween('mood_entries.created_at', [$startDate, $endDate])
            ->select('tags.id', 'tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.id', 'tags.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return $tags;
    }

    /**
     * Get mood name from score
     */
    private function getMoodName($score)
    {
        if ($score >= 9) return 'Very Happy';
        if ($score >= 7) return 'Happy';
        if ($score >= 5) return 'Neutral';
        if ($score >= 3) return 'Sad';
        return 'Very Sad';
    }

    /**
     * Get mood emoji from score
     */
    private function getMoodEmoji($score)
    {
        if ($score >= 9) return '😄';
        if ($score >= 7) return '🙂';
        if ($score >= 5) return '😐';
        if ($score >= 3) return '😕';
        return '😢';
    }

    /**
     * Get mood icon SVG from score
     */
    private function getMoodIcon($score)
    {
        if ($score >= 9) return 'great';
        if ($score >= 7) return 'happy';
        if ($score >= 5) return 'neutral';
        if ($score >= 3) return 'sad';
        return 'depressed';
    }

    /**
     * Get mood color from score
     */
    private function getMoodColor($score)
    {
        if ($score >= 9) return '#10B981'; // Green
        if ($score >= 7) return '#FBBF24'; // Yellow
        if ($score >= 5) return '#F59E0B'; // Amber
        if ($score >= 3) return '#F97316'; // Orange
        return '#EF4444'; // Red
    }

    public function render()
    {
        $totalEntries = $this->getTotalEntries();
        $mostLoggedMoods = $this->getMostLoggedMoods();
        $moodOverTime = $this->getMoodOverTime();
        $happiestDay = $this->getHappiestDay();
        $mostLoggedTags = $this->getMostLoggedTags();

        // Get the most logged mood
        $topMood = collect($mostLoggedMoods)->sortByDesc('count')->first();
        // Get the most logged tag
        $topTag = $mostLoggedTags->first();

        return view('livewire.stats', [
            'totalEntries' => $totalEntries,
            'mostLoggedMoods' => $mostLoggedMoods,
            'moodOverTime' => $moodOverTime,
            'happiestDay' => $happiestDay,
            'mostLoggedTags' => $mostLoggedTags,
            'topMood' => $topMood,
            'topTag' => $topTag,
            'monthName' => Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->format('F Y'),
        ])->layout('layouts.app-mobile');
    }
}
