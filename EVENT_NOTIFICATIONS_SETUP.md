# Event Notifications System - Setup Guide

This guide explains how to set up and configure the automatic push notifications system for Google Calendar events.

## Overview

When a Google Calendar event ends, the system automatically sends a push notification to the user's mobile device asking them to rate their experience. The user can tap the notification to open the native mood entry screen and log their feelings about the event.

## System Components

### Backend (Laravel)
- **`ExpoPushNotificationService`**: Service that sends push notifications via Expo Push API
- **`SendEventEndedNotifications`**: Artisan command that finds ended events and sends notifications
- **Scheduler**: Runs the command every 5 minutes to check for events that ended in the last 15 minutes

### Mobile App (React Native/Expo)
- **`pushHandler.js`**: Handles incoming notifications and routes them to the correct screen
- **`MoodEntryScreen`**: Native screen for creating mood entries
- **Expo Notifications**: Receives and displays push notifications

## Setup Instructions

### 1. Configure Push Tokens Registration

Ensure your backend has the push tokens registered for users:

```bash
# Check if push_token column exists in users table
php artisan tinker
>>> User::first()->push_token
```

If the column doesn't exist, create a migration:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('push_token')->nullable();
    $table->boolean('push_enabled')->default(true);
});
```

### 2. Test the Notification System

#### Option A: Get Push Token from Mobile App (Easiest)

In the mobile app (development mode):

1. Open the app on your **physical device** (push notifications don't work on simulators)
2. Go to **Settings** → **Privacy** tab
3. Scroll down to the **"Push Token (Debug)"** section (only visible in dev mode)
4. If you see a token, tap **"Copy"** to copy it to clipboard
5. If no token is shown, tap **"Get Token"** or **"Refresh & Register"**
6. The token will be copied automatically to your clipboard

#### Option B: Get Push Token from Logs

Check the mobile app console logs when logging in:
```
[PushTokenService] Push token obtained: ExponentPushToken[...]
```

#### Send Test Notification

Once you have your push token, send a test notification:

```bash
# Paste your token here (it will be in your clipboard if you used Option A)
php artisan notifications:test "ExponentPushToken[YOUR_TOKEN_HERE]" --delay=10
```

This will:
- Wait 10 seconds (giving you time to close the app/lock screen)
- Send a test "event_mood" notification
- You should see the notification appear on your device
- Tap it to open the native mood entry screen

### 3. Test with Real Events

Create a calendar event that ends in 2-3 minutes and test the flow:

```bash
# Wait for the event to end, then manually trigger the command
php artisan notifications:send-event-ended --minutes=15

# Or use dry-run mode to see what would be sent
php artisan notifications:send-event-ended --dry-run
```

### 4. Enable Automatic Scheduling (Development)

The scheduler is already configured in `routes/console.php` to run every 5 minutes.

To run the scheduler in development:

```bash
# Option 1: Run scheduler continuously (recommended for dev)
php artisan schedule:work

# Option 2: Run scheduler once (for testing)
php artisan schedule:run
```

You should see output like:
```
Running scheduled command: notifications:send-event-ended --minutes=15 .... DONE (0.5s)
```

### 5. Enable Automatic Scheduling (Production)

#### Option A: Using Laravel Forge (Recommended)

1. Go to your Laravel Forge server dashboard
2. Navigate to "Scheduler" tab
3. Ensure the cron job is enabled (should be added automatically)
4. The cron runs every minute and Laravel's scheduler handles the timing

Forge automatically adds this cron:
```bash
* * * * * cd /home/forge/your-site.com && php artisan schedule:run >> /dev/null 2>&1
```

#### Option B: Manual Cron Configuration

Add this cron job to your server:

```bash
# Edit crontab
crontab -e

# Add this line (replace paths as needed)
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Option C: Using Systemd Timer (Advanced)

Create a systemd service and timer:

