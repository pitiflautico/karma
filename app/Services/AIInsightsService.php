<?php

namespace App\Services;

use App\Models\AIInsight;
use App\Models\AIUsageLog;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIInsightsService
{
    private string $apiKey;
    private string $model = 'meta-llama/Llama-3.3-70B-Instruct-Turbo';
    private string $apiUrl = 'https://api.together.xyz/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = 'tgp_v1_YK8EaACZgnowRqTsSwcNIrskpZnTzBLHTIxwf5bo2Cc';
    }

    /**
     * Get or generate insights for a user
     */
    public function getInsights(User $user, string $period = '30_days'): ?AIInsight
    {
        // Check if we have a recent cached insight
        $latestInsight = AIInsight::getLatest($user->id, $period);

        if ($latestInsight && $latestInsight->isFresh()) {
            return $latestInsight;
        }

        // Generate new insights
        return $this->generateInsights($user, $period);
    }

    /**
     * Generate new AI insights
     */
    public function generateInsights(User $user, string $period = '30_days'): ?AIInsight
    {
        // Get mood data for the period
        $days = $this->getDaysFromPeriod($period);
        $moodData = $this->prepareMoodData($user, $days);

        if (empty($moodData['entries'])) {
            return null;
        }

        // Add user_id to mood data for logging
        $moodData['user_id'] = $user->id;

        // Call Together.ai API
        $aiResponse = $this->callTogetherAI($moodData);

        if (!$aiResponse) {
            return null;
        }

        // Save the insight
        return AIInsight::create([
            'user_id' => $user->id,
            'period' => $period,
            'insights_data' => $aiResponse,
            'summary_stats' => $moodData['summary'],
            'generated_at' => now(),
        ]);
    }

    /**
     * Prepare mood data for AI analysis
     */
    private function prepareMoodData(User $user, int $days): array
    {
        $startDate = now()->subDays($days);

        $moodEntries = MoodEntry::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->with('calendarEvent')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($moodEntries->isEmpty()) {
            return ['entries' => [], 'summary' => []];
        }

        $entries = [];
        $totalScore = 0;
        $dayOfWeekScores = [];
        $activitiesImpact = [];

        foreach ($moodEntries as $entry) {
            $dayOfWeek = $entry->created_at->format('l');
            $date = $entry->created_at->format('Y-m-d');

            // Get activity from calendar event if available
            $activities = [];
            if ($entry->calendarEvent) {
                $eventType = $entry->calendarEvent->event_type ?? $entry->calendarEvent->title;
                if ($eventType) {
                    $activities[] = $eventType;
                }
            }

            $entries[] = [
                'date' => $date,
                'day' => $dayOfWeek,
                'score' => $entry->mood_score,
                'note' => $entry->note,
                'activities' => $activities,
            ];

            $totalScore += $entry->mood_score;

            // Track day of week patterns
            if (!isset($dayOfWeekScores[$dayOfWeek])) {
                $dayOfWeekScores[$dayOfWeek] = ['total' => 0, 'count' => 0];
            }
            $dayOfWeekScores[$dayOfWeek]['total'] += $entry->mood_score;
            $dayOfWeekScores[$dayOfWeek]['count']++;

            // Track activities impact
            if (!empty($activities)) {
                foreach ($activities as $activity) {
                    if (!isset($activitiesImpact[$activity])) {
                        $activitiesImpact[$activity] = ['total' => 0, 'count' => 0];
                    }
                    $activitiesImpact[$activity]['total'] += $entry->mood_score;
                    $activitiesImpact[$activity]['count']++;
                }
            }
        }

        // Calculate averages
        $averageScore = $totalScore / $moodEntries->count();

        $dayOfWeekAverages = [];
        foreach ($dayOfWeekScores as $day => $data) {
            $dayOfWeekAverages[$day] = round($data['total'] / $data['count'], 2);
        }

        $activityAverages = [];
        foreach ($activitiesImpact as $activity => $data) {
            $activityAverages[$activity] = round($data['total'] / $data['count'], 2);
        }

        arsort($activityAverages);

        return [
            'entries' => $entries,
            'summary' => [
                'period_days' => $days,
                'total_entries' => $moodEntries->count(),
                'average_score' => round($averageScore, 2),
                'highest_score' => $moodEntries->max('mood_score'),
                'lowest_score' => $moodEntries->min('mood_score'),
                'day_of_week_averages' => $dayOfWeekAverages,
                'activity_averages' => $activityAverages,
            ],
        ];
    }

    /**
     * Call Together.ai API for insights
     */
    private function callTogetherAI(array $moodData): ?array
    {
        $prompt = $this->buildPrompt($moodData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a compassionate emotional wellness coach. Analyze mood data and provide supportive, actionable insights. Be empathetic, encouraging, and specific in your recommendations. Use a warm, friendly tone.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 1500,
                'temperature' => 0.7,
                'top_p' => 0.9,
            ]);

            if ($response->failed()) {
                Log::error('Together.ai API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                return null;
            }

            // Log usage
            $usage = $data['usage'] ?? [];
            $this->logUsage(
                $moodData['user_id'] ?? null,
                $usage['prompt_tokens'] ?? 0,
                $usage['completion_tokens'] ?? 0,
                $usage['total_tokens'] ?? 0
            );

            return [
                'raw_response' => $content,
                'generated_at' => now()->toISOString(),
                'model' => $this->model,
                'usage' => $usage,
            ];

        } catch (\Exception $e) {
            Log::error('Together.ai API exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Build the prompt for AI analysis
     */
    private function buildPrompt(array $moodData): string
    {
        $summary = $moodData['summary'];
        $entries = $moodData['entries'];

        $prompt = "I need you to analyze mood tracking data and provide personalized emotional wellness insights.\n\n";

        $prompt .= "**Summary Statistics:**\n";
        $prompt .= "- Period: Last {$summary['period_days']} days\n";
        $prompt .= "- Total entries: {$summary['total_entries']}\n";
        $prompt .= "- Average mood score: {$summary['average_score']}/10\n";
        $prompt .= "- Highest score: {$summary['highest_score']}/10\n";
        $prompt .= "- Lowest score: {$summary['lowest_score']}/10\n\n";

        if (!empty($summary['day_of_week_averages'])) {
            $prompt .= "**Average Mood by Day of Week:**\n";
            foreach ($summary['day_of_week_averages'] as $day => $avg) {
                $prompt .= "- {$day}: {$avg}/10\n";
            }
            $prompt .= "\n";
        }

        if (!empty($summary['activity_averages'])) {
            $prompt .= "**Activities and Associated Mood (sorted by highest mood):**\n";
            foreach ($summary['activity_averages'] as $activity => $avg) {
                $prompt .= "- {$activity}: {$avg}/10\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "**Recent Mood Entries (last 10):**\n";
        $recentEntries = array_slice($entries, -10);
        foreach ($recentEntries as $entry) {
            $prompt .= "- {$entry['date']} ({$entry['day']}): {$entry['score']}/10";
            if (!empty($entry['note'])) {
                $prompt .= " - Note: {$entry['note']}";
            }
            if (!empty($entry['activities'])) {
                $prompt .= " - Activities: " . implode(', ', $entry['activities']);
            }
            $prompt .= "\n";
        }

        $prompt .= "\n**Please provide insights in the following format:**\n\n";
        $prompt .= "1. **Overall Emotional Pattern**: A brief overview of the person's emotional state during this period.\n\n";
        $prompt .= "2. **Best Days**: Identify which days of the week they tend to feel best and why this might be.\n\n";
        $prompt .= "3. **Challenging Days**: Identify which days are most difficult and offer supportive understanding.\n\n";
        $prompt .= "4. **Activity Recommendations**: Based on the data, which activities seem to boost their mood most? Recommend they do more of these.\n\n";
        $prompt .= "5. **Patterns & Trends**: Any notable patterns in their mood fluctuations.\n\n";
        $prompt .= "6. **Supportive Advice**: 2-3 specific, actionable recommendations to improve their emotional wellbeing.\n\n";
        $prompt .= "7. **Encouragement**: End with a warm, encouraging message acknowledging their progress and effort in tracking their moods.\n\n";
        $prompt .= "Keep the tone warm, supportive, and conversational. Avoid clinical language. Be specific to their data.";

        return $prompt;
    }

    /**
     * Get number of days from period string
     */
    private function getDaysFromPeriod(string $period): int
    {
        return match($period) {
            '7_days' => 7,
            '30_days' => 30,
            '90_days' => 90,
            default => 30,
        };
    }

    /**
     * Log AI usage
     */
    private function logUsage(?string $userId, int $inputTokens, int $outputTokens, int $totalTokens): void
    {
        if (!$userId) {
            return;
        }

        // Together.ai pricing for Llama-3.3-70B-Instruct-Turbo
        // $0.88 per million input tokens, $0.88 per million output tokens
        $inputCost = ($inputTokens / 1000000) * 0.88;
        $outputCost = ($outputTokens / 1000000) * 0.88;
        $totalCost = $inputCost + $outputCost;

        AIUsageLog::create([
            'user_id' => $userId,
            'service' => 'insights',
            'model' => $this->model,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $totalTokens,
            'estimated_cost' => $totalCost,
            'request_type' => 'mood_insights',
        ]);
    }
}
