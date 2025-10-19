# Karma - Mood & Wellness Tracking Platform

A comprehensive mood and wellness tracking platform with Google Calendar integration, AI-powered selfie mood entries, and subscription management.

## Features

- **Mood Entry Tracking**: Manual and AI-powered selfie mood entries with facial expression analysis
- **Google Calendar Integration**: Automatic event synchronization with mood tracking
- **Push Notifications**: Smart notifications for event-based mood logging
- **Tags System**: Categorize mood entries with system and custom tags
- **Subscription Management**: RevenueCat integration for in-app purchases
- **Analytics & Stats**: Visualize mood patterns over time
- **Mobile-Optimized**: Responsive views for mobile and desktop

---

## Technology Stack

### Backend
- **Framework**: Laravel 10
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **APIs**: Google Calendar API, Expo Push Notifications, RevenueCat

### Frontend
- **Web**: Livewire, Alpine.js, TailwindCSS
- **Mobile**: React Native (Expo)

---

## Installation

### Prerequisites

- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Google Calendar API credentials
- RevenueCat account (for subscriptions)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd karma
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**

   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=karma
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed system data (IMPORTANT)**
   ```bash
   # Seed tags - Required for mood tagging functionality
   php artisan db:seed --class=TagSeeder
   ```

   **⚠️ The TagSeeder is required** to populate system tags (activities, emotions, health, etc.). Without this, the tagging functionality will not work properly.

