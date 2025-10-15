<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Get user profile data
     */
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path,
                'age' => $user->age,
                'settings' => $user->settings ?? [],
                'calendar_sync_enabled' => $user->calendar_sync_enabled ?? false,
                'google_calendar_connected' => !empty($user->google_token),
            ],
        ]);
    }

    /**
     * Update user profile data
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'age' => 'sometimes|integer|min:1|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'age']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'age' => $user->age,
            ],
        ]);
    }

    /**
     * Get calendar sync status
     */
    public function calendarStatus()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'is_connected' => !empty($user->google_calendar_token),
                'calendar_sync_enabled' => $user->calendar_sync_enabled ?? false,
                'last_sync_at' => $user->last_calendar_sync_at,
                'quiet_hours_start' => $user->quiet_hours_start ?? '22:00',
                'quiet_hours_end' => $user->quiet_hours_end ?? '08:00',
            ],
        ]);
    }

    /**
     * Toggle calendar sync
     */
    public function toggleCalendarSync(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'calendar_sync_enabled' => !$user->calendar_sync_enabled
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->calendar_sync_enabled ? 'Calendar sync enabled' : 'Calendar sync disabled',
            'data' => [
                'calendar_sync_enabled' => $user->calendar_sync_enabled,
            ],
        ]);
    }

    /**
     * Update quiet hours
     */
    public function updateQuietHours(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiet_hours_start' => 'required|date_format:H:i',
            'quiet_hours_end' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $user->update([
            'quiet_hours_start' => $request->quiet_hours_start,
            'quiet_hours_end' => $request->quiet_hours_end,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiet hours updated successfully',
            'data' => [
                'quiet_hours_start' => $user->quiet_hours_start,
                'quiet_hours_end' => $user->quiet_hours_end,
            ],
        ]);
    }

    /**
     * Sync calendar now
     */
    public function syncCalendar()
    {
        $user = Auth::user();

        if (!$user->calendar_sync_enabled || !$user->google_calendar_token) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar sync is not enabled or Google Calendar is not connected',
            ], 400);
        }

        try {
            $service = new \App\Services\GoogleCalendarService();
            $events = $service->syncEvents($user, 50);

            return response()->json([
                'success' => true,
                'message' => 'Calendar synced successfully',
                'data' => [
                    'events_synced' => count($events),
                    'last_sync_at' => now(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Manual calendar sync failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync calendar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect calendar
     */
    public function disconnectCalendar()
    {
        $user = Auth::user();

        $user->update([
            'google_calendar_token' => null,
            'google_calendar_refresh_token' => null,
            'google_calendar_sync_token' => null,
            'calendar_sync_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Google Calendar disconnected successfully',
        ]);
    }
}
