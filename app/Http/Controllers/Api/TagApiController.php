<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagApiController extends Controller
{
    /**
     * Get tags for a specific mood score
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $moodScore = $request->query('mood_score');

            $query = Tag::query();

            // If mood_score is provided, filter tags relevant to that score
            if ($moodScore) {
                $query->forMoodScore((int) $moodScore);
            }

            // Get system tags
            $systemTags = $query->system()->orderBy('name')->get();

            // Get user's custom tags
            $customTags = Tag::customForUser(auth()->id())->orderBy('name')->get();

            return response()->json([
                'system_tags' => $systemTags,
                'custom_tags' => $customTags,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching tags: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a custom tag
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'emoji' => 'nullable|string|max:10',
            'category' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = Tag::create([
                'name' => $request->name,
                'emoji' => $request->emoji,
                'category' => $request->category ?? 'custom',
                'is_custom' => true,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Tag created successfully',
                'tag' => $tag,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating tag: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