```bash
# /etc/systemd/system/laravel-scheduler.service
[Unit]
Description=Laravel Scheduler
After=network.target

[Service]
Type=oneshot
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php artisan schedule:run

# /etc/systemd/system/laravel-scheduler.timer
[Unit]
Description=Laravel Scheduler Timer

[Timer]
OnCalendar=*:0/1
Persistent=true

[Install]
WantedBy=timers.target

# Enable and start
sudo systemctl enable laravel-scheduler.timer
sudo systemctl start laravel-scheduler.timer
```

### 6. Verify Scheduling is Working

Check the Laravel logs to see scheduled tasks running:

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Or check the last scheduled run
php artisan schedule:list
```

Expected output:
```
  0 */5 * * *  notifications:send-event-ended --minutes=15 ... Next Due: 3 minutes from now
```

## Monitoring and Debugging

### Check for Events to Notify

```bash
# See what events would be notified (dry-run mode)
php artisan notifications:send-event-ended --dry-run --minutes=15
```

### Check Push Token Status

```bash
php artisan tinker

# Check a user's push configuration
>>> $user = User::find(1);
>>> $user->push_token;
>>> $user->push_enabled;
>>> $user->calendar_sync_enabled;
```

### View Scheduled Tasks

```bash
# List all scheduled tasks and their next run time
php artisan schedule:list

# Test a specific scheduled task
php artisan schedule:test
```

### Check Logs

```bash
# Backend logs (Laravel)
tail -f storage/logs/laravel.log | grep -i "notification\|event"

# In production (Forge)
# Go to Forge → Sites → Logs
# Or SSH and check: /home/forge/your-site.com/storage/logs/laravel.log
```

### Mobile App Debugging

Enable DEBUG mode in the mobile app:

```javascript
// src/config/config.js
DEBUG: true,
```

Check logs for notification handling:
- `[PushHandler] Handling notification:`
- `[PushHandler] Opening native mood entry for event:`
- `[MoodEntry] Mood entry saved successfully`

## Customization

### Change Notification Frequency

Edit `routes/console.php`:

```php
// Run every 10 minutes instead of 5
Schedule::command('notifications:send-event-ended --minutes=20')
    ->everyTenMinutes()
    ->name('send-event-ended-notifications');
```

### Customize Notification Text

Edit `app/Services/ExpoPushNotificationService.php`:

```php
public function sendEventEndedNotification(string $pushToken, string $eventTitle, string $eventId): array
{
    return $this->sendNotification(
        pushToken: $pushToken,
        title: 'Your custom title',
        body: "Your custom message: {$eventTitle}",
        // ...
    );
}
```

### Adjust Time Window

```bash
# Look for events that ended in the last 30 minutes
php artisan notifications:send-event-ended --minutes=30
```

Update scheduler:
```php
Schedule::command('notifications:send-event-ended --minutes=30')
    ->everyTenMinutes()
    ->name('send-event-ended-notifications');
```

## Troubleshooting

### Notifications Not Sending

1. **Check if scheduler is running:**
   ```bash
   php artisan schedule:work
   ```

2. **Verify cron is working (production):**
   ```bash
   crontab -l  # Should show the Laravel scheduler cron
   ```

3. **Check user has valid push token:**
   ```bash
   php artisan tinker
   >>> User::whereNotNull('push_token')->where('push_enabled', true)->count()
   ```

4. **Verify events exist:**
   ```bash
   php artisan notifications:send-event-ended --dry-run
   ```

### Push Token Invalid

If you get "DeviceNotRegistered" errors:
- The user needs to log in again on their mobile device
- The push token will be automatically re-registered
- Old tokens are automatically disabled

### Notifications Sending During Quiet Hours

Users can set quiet hours in their profile:
- Go to Calendar Settings
- Set "Quiet Hours Start" and "Quiet Hours End"
- Notifications will be skipped during these hours

Check if quiet hours are being respected:
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->quiet_hours_start;  // e.g., "22:00"
>>> $user->quiet_hours_end;    // e.g., "08:00"
```

