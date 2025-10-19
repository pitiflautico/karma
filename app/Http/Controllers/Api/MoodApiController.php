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
            // Entry type: manual or selfie
            'entry_type' => 'nullable|string|in:manual,selfie',
            // Tags
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            // Face analysis fields (all optional)
            'face_expression' => 'nullable|string|max:50',
            'face_expression_confidence' => 'nullable|numeric|min:0|max:1',
            'face_energy_level' => 'nullable|string|max:20',
            'face_eyes_openness' => 'nullable|numeric|min:0|max:1',
            'face_social_context' => 'nullable|string|max:20',
            'face_total_faces' => 'nullable|integer|min:0',
            'bpm' => 'nullable|integer|min:30|max:220',
            'environment_brightness' => 'nullable|string|max:20',
            'face_analysis_raw' => 'nullable|array',
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

            // Add entry type if provided
            if ($request->has('entry_type')) {
                $moodEntry->entry_type = $request->entry_type;
            }

            // Add face analysis data if provided
            if ($request->has('face_expression')) {
                $moodEntry->face_expression = $request->face_expression;
            }
            if ($request->has('face_expression_confidence')) {
                $moodEntry->face_expression_confidence = $request->face_expression_confidence;
            }
            if ($request->has('face_energy_level')) {
                $moodEntry->face_energy_level = $request->face_energy_level;
            }
            if ($request->has('face_eyes_openness')) {
                $moodEntry->face_eyes_openness = $request->face_eyes_openness;
            }
            if ($request->has('face_social_context')) {
                $moodEntry->face_social_context = $request->face_social_context;
            }
            if ($request->has('face_total_faces')) {
                $moodEntry->face_total_faces = $request->face_total_faces;
            }
            if ($request->has('bpm')) {
                $moodEntry->bpm = $request->bpm;
            }
            if ($request->has('environment_brightness')) {
                $moodEntry->environment_brightness = $request->environment_brightness;
            }
            if ($request->has('face_analysis_raw')) {
                $moodEntry->face_analysis_raw = $request->face_analysis_raw;
            }

            $moodEntry->save();

            // Attach tags if provided
            if ($request->has('tag_ids') && is_array($request->tag_ids)) {
                $moodEntry->tags()->sync($request->tag_ids);
            }

            // Update calendar event with mood_entry_id (bidirectional relationship)
            if ($request->calendar_event_id) {
                $calendarEvent = \App\Models\CalendarEvent::find($request->calendar_event_id);
                if ($calendarEvent && $calendarEvent->user_id === auth()->id()) {
                    $calendarEvent->mood_entry_id = $moodEntry->id;
                    $calendarEvent->save();
                }
            }

            // Load relationships
            $moodEntry->load(['calendarEvent', 'tags']);

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
                ->with(['calendarEvent', 'tags'])
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
                ->with(['calendarEvent', 'tags'])
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
