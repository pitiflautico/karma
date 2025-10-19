<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Join a group using invite code
     */
    public function joinGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invite_code' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invite code format',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $inviteCode = strtoupper($request->invite_code);

        // Find group by invite code
        $group = Group::where('invite_code', $inviteCode)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invite code. Group not found.',
            ], 404);
        }

        // Check if group is active
        if (!$group->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This group is no longer accepting new members.',
            ], 403);
        }

        // Check if user is already a member
        if ($user->groups()->where('group_id', $group->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this group.',
            ], 409);
        }

        // Add user to group
        $user->groups()->attach($group->id);

        // Load fresh group with member count
        $group->load('users');

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the group!',
            'data' => [
                'group' => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'member_count' => $group->users->count(),
                    'joined_at' => now()->toIso8601String(),
                ],
            ],
        ], 200);
    }

    /**
     * Leave a group
     */
    public function leaveGroup($groupId)
    {
        $user = Auth::user();

        $group = Group::find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        // Check if user is a member
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 404);
        }

        // Remove user from group
        $user->groups()->detach($group->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the group.',
        ], 200);
    }

    /**
     * Get list of groups user belongs to
     */
    public function myGroups()
    {
        $user = Auth::user();

        $groups = $user->groups()
            ->with('users')
            ->get()
            ->map(function ($group) use ($user) {
                // Get group stats
                $memberCount = $group->users->count();
                $todayAvg = $group->getAverageMood(now()->startOfDay(), now()->endOfDay());

                // Calculate activity rate (members who logged mood today)
                $membersWhoLoggedToday = $group->moodEntries()
                    ->whereDate('created_at', today())
                    ->distinct('user_id')
                    ->count('user_id');

                $activityRate = $memberCount > 0
                    ? round(($membersWhoLoggedToday / $memberCount) * 100)
                    : 0;

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'invite_code' => $group->invite_code,
                    'member_count' => $memberCount,
                    'mood_today' => round($todayAvg, 1),
                    'activity_rate' => $activityRate,
                    'joined_at' => $user->groups()
                        ->where('group_id', $group->id)
                        ->first()
                        ->pivot
                        ->created_at
                        ->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'groups' => $groups,
                'total' => $groups->count(),
            ],
        ], 200);
    }

    /**
     * Get group dashboard with details
     */
    public function show($groupId)
    {
        $user = Auth::user();

        $group = Group::with('users')->find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        // Check if user is a member
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        $memberCount = $group->users->count();

        return response()->json([
            'success' => true,
            'data' => [
                'group' => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'member_count' => $memberCount,
                    'created_at' => $group->created_at->toIso8601String(),
                ],
            ],
        ], 200);
    }

    /**
     * Get group statistics
     */
    public function stats($groupId, Request $request)
    {
        $user = Auth::user();

        $group = Group::with('users')->find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        // Check if user is a member
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        $memberCount = $group->users->count();

        // Privacy: Don't show stats if less than 3 members (to prevent identification)
        if ($memberCount < 3) {
            return response()->json([
                'success' => true,
                'data' => [
                    'privacy_notice' => 'Stats will be available when the group has at least 3 members.',
                    'member_count' => $memberCount,
                    'minimum_required' => 3,
                ],
            ], 200);
        }

        // Get period from query params (default: 7 days)
        $period = $request->query('period', '7d');

        $startDate = match($period) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subDays(7),
        };

        // Average mood for period
        $avgMood = $group->getAverageMood($startDate, now());

        // Mood trend (daily averages)
        $days = match($period) {
            '24h' => 1,
            '7d' => 7,
            '30d' => 30,
            default => 7,
        };

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAvg = $group->getAverageMood($date->startOfDay(), $date->endOfDay());
            $trend[] = [
                'date' => $date->toDateString(),
                'average_mood' => round($dayAvg, 1),
            ];
        }

        // Activity rate today
        $membersWhoLoggedToday = $group->moodEntries()
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');

        $activityRate = round(($membersWhoLoggedToday / $memberCount) * 100);

        // Mood distribution (1-10 scale)
        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $count = $group->moodEntries()
                ->where('created_at', '>=', $startDate)
                ->whereBetween('mood_score', [$i - 0.5, $i + 0.5])
                ->count();

            $distribution[] = [
                'mood_score' => $i,
                'count' => $count,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'member_count' => $memberCount,
                'average_mood' => round($avgMood, 1),
                'activity_today' => [
                    'members_logged' => $membersWhoLoggedToday,
                    'total_members' => $memberCount,
                    'percentage' => $activityRate,
                ],
                'mood_trend' => $trend,
                'mood_distribution' => $distribution,
            ],
        ], 200);
    }
}
