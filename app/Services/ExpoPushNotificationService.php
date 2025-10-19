<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushNotificationService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send a push notification via Expo Push API
     *
     * @param string $pushToken The Expo push token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data to send with notification
     * @param array $options Additional options (sound, badge, etc)
     * @return array Response from Expo API
     */
    public function sendNotification(
        string $pushToken,
        string $title,
        string $body,
        array $data = [],
        array $options = []
    ): array {
        try {
            // Validate Expo push token format
            if (!$this->isValidExpoPushToken($pushToken)) {
                Log::warning('Invalid Expo push token format', [
                    'token' => substr($pushToken, 0, 20) . '...'
                ]);
                return [
                    'success' => false,
                    'error' => 'Invalid push token format',
                ];
            }

            // Build notification payload
            $payload = [
                'to' => $pushToken,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => $options['sound'] ?? 'default',
                'priority' => $options['priority'] ?? 'high',
            ];

            // Add badge if provided
            if (isset($options['badge'])) {
                $payload['badge'] = $options['badge'];
            }

            // Add category/channel for Android
            if (isset($options['categoryId'])) {
                $payload['categoryId'] = $options['categoryId'];
            }

            // Send request to Expo Push API
            $response = Http::timeout(30)
                ->post(self::EXPO_PUSH_URL, $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Check if Expo API returned errors
                if (isset($result['data'][0]['status']) && $result['data'][0]['status'] === 'error') {
                    Log::error('Expo Push API returned error', [
                        'error' => $result['data'][0],
                        'token_preview' => substr($pushToken, 0, 20) . '...',
                    ]);

                    return [
                        'success' => false,
                        'error' => $result['data'][0]['message'] ?? 'Unknown error from Expo',
                    ];
                }

                Log::info('Push notification sent successfully', [
                    'token_preview' => substr($pushToken, 0, 20) . '...',
                    'title' => $title,
                ]);

                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            Log::error('Failed to send push notification', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send notification',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Exception sending push notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send push notifications to multiple tokens (batch)
     *
     * @param array $notifications Array of notification data
     * @return array Results for each notification
     */
    public function sendBatchNotifications(array $notifications): array
    {
        try {
            // Validate and prepare notifications
            $messages = [];
            foreach ($notifications as $notification) {
                if (!isset($notification['pushToken']) || !$this->isValidExpoPushToken($notification['pushToken'])) {
                    Log::warning('Skipping invalid push token in batch');
                    continue;
                }

                $messages[] = [
                    'to' => $notification['pushToken'],
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                    'data' => $notification['data'] ?? [],
                    'sound' => $notification['sound'] ?? 'default',
                    'priority' => $notification['priority'] ?? 'high',
                ];
            }

            if (empty($messages)) {
                return [
                    'success' => false,
                    'error' => 'No valid notifications to send',
                ];
            }

            // Send batch request
            $response = Http::timeout(30)
                ->post(self::EXPO_PUSH_URL, $messages);

            if ($response->successful()) {
                $result = $response->json();

                Log::info('Batch push notifications sent', [
                    'count' => count($messages),
                    'results' => $result,
                ]);

                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            Log::error('Failed to send batch push notifications', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send batch notifications',
            ];
        } catch (\Exception $e) {
            Log::error('Exception sending batch push notifications', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send event ended notification
     *
     * @param string $pushToken
     * @param string $eventTitle
     * @param string $eventId
     * @return array
     */
    public function sendEventEndedNotification(string $pushToken, string $eventTitle, string $eventId): array
    {
        return $this->sendNotification(
            pushToken: $pushToken,
            title: 'How did it go?',
            body: "Rate your experience: {$eventTitle}",
            data: [
                'type' => 'event_mood',
                'eventId' => $eventId,
                'eventTitle' => $eventTitle,
            ],
            options: [
                'sound' => 'default',
                'priority' => 'high',
                'categoryId' => 'event_reminder',
            ]
        );
    }

    /**
     * Validate Expo push token format
     *
     * @param string $token
     * @return bool
     */
    private function isValidExpoPushToken(string $token): bool
    {
        // Expo push tokens start with "ExponentPushToken["
        return str_starts_with($token, 'ExponentPushToken[') ||
               str_starts_with($token, 'ExpoPushToken[');
    }
}
