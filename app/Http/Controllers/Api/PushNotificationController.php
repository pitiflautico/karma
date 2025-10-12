<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    /**
     * Register or update push notification token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
            'pushToken' => 'nullable|string',
            'hasPermission' => 'nullable|boolean',
            'platform' => 'required|in:ios,android',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user from Bearer token
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Verify that userId matches authenticated user
        if ($user->id !== $request->userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID mismatch'
            ], 403);
        }

        // Check if user denied permission
        if ($request->has('hasPermission') && $request->hasPermission === false) {
            $user->update([
                'push_token' => null,
                'push_platform' => $request->platform,
                'push_enabled' => false,
                'push_registered_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push notification permission denied',
                'data' => [
                    'pushEnabled' => false,
                ]
            ]);
        }

        // User granted permission and provided token
        if ($request->has('pushToken')) {
            $user->update([
                'push_token' => $request->pushToken,
                'push_platform' => $request->platform,
                'push_enabled' => true,
                'push_registered_at' => now(),
            ]);

            \Log::info('Push token registered', [
                'user_id' => $user->id,
                'platform' => $request->platform,
                'token_preview' => substr($request->pushToken, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push token registered successfully',
                'data' => [
                    'pushEnabled' => true,
                    'platform' => $user->push_platform,
                    'registeredAt' => $user->push_registered_at,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Either pushToken or hasPermission must be provided'
        ], 422);
    }
}
