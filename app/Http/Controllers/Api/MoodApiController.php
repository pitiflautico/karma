<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MoodApiController extends Controller
{
    /**
     * Create a new mood entry
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mood_score' => 'required|integer|min:1|max:10',
            'note' => 'nullable|string|max:500',
            'calendar_event_id' => 'nullable|exists:calendar_events,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $moodEntry = new MoodEntry();
            $moodEntry->user_id = auth()->id();
            $moodEntry->mood_score = $request->mood_score;
            $moodEntry->note = $request->note;
            $moodEntry->calendar_event_id = $request->calendar_event_id;
            $moodEntry->save();

            // Load relationships
            $moodEntry->load('calendarEvent');

            return response()->json([
                'message' => 'Mood entry created successfully',
                'mood' => $moodEntry,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating mood entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create mood entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's mood entries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $moods = MoodEntry::where('user_id', auth()->id())
                ->with('calendarEvent')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'moods' => $moods,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching mood entries: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch mood entries',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single mood entry
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $mood = MoodEntry::where('id', $id)
                ->where('user_id', auth()->id())
                ->with('calendarEvent')
                ->firstOrFail();

            return response()->json([
                'mood' => $mood,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Mood entry not found',
            ], 404);
        }
    }

    /**
     * Update a mood entry
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'mood_score' => 'sometimes|integer|min:1|max:10',
            'note' => 'nullable|string|max:500',
            'calendar_event_id' => 'nullable|exists:calendar_events,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $mood = MoodEntry::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if ($request->has('mood_score')) {
                $mood->mood_score = $request->mood_score;
            }

            if ($request->has('note')) {
                $mood->note = $request->note;
            }

            if ($request->has('calendar_event_id')) {
                $mood->calendar_event_id = $request->calendar_event_id;
            }

            $mood->save();
            $mood->load('calendarEvent');

            return response()->json([
                'message' => 'Mood entry updated successfully',
                'mood' => $mood,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating mood entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update mood entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a mood entry
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $mood = MoodEntry::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $mood->delete();

            return response()->json([
                'message' => 'Mood entry deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error deleting mood entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete mood entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
