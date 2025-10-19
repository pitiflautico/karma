<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;

class GoogleCalendarService
{
    /**
     * Fetch events from Google Calendar and store them
     * Uses incremental sync to only fetch new/modified events
     */
    public function syncEvents(User $user, int $limit = 10): array
    {
        if (!$user->calendar_sync_enabled || !$user->google_calendar_token) {
            return [];
        }

        try {
            $client = $this->getGoogleClient($user);
            $service = new Calendar($client);

            // Build sync parameters
            $optParams = [
                'maxResults' => $limit,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => Carbon::now()->toRfc3339String(),
            ];

            // Use sync token for incremental sync if available
            if ($user->google_calendar_sync_token) {
                unset($optParams['timeMin']); // syncToken and timeMin are mutually exclusive
                $optParams['syncToken'] = $user->google_calendar_sync_token;
            }

            $results = $service->events->listEvents('primary', $optParams);
            $events = $results->getItems();

            $syncedEvents = [];
            $googleEventIds = [];

            foreach ($events as $event) {
                // Skip cancelled events - delete them from our database
                if ($event->getStatus() === 'cancelled') {
                    CalendarEvent::where('user_id', $user->id)
                        ->where('google_event_id', $event->getId())
                        ->delete();
                    continue;
                }

                $start = $event->start->dateTime ?: $event->start->date;
                $end = $event->end->dateTime ?: $event->end->date;

                $googleEventIds[] = $event->getId();

                // Extract attendees
                $attendees = [];
                if ($event->getAttendees()) {
                    foreach ($event->getAttendees() as $attendee) {
                        $attendees[] = [
                            'email' => $attendee->getEmail(),
                            'name' => $attendee->getDisplayName(),
                            'response_status' => $attendee->getResponseStatus(),
                            'optional' => $attendee->getOptional() ?? false,
                        ];
                    }
                }

                // Extract organizer
                $organizer = $event->getOrganizer();

                // Extract conference data (Google Meet, Zoom, etc)
                $conferenceLink = null;
                if ($event->getConferenceData() && $event->getConferenceData()->getEntryPoints()) {
                    foreach ($event->getConferenceData()->getEntryPoints() as $entryPoint) {
                        if ($entryPoint->getEntryPointType() === 'video') {
                            $conferenceLink = $entryPoint->getUri();
                            break;
                        }
                    }
                }

                $calendarEvent = CalendarEvent::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'google_event_id' => $event->getId(),
                    ],
                    [
                        'title' => $event->getSummary() ?: 'No Title',
                        'description' => $event->getDescription(),
                        'start_time' => Carbon::parse($start),
                        'end_time' => Carbon::parse($end),
                        'location' => $event->getLocation(),
                        'attendees' => $attendees,
                        'organizer_email' => $organizer ? $organizer->getEmail() : null,
                        'organizer_name' => $organizer ? $organizer->getDisplayName() : null,
                        'event_link' => $event->getHtmlLink(),
                        'conference_link' => $conferenceLink,
                        'status' => $event->getStatus(),
                    ]
                );

                $syncedEvents[] = $calendarEvent;
            }

            // Store the new sync token for next incremental sync
            $nextSyncToken = $results->getNextSyncToken();
            if ($nextSyncToken) {
                $user->update([
                    'google_calendar_sync_token' => $nextSyncToken,
                    'last_calendar_sync_at' => now(),
                ]);
            }

            // Delete events that no longer exist in Google Calendar
            // (only for events that were in the sync window but not returned)
            if (!empty($googleEventIds)) {
                CalendarEvent::where('user_id', $user->id)
                    ->where('start_time', '>=', now())
                    ->whereNotIn('google_event_id', $googleEventIds)
                    ->delete();
            }

            \Log::info("Calendar synced for user {$user->id}", [
                'events_synced' => count($syncedEvents),
                'sync_token' => $nextSyncToken ? 'updated' : 'none',
            ]);

            return $syncedEvents;
        } catch (\Google\Service\Exception $e) {
            // If sync token is invalid, clear it and retry with full sync
            if ($e->getCode() === 410) {
                \Log::warning("Sync token expired for user {$user->id}, clearing and retrying");
                $user->update(['google_calendar_sync_token' => null]);
                return $this->syncEvents($user, $limit);
            }

            \Log::error('Google Calendar Sync Error: ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get configured Google Client
     */
    private function getGoogleClient(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($user->google_calendar_token);

        // Refresh token if expired
        if ($client->isAccessTokenExpired() && $user->google_calendar_refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($user->google_calendar_refresh_token);
            $newToken = $client->getAccessToken();

            $user->update([
                'google_calendar_token' => $newToken['access_token'] ?? $newToken,
            ]);
        }

        return $client;
    }
}
