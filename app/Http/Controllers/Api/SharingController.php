<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use App\Models\SharedAccess;
use App\Models\SharingInvite;
use App\Models\User;
use App\Notifications\SharingInvitationReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SharingController extends Controller
{
    /**
     * Send sharing invitation
     */
    public function sendInvite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'can_view_moods' => 'boolean',
            'can_view_notes' => 'boolean',
            'can_view_selfies' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invitation data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $email = $request->email;

        // Check if user is trying to share with themselves
        if ($user->email === $email) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot share with yourself.',
            ], 400);
        }

        // Check if already sharing with this user
        $recipient = User::where('email', $email)->first();
        if ($recipient) {
            $existingAccess = SharedAccess::where('owner_id', $user->id)
                ->where('shared_with_id', $recipient->id)
                ->first();

            if ($existingAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already sharing with this user.',
                ], 409);
            }
        }

        // Check if there's a pending invitation
        $existingInvite = SharingInvite::where('owner_id', $user->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();

        if ($existingInvite) {
            return response()->json([
                'success' => false,
                'message' => 'There is already a pending invitation for this email.',
            ], 409);
        }

        // Create invitation
        $invite = SharingInvite::create([
            'owner_id' => $user->id,
            'email' => $email,
            'token' => Str::random(32),
            'can_view_moods' => $request->can_view_moods ?? true,
            'can_view_notes' => $request->can_view_notes ?? false,
            'can_view_selfies' => $request->can_view_selfies ?? false,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // If recipient is already a user, send push notification
        if ($recipient && $recipient->push_token) {
            try {
                $recipient->notify(new SharingInvitationReceived($user));
            } catch (\Exception $e) {
                \Log::warning("Failed to send sharing invitation push notification: {$e->getMessage()}");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully!',
            'data' => [
                'invite' => [
                    'id' => $invite->id,
                    'email' => $invite->email,
                    'can_view_moods' => $invite->can_view_moods,
                    'can_view_notes' => $invite->can_view_notes,
                    'can_view_selfies' => $invite->can_view_selfies,
                    'expires_at' => $invite->expires_at->toIso8601String(),
                    'created_at' => $invite->created_at->toIso8601String(),
                ],
            ],
        ], 200);
    }

    /**
     * Get my sent invitations
     */
    public function myInvites()
    {
        $user = Auth::user();

        $invites = SharingInvite::where('owner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invite) {
                return [
                    'id' => $invite->id,
                    'email' => $invite->email,
                    'status' => $invite->status,
                    'can_view_moods' => $invite->can_view_moods,
                    'can_view_notes' => $invite->can_view_notes,
                    'can_view_selfies' => $invite->can_view_selfies,
                    'expires_at' => $invite->expires_at?->toIso8601String(),
                    'created_at' => $invite->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'invites' => $invites,
                'total' => $invites->count(),
            ],
        ], 200);
    }

    /**
     * Get invitations received by me
     */
    public function invitesReceived()
    {
        $user = Auth::user();

        $invites = SharingInvite::where('email', $user->email)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->with('owner:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invite) {
                return [
                    'id' => $invite->id,
                    'token' => $invite->token,
                    'owner' => [
                        'name' => $invite->owner->name,
                        'email' => $invite->owner->email,
                    ],
                    'can_view_moods' => $invite->can_view_moods,
                    'can_view_notes' => $invite->can_view_notes,
                    'can_view_selfies' => $invite->can_view_selfies,
                    'expires_at' => $invite->expires_at?->toIso8601String(),
                    'created_at' => $invite->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'invites' => $invites,
                'total' => $invites->count(),
            ],
        ], 200);
    }

    /**
     * Accept sharing invitation
     */
    public function acceptInvite($token)
    {
        $user = Auth::user();

        $invite = SharingInvite::where('token', $token)
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->first();

        if (!$invite) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation.',
            ], 404);
        }

        // Check if expired
        if ($invite->expires_at && $invite->expires_at->isPast()) {
            $invite->update(['status' => 'expired']);

            return response()->json([
                'success' => false,
                'message' => 'This invitation has expired.',
            ], 410);
        }

        // Create shared access
        DB::transaction(function () use ($invite, $user) {
            SharedAccess::create([
                'owner_id' => $invite->owner_id,
                'shared_with_id' => $user->id,
                'can_view_moods' => $invite->can_view_moods,
                'can_view_notes' => $invite->can_view_notes,
                'can_view_selfies' => $invite->can_view_selfies,
            ]);

            $invite->update(['status' => 'accepted']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted! You can now view shared data.',
        ], 200);
    }

    /**
     * Reject sharing invitation
     */
    public function rejectInvite($token)
    {
        $user = Auth::user();

        $invite = SharingInvite::where('token', $token)
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->first();

        if (!$invite) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation.',
            ], 404);
        }

        $invite->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Invitation rejected.',
        ], 200);
    }

    /**
     * Get list of people I'm sharing with
     */
    public function sharingWith()
    {
        $user = Auth::user();

        $shares = SharedAccess::where('owner_id', $user->id)
            ->with('sharedWith:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($share) {
                return [
                    'id' => $share->id,
                    'user' => [
                        'id' => $share->sharedWith->id,
                        'name' => $share->sharedWith->name,
                        'email' => $share->sharedWith->email,
                    ],
                    'permissions' => [
                        'can_view_moods' => $share->can_view_moods,
                        'can_view_notes' => $share->can_view_notes,
                        'can_view_selfies' => $share->can_view_selfies,
                    ],
                    'created_at' => $share->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'shares' => $shares,
                'total' => $shares->count(),
            ],
        ], 200);
    }

    /**
     * Get list of people sharing with me
     */
    public function sharedWithMe()
    {
        $user = Auth::user();

        $shares = SharedAccess::where('shared_with_id', $user->id)
            ->with('owner:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($share) {
                // Get latest mood entry from owner
                $latestMood = null;
                if ($share->can_view_moods) {
                    $mood = MoodEntry::where('user_id', $share->owner_id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($mood) {
                        $latestMood = [
                            'mood_score' => $mood->mood_score,
                            'created_at' => $mood->created_at->toIso8601String(),
                        ];

                        if ($share->can_view_notes && $mood->notes) {
                            $latestMood['notes'] = $mood->notes;
                        }
                    }
                }

                return [
                    'id' => $share->id,
                    'owner' => [
                        'id' => $share->owner->id,
                        'name' => $share->owner->name,
                        'email' => $share->owner->email,
                    ],
                    'permissions' => [
                        'can_view_moods' => $share->can_view_moods,
                        'can_view_notes' => $share->can_view_notes,
                        'can_view_selfies' => $share->can_view_selfies,
                    ],
                    'latest_mood' => $latestMood,
                    'created_at' => $share->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'shares' => $shares,
                'total' => $shares->count(),
            ],
        ], 200);
    }

    /**
     * Revoke sharing access
     */
    public function revokeAccess($shareId)
    {
        $user = Auth::user();

        $share = SharedAccess::where('id', $shareId)
            ->where('owner_id', $user->id)
            ->first();

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Shared access not found.',
            ], 404);
        }

        $share->delete();

        return response()->json([
            'success' => true,
            'message' => 'Access revoked successfully.',
        ], 200);
    }

    /**
     * Update sharing permissions
     */
    public function updatePermissions(Request $request, $shareId)
    {
        $validator = Validator::make($request->all(), [
            'can_view_moods' => 'boolean',
            'can_view_notes' => 'boolean',
            'can_view_selfies' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid permission data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $share = SharedAccess::where('id', $shareId)
            ->where('owner_id', $user->id)
            ->first();

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Shared access not found.',
            ], 404);
        }

        $share->update([
            'can_view_moods' => $request->can_view_moods ?? $share->can_view_moods,
            'can_view_notes' => $request->can_view_notes ?? $share->can_view_notes,
            'can_view_selfies' => $request->can_view_selfies ?? $share->can_view_selfies,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.',
            'data' => [
                'permissions' => [
                    'can_view_moods' => $share->can_view_moods,
                    'can_view_notes' => $share->can_view_notes,
                    'can_view_selfies' => $share->can_view_selfies,
                ],
            ],
        ], 200);
    }

    /**
     * Get mood entries from a user who is sharing with me
     */
    public function getMoodsFromUser($ownerId, Request $request)
    {
        $user = Auth::user();

        // Check if owner is sharing with me
        $share = SharedAccess::where('owner_id', $ownerId)
            ->where('shared_with_id', $user->id)
            ->first();

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this user\'s data.',
            ], 403);
        }

        if (!$share->can_view_moods) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view moods.',
            ], 403);
        }

        // Get filters
        $limit = $request->query('limit', 50);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = MoodEntry::where('user_id', $ownerId)
            ->orderBy('created_at', 'desc');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $moods = $query->limit($limit)->get()->map(function ($mood) use ($share) {
            $entry = [
                'id' => $mood->id,
                'mood_score' => $mood->mood_score,
                'created_at' => $mood->created_at->toIso8601String(),
            ];

            // Only include notes if permission granted
            if ($share->can_view_notes && $mood->notes) {
                $entry['notes'] = $mood->notes;
            }

            // Only include selfie if permission granted
            if ($share->can_view_selfies && $mood->selfie_path) {
                $entry['selfie_url'] = asset('storage/' . $mood->selfie_path);
            }

            return $entry;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'moods' => $moods,
                'total' => $moods->count(),
                'permissions' => [
                    'can_view_moods' => $share->can_view_moods,
                    'can_view_notes' => $share->can_view_notes,
                    'can_view_selfies' => $share->can_view_selfies,
                ],
            ],
        ], 200);
    }
}
