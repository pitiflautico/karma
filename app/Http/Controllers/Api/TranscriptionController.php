<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranscriptionController extends Controller
{
    /**
     * Transcribe audio file to text using OpenAI Whisper
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transcribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio' => 'required|file|mimes:m4a,mp3,wav,webm|max:25600', // Max 25MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('audio');
            $apiKey = config('services.openai.api_key');

            if (!$apiKey) {
                Log::error('OpenAI API key not configured');
                return response()->json([
                    'message' => 'Transcription service not configured',
                ], 500);
            }

            // Send audio to OpenAI Whisper API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI transcription failed: ' . $response->body());
                return response()->json([
                    'message' => 'Failed to transcribe audio',
                    'error' => $response->json('error.message', 'Unknown error'),
                ], 500);
            }

            $transcription = $response->json('text', '');

            return response()->json([
                'text' => $transcription,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error transcribing audio: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to transcribe audio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
