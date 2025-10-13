<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SelfieController extends Controller
{
    /**
     * Upload a selfie photo
     *
     * Expects:
     * - photo: base64 encoded image
     * - mood_entry_id (optional): ID of mood entry to attach photo to
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'mood_entry_id' => 'nullable|exists:mood_entries,id',
        ]);

        try {
            // Check if user already has a selfie today
            $todayStart = now()->startOfDay();
            $todayEnd = now()->endOfDay();

            $existingToday = MoodEntry::where('user_id', auth()->id())
                ->whereNotNull('selfie_photo_path')
                ->whereBetween('selfie_taken_at', [$todayStart, $todayEnd])
                ->exists();

            if ($existingToday) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only upload one selfie per day. Try again tomorrow!'
                ], 422);
            }
            // Decode base64 image
            $imageData = $request->input('photo');

            // Remove data:image/xxx;base64, prefix if exists
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $extension = $matches[1];
            } else {
                $extension = 'jpg';
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            // Generate unique filename
            $filename = 'selfie_' . auth()->id() . '_' . time() . '_' . Str::random(8) . '.' . $extension;
            $path = 'selfies/' . auth()->id() . '/' . $filename;

            // Store in storage/app/public/selfies/
            Storage::disk('public')->put($path, $imageData);

            // If mood_entry_id provided, attach to mood entry
            if ($request->input('mood_entry_id')) {
                $moodEntry = MoodEntry::where('id', $request->input('mood_entry_id'))
                    ->where('user_id', auth()->id())
                    ->first();

                if ($moodEntry) {
                    $moodEntry->update([
                        'selfie_photo_path' => $path,
                        'selfie_taken_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ]);

        } catch (\Exception $e) {
            \Log::error('Selfie upload error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's selfies
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 50);

        $moodEntries = MoodEntry::where('user_id', auth()->id())
            ->whereNotNull('selfie_photo_path')
            ->orderBy('selfie_taken_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'photo_url' => Storage::disk('public')->url($entry->selfie_photo_path),
                    'mood_score' => $entry->mood_score,
                    'taken_at' => $entry->selfie_taken_at,
                    'created_at' => $entry->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'selfies' => $moodEntries,
        ]);
    }

    /**
     * Delete a selfie
     */
    public function destroy($id)
    {
        $moodEntry = MoodEntry::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$moodEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Mood entry not found'
            ], 404);
        }

        // Delete photo from storage
        if ($moodEntry->selfie_photo_path) {
            Storage::disk('public')->delete($moodEntry->selfie_photo_path);
        }

        // Clear photo fields
        $moodEntry->update([
            'selfie_photo_path' => null,
            'selfie_heatmap_path' => null,
            'selfie_taken_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selfie deleted successfully'
        ]);
    }
}
