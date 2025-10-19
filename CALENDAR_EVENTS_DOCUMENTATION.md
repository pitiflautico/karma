# Calendar Events Documentation

## Overview
The Calendar Events system integrates with Google Calendar to sync events and display detailed event information including attendees, organizers, and conference links.

## Database Schema

### calendar_events Table
```sql
- id (uuid, primary key)
- user_id (uuid, foreign key to users)
- google_event_id (string, unique)
- title (string)
- description (text, nullable)
- location (string, nullable)
- attendees (json, nullable)
- organizer_email (string, nullable)
- organizer_name (string, nullable)
- event_link (string, nullable)
- conference_link (string, nullable)
- status (string, nullable)
- start_time (datetime)
- end_time (datetime)
- event_type (string, nullable)
- is_all_day (boolean)
- reminder_sent (boolean)
- reminder_sent_at (datetime, nullable)
- mood_entry_id (uuid, nullable)
- mood_prompted (boolean)
- prompted_at (datetime, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Attendees Structure
The `attendees` field is stored as JSON with the following structure:
```json
[
  {
    "email": "user@example.com",
    "name": "John Doe",
    "response_status": "accepted|declined|tentative|needsAction",
    "optional": false
  }
]
```

## Google Calendar Sync

### Sync Service
**File:** `app/Services/GoogleCalendarService.php`

The service syncs the following data from Google Calendar:
- Event basic information (title, description, location, dates)
- **Attendees list** with response status (accepted, declined, tentative, pending)
- **Organizer** information (name and email)
- **Conference links** (Google Meet, Zoom, etc.)
- **Event link** to Google Calendar
- **Event status**

### Manual Sync
```bash
php artisan calendar:sync-all
php artisan calendar:sync-all --user={user_id}
php artisan calendar:sync-all --limit=100
```

## API Endpoints

### Sync Calendar Events
**POST** `/api/user/calendar/sync`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response:**
```json
{
  "success": true,
  "message": "Calendar synced successfully",
  "data": {
    "events_synced": 30,
    "last_sync_at": "2025-10-19T17:58:01.000000Z"
  }
}
```

### Get Upcoming Events
**GET** `/api/user/calendar/upcoming-events`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "events": [
    {
      "id": "uuid",
      "title": "Team Meeting",
      "description": "Weekly sync",
      "location": "Conference Room A",
      "attendees": [
        {
          "email": "john@example.com",
          "name": "John Doe",
          "response_status": "accepted",
          "optional": false
        }
      ],
      "organizer_email": "organizer@example.com",
      "organizer_name": "Organizer Name",
      "event_link": "https://www.google.com/calendar/event?eid=...",
      "conference_link": "https://meet.google.com/abc-defg-hij",
      "status": "confirmed",
      "start_time": "2025-10-20T10:00:00.000000Z",
      "end_time": "2025-10-20T11:00:00.000000Z",
      "is_all_day": false
    }
  ]
}
```

## Web Interface

### Calendar Events Page
**Route:** `/calendar-events`

**Features:**
- Lists all upcoming Google Calendar events
- Click on event to view detailed information in modal
- Sync button to manually trigger calendar sync
- Shows last sync timestamp
- Mobile and desktop responsive

### Event Detail Modal
**Component:** `CalendarEventDetailModal`

**Displays:**
- Event title and description
- Date, time, and location
- Conference/video meeting link (if available)
- Organizer information
- Complete attendee list with RSVP status badges:
  - **Going** (green) - Accepted
  - **Declined** (red) - Declined invitation
  - **Maybe** (yellow) - Tentatively accepted
  - **Pending** (gray) - No response yet
- Optional attendee indicator
- Link to view event in Google Calendar

### Attendee Status Badges
```blade
Going      - bg-green-100 text-green-800
Declined   - bg-red-100 text-red-800
Maybe      - bg-yellow-100 text-yellow-800
Pending    - bg-gray-100 text-gray-600
```

## Mobile Integration

### Event Detail View
The event detail modal is fully mobile-optimized with:
- Safe area support for iOS notch
- Touch-friendly interface
- Scrollable content for long attendee lists
- Bottom sheet style on mobile
- Backdrop dismiss on click

### Opening Modal
```javascript
// Dispatch event to open modal
Livewire.dispatch('viewEventDetails', { eventId: 'event-uuid' });
```

## Migration Steps

### Production Deployment
1. Run migrations:
```bash
myforge-artisan feelith "migrate --force"
```

2. Clear existing events (optional, for clean slate):
```bash
myforge-artisan feelith "calendar:clear"
```

3. Sync all user calendars:
```bash
myforge-artisan feelith "calendar:sync-all"
```

## Model Methods

### CalendarEvent Model
```php
// Check if event has ended
$event->hasEnded(): bool

// Check if mood has been logged
$event->hasMoodLogged(): bool

// Get mood entry
$event->moodEntry()

// Get user
$event->user()
```

## Features

### Incremental Sync
The service uses Google's sync tokens for incremental synchronization, only fetching new or modified events instead of all events each time.

### Conference Link Detection
Automatically detects and extracts video conference links from:
- Google Meet
- Zoom
- Microsoft Teams
- Other video conferencing platforms

### Attendee Management
- Tracks all invited attendees
- Shows response status (accepted, declined, tentative, pending)
- Identifies optional vs required attendees
- Displays attendee names and emails

## Error Handling

### Common Issues
1. **Missing columns error** - Run migrations
2. **Sync token expired** - Service automatically clears token and retries with full sync
3. **Array to string conversion** - Fixed in SyncAllGoogleCalendars command

### Debugging
Check logs for sync errors:
```bash
tail -f storage/logs/laravel.log | grep "Calendar sync"
```

## Security

- Users can only view their own calendar events
- Event data is tied to user_id
- All API endpoints require authentication
- Conference links are only shown to authorized users

## Performance

- Uses eager loading for relationships
- Implements pagination for large event lists
- Caches sync tokens to reduce API calls
- Background sync via scheduled tasks

## Future Enhancements

- [ ] Calendar event categories/tags
- [ ] Event color coding
- [ ] Event reminders customization
- [ ] Recurring events support improvements
- [ ] Multi-calendar support
- [ ] Event conflict detection