7. **Configure Google Calendar (Optional)**

   See [Google Calendar Setup](#google-calendar-setup) section below.

8. **Configure Push Notifications (Optional)**

   See [EVENT_NOTIFICATIONS_SETUP.md](./EVENT_NOTIFICATIONS_SETUP.md)

9. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## Configuration

### Google Calendar Setup

1. Create a project in [Google Cloud Console](https://console.cloud.google.com)
2. Enable Google Calendar API
3. Create OAuth 2.0 credentials
4. Add to `.env`:
   ```env
   GOOGLE_CLIENT_ID=your-client-id
   GOOGLE_CLIENT_SECRET=your-client-secret
   GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
   ```

### RevenueCat Setup

1. Create account at [RevenueCat](https://www.revenuecat.com)
2. Set up your products (iOS/Android)
3. Add to `.env`:
   ```env
   REVENUECAT_API_KEY=your-api-key
   REVENUECAT_WEBHOOK_SECRET=your-webhook-secret
   ```
4. Configure webhook URL in RevenueCat dashboard:
   ```
   https://your-domain.com/api/revenuecat/webhook
   ```

See [REVENUECAT_WEBHOOK_DOCUMENTATION.md](./REVENUECAT_WEBHOOK_DOCUMENTATION.md) for details.

### Push Notifications Setup

Add to `.env`:
```env
EXPO_PUSH_URL=https://exp.host/--/api/v2/push/send
```

Configure push tokens in user profiles. See [EVENT_NOTIFICATIONS_SETUP.md](./EVENT_NOTIFICATIONS_SETUP.md)

---

## API Documentation

### Mood Entry API
See [MOOD_API_DOCUMENTATION.md](./MOOD_API_DOCUMENTATION.md)

Endpoints:
- `POST /api/moods` - Create mood entry
- `GET /api/moods` - List mood entries
- `GET /api/moods/{id}` - Get single mood entry
- `PUT /api/moods/{id}` - Update mood entry
- `DELETE /api/moods/{id}` - Delete mood entry

### Tags API
See [TAGS_API_DOCUMENTATION.md](./TAGS_API_DOCUMENTATION.md)

Endpoints:
- `GET /api/tags` - Get all tags (system + custom)
- `POST /api/tags` - Create custom tag

### Calendar Events API
See [CALENDAR_EVENTS_DOCUMENTATION.md](./CALENDAR_EVENTS_DOCUMENTATION.md)

Endpoints:
- `POST /api/user/calendar/sync` - Sync calendar events
- `GET /api/user/calendar/upcoming-events` - Get upcoming events
- `GET /api/user/calendar-status` - Get calendar sync status
- `POST /api/user/calendar/toggle` - Toggle calendar sync
- `POST /api/user/calendar/disconnect` - Disconnect calendar

### User Profile API

Endpoints:
- `GET /api/user/profile` - Get user profile
- `PUT /api/user/profile` - Update user profile
- `POST /api/user/push-token` - Register push notification token

---

## Scheduled Tasks

The application uses Laravel's scheduler for background tasks:

### Setup Scheduler

**Development**:
```bash
php artisan schedule:work
```

**Production** (add to crontab):
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Commands

| Command | Frequency | Description |
|---------|-----------|-------------|
| `notifications:send-event-ended` | Every 5 minutes | Send notifications for ended events |
| `calendar:sync-all` | Every 15 minutes | Sync Google Calendar for all users |

---

## Artisan Commands

### Mood & Calendar

```bash
# Manually sync Google Calendar (with full event details)
php artisan calendar:sync-all

# Sync specific user
php artisan calendar:sync-all --user=uuid-here

# Sync with custom event limit
php artisan calendar:sync-all --limit=100

# Clear all calendar events
php artisan calendar:clear
```

### Notifications

```bash
# Send event-ended notifications
php artisan notifications:send-event-ended

# Dry run (see what would be sent)
php artisan notifications:send-event-ended --dry-run

# Test push notification
php artisan notifications:test "ExponentPushToken[...]"
```

### Database

```bash
# Run migrations
php artisan migrate

# Seed tags (required for tagging functionality)
php artisan db:seed --class=TagSeeder

# Reset database
php artisan migrate:fresh --seed
```

---

## Database Schema

### Key Tables

- **users** - User accounts with Google OAuth and push tokens
- **mood_entries** - Mood logs with optional selfie analysis and entry_type
- **tags** - System and custom tags
- **mood_entry_tag** - Pivot table for mood-tag relationships
- **calendar_events** - Synced Google Calendar events with attendees, organizer, and conference links
- **subscriptions** - RevenueCat subscription data

### Migrations

Run in order:
1. Base tables: users, mood_entries, calendar_events, etc.
2. `2025_10_16_065033_add_mood_entry_id_to_calendar_events_table.php`
3. `2025_10_17_130814_add_face_analysis_to_mood_entries_table.php`
4. `2025_10_17_152353_add_revenuecat_fields_to_subscriptions_table.php`
5. `2025_10_19_092858_create_tags_table.php`
6. `2025_10_19_092911_create_mood_entry_tag_table.php`
7. `2025_10_19_174210_add_entry_type_to_mood_entries_table.php`
8. `2025_10_19_174912_add_event_details_to_calendar_events_table.php`

---

## Mobile App Integration

### React Native Setup

1. Install dependencies:
   ```bash
   npm install react-native-purchases expo-notifications
   ```

2. Configure RevenueCat:
   ```javascript
   import Purchases from 'react-native-purchases';

   Purchases.configure({
     apiKey: Platform.select({
       ios: 'YOUR_IOS_KEY',
       android: 'YOUR_ANDROID_KEY',
     }),
   });
   ```

3. Set user ID after login:
   ```javascript
   await Purchases.logIn(user.id); // Use backend user UUID
   ```

4. Register push token:
   ```javascript
   const token = await Notifications.getExpoPushTokenAsync();
   await api.post('/user/push-token', { push_token: token.data });
   ```

---

## Features & Usage

### Mood Entry Types

1. **Manual Entry**
   - User selects mood score (1-10)
   - Optional text note
   - Optional calendar event association
   - Optional tags

2. **Selfie Entry**
   - User takes selfie
   - ML Kit analyzes facial expression
   - Auto-suggests mood score
   - Captures: expression, energy level, environment brightness
   - Optional heart rate (BPM) via camera

### Tags System

**System Tags** (predefined):
- Activities: Work, Exercise, Study, Creative, etc.
- Social: Friends, Family, Partner, Alone, etc.
- Health: Sleep, Meditation, Nature, etc.
- Emotions: Happy, Sad, Anxious, Calm, etc.
- Weather: Sunny, Rainy, Cold, Hot, etc.

**Custom Tags**: Users can create their own tags

**Mood Associations**: Tags can be filtered by mood score relevance

### Google Calendar Integration

See [CALENDAR_EVENTS_DOCUMENTATION.md](./CALENDAR_EVENTS_DOCUMENTATION.md) for complete details.

1. User connects Google account
2. Events sync automatically every 15 minutes
3. Full event details synced: attendees, organizer, conference links
4. Click event to view detailed modal with:
   - Complete attendee list with RSVP status (Going/Declined/Maybe/Pending)
   - Organizer information
   - Conference/video meeting links (Google Meet, Zoom, etc.)
   - Event description and location
   - Link to view in Google Calendar
5. When event ends, push notification sent
6. User taps notification → logs mood for that event
7. Analytics show mood patterns by event type

### Push Notifications

**Event-Ended Notifications**:
- Sent 0-15 minutes after event ends
- Only if user has calendar sync enabled
- Respects quiet hours
- Deep links to mood entry screen with event context

---

## Testing

### Backend Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=MoodEntryTest
```

### API Testing

Use the provided test commands:

```bash
# Test push notification
php artisan notifications:test "ExponentPushToken[...]" --delay=10

# Test event notifications (dry run)
php artisan notifications:send-event-ended --dry-run

# Test calendar sync
php artisan calendar:sync-all --user=uuid-here
```

---

## Deployment

### Production Checklist

- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed tags: `php artisan db:seed --class=TagSeeder`
- [ ] Configure cron for scheduler
- [ ] Set up SSL certificate (required for webhooks)
- [ ] Configure Google OAuth credentials
- [ ] Configure RevenueCat webhook URL
- [ ] Test push notifications end-to-end
- [ ] Configure environment variables
- [ ] Enable query caching
- [ ] Set up log rotation
- [ ] Configure backups

### Environment Variables

Required in production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=...

REVENUECAT_API_KEY=...
REVENUECAT_WEBHOOK_SECRET=...

EXPO_PUSH_URL=https://exp.host/--/api/v2/push/send
```

---

## Troubleshooting

### Tags Not Showing

**Problem**: Tags API returns empty results

**Solution**:
```bash
php artisan db:seed --class=TagSeeder
```

### Calendar Not Syncing

**Problem**: Events not appearing

**Solution**:
```bash
# Check sync status
php artisan calendar:sync-all --user=uuid-here

# Check logs
tail -f storage/logs/laravel.log | grep -i calendar
```

### Push Notifications Not Working

**Problem**: Notifications not being received

**Solution**: See [EVENT_NOTIFICATIONS_SETUP.md](./EVENT_NOTIFICATIONS_SETUP.md#troubleshooting)

### Subscription Not Updating

**Problem**: RevenueCat purchases not reflecting

**Solution**: See [REVENUECAT_WEBHOOK_DOCUMENTATION.md](./REVENUECAT_WEBHOOK_DOCUMENTATION.md#troubleshooting)

---

## Documentation

- [Mood Entry API](./MOOD_API_DOCUMENTATION.md)
- [Tags API](./TAGS_API_DOCUMENTATION.md)
- [Calendar Events](./CALENDAR_EVENTS_DOCUMENTATION.md)
- [RevenueCat Webhook](./REVENUECAT_WEBHOOK_DOCUMENTATION.md)
- [Event Notifications Setup](./EVENT_NOTIFICATIONS_SETUP.md)
- [Native App Integration](./NATIVE_APP_INTEGRATION.md)
- [Onboarding System](./ONBOARDING_SYSTEM.md)

---

## License

Proprietary - All rights reserved

---

## Support

For issues, bugs, or questions:
- Email: development@feelith.com
- Create an issue in the repository

---

**Last Updated**: October 19, 2025
**Version**: 1.1.0