## Production Checklist

- [ ] Cron job is configured (via Forge or manually)
- [ ] Scheduler runs every minute: `* * * * *`
- [ ] Push notification command runs every 5 minutes
- [ ] Google Calendar sync runs every 15 minutes
- [ ] Logs are being written to `storage/logs/laravel.log`
- [ ] Mobile app registers push tokens on login
- [ ] Test notification works: `php artisan notifications:test`
- [ ] Real event notifications work end-to-end
- [ ] Quiet hours are respected
- [ ] Failed tokens are automatically disabled

## Architecture Diagram

```
┌─────────────────┐
│  Google Calendar │
└────────┬─────────┘
         │ Sync (every 15min)
         ▼
┌─────────────────────────────┐
│  Laravel Backend            │
│  ┌───────────────────────┐  │
│  │ CalendarEvent Model   │  │
│  │ - end_time           │  │
│  │ - reminder_sent      │  │
│  │ - mood_entry_id      │  │
│  └───────────────────────┘  │
│           │                  │
│           │ Scheduler        │
│           │ (every 5min)     │
│           ▼                  │
│  ┌───────────────────────┐  │
│  │ SendEventEnded        │  │
│  │ Command               │  │
│  │ - Finds ended events  │  │
│  │ - Sends notifications │  │
│  └───────────┬───────────┘  │
│              │              │
│              │ Expo Push API│
└──────────────┼──────────────┘
               │
               ▼
      ┌────────────────┐
      │  Mobile Device  │
      │  ┌───────────┐  │
      │  │  Expo     │  │
      │  │  Push     │  │
      │  └─────┬─────┘  │
      │        │        │
      │        ▼        │
      │  ┌───────────┐  │
      │  │ Push      │  │
      │  │ Handler   │  │
      │  └─────┬─────┘  │
      │        │        │
      │        ▼        │
      │  ┌───────────┐  │
      │  │ Native    │  │
      │  │ Mood      │  │
      │  │ Screen    │  │
      │  └───────────┘  │
      └────────────────┘
```

## Manual Calendar Synchronization

### Sync All Users

Force sync Google Calendar events for all users with calendar sync enabled:

```bash
# Sync all users
php artisan calendar:sync-all

# Sync with custom event limit per user
php artisan calendar:sync-all --limit=100
```

### Sync Specific User

Sync calendar for a single user by ID:

```bash
# Sync specific user
php artisan calendar:sync-all --user=uuid-here

# Sync specific user with custom limit
php artisan calendar:sync-all --user=uuid-here --limit=200
```

### When to Use Manual Sync

- **After changing calendar settings**: User enables/disables calendar sync
- **Troubleshooting**: Events not appearing or stuck
- **Initial setup**: Force sync after connecting Google Calendar
- **Bulk operations**: Sync all users after system maintenance

### Command Output

```bash
🔄 Starting Google Calendar synchronization...
📋 Found 3 user(s) to sync
👤 Syncing: John Doe (john@example.com)
   📅 Synced 15 event(s)
   ⏰ Last sync: 2025-10-19 12:30:00
👤 Syncing: Jane Smith (jane@example.com)
   📅 Synced 8 event(s)
   ⏰ Last sync: 2025-10-19 12:30:01

📊 Summary:
   ✅ Synced: 3

✅ Synchronization complete!
```

### Options

| Option | Description | Default |
|--------|-------------|---------|
| `--user=UUID` | Sync only specific user | All users |
| `--limit=N` | Max events per user | 50 |

---

## Support

If you encounter issues:
1. Check logs: `storage/logs/laravel.log`
2. Run dry-run: `php artisan notifications:send-event-ended --dry-run`
3. Test with: `php artisan notifications:test "YourPushToken"`
4. Verify scheduler: `php artisan schedule:list`
5. Force sync calendar: `php artisan calendar:sync-all`
