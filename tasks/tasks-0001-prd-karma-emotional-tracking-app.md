## Relevant Files

### Core Models & Migrations
- `app/Models/User.php` - User model with Google Calendar integration fields and relationships
- `app/Models/MoodEntry.php` - Core mood tracking model with calendar event relationships
- `app/Models/CalendarEvent.php` - Google Calendar events synchronized to local database
- `app/Models/EmotionalSelfie.php` - Emotional selfies with mood correlation
- `app/Models/Group.php` - Group mood tracking spaces with invite codes
- `app/Models/Subscription.php` - User subscription and payment tracking
- `app/Models/MoodPrompt.php` - Automated mood prompts after calendar events
- `database/migrations/2025_10_07_100400_create_mood_entries_table.php` - MoodEntry schema
- `database/migrations/2025_10_07_100300_create_calendar_events_table.php` - CalendarEvent schema
- `database/migrations/2025_10_07_100600_create_shared_access_table.php` - Private sharing permissions

### Livewire Components (Web UI)
- `app/Livewire/Dashboard.php` - Main user dashboard with mood prompts and stats
- `app/Livewire/MoodEntryForm.php` - Manual mood logging form
- `app/Livewire/Calendar.php` - Calendar view with mood visualization
- `app/Livewire/CalendarEvents.php` - Calendar events list
- `app/Livewire/MoodPrompts.php` - Pending mood prompts management
- `resources/views/livewire/dashboard.blade.php` - Dashboard view
- `resources/views/livewire/mood-entry-form.blade.php` - Mood entry form view
- `resources/views/livewire/calendar.blade.php` - Calendar view

### Services & Jobs
- `app/Services/GoogleCalendarService.php` - Google Calendar API integration with incremental sync
- `app/Jobs/SyncGoogleCalendar.php` - **TO CREATE** - Scheduled job for calendar sync
- `app/Jobs/SendMoodReminders.php` - **TO CREATE** - Smart mood reminder notifications
- `app/Jobs/GenerateMoodPrompts.php` - **TO CREATE** - Automated prompt generation
- `app/Services/MoodAnalyticsService.php` - **TO CREATE** - Analytics and insights engine
- `app/Services/StripeService.php` - **TO CREATE** - Payment processing

### Controllers
- `app/Http/Controllers/Auth/GoogleAuthController.php` - Google OAuth and calendar sync
- `app/Http/Controllers/Api/AuthController.php` - API authentication for future mobile app
- `app/Http/Controllers/MoodEntryController.php` - **TO CREATE** - Mood entry CRUD operations
- `app/Http/Controllers/GroupController.php` - **TO CREATE** - Group management
- `app/Http/Controllers/SubscriptionController.php` - **TO CREATE** - Subscription management

### Filament Admin Resources
- `app/Filament/Resources/UserResource.php` - User management in admin panel
- `app/Filament/Resources/MoodEntryResource.php` - Mood entries management
- `app/Filament/Resources/GroupResource.php` - Group spaces management
- `app/Filament/Resources/SubscriptionResource.php` - Subscription management
- `app/Filament/Widgets/MoodStatisticsWidget.php` - **TO CREATE** - System-wide mood analytics
- `app/Filament/Widgets/UserActivityWidget.php` - **TO CREATE** - User engagement metrics

### Routes
- `routes/web.php` - Web application routes (Livewire components)
- `routes/api.php` - API routes for authentication and future mobile app
- `routes/admin.php` - Admin panel routes

### Tests
- `tests/Feature/MoodEntryTest.php` - **TO CREATE** - Feature tests for mood tracking
- `tests/Feature/GoogleCalendarSyncTest.php` - **TO CREATE** - Calendar integration tests
- `tests/Feature/GroupMoodTrackingTest.php` - **TO CREATE** - Group features tests
- `tests/Feature/PrivateSharingTest.php` - **TO CREATE** - Private sharing tests
- `tests/Unit/MoodAnalyticsServiceTest.php` - **TO CREATE** - Analytics service tests
- `tests/Unit/GoogleCalendarServiceTest.php` - **TO CREATE** - Calendar service tests

### Configuration
- `config/services.php` - Google OAuth and Stripe API configuration
- `config/karma.php` - **TO CREATE** - Karma app-specific settings

## Notes

* Unit/Feature tests should typically be placed under `tests/Unit` and `tests/Feature` respectively, close to the code they verify when practical.
* Use `php artisan test [optional/path/to/test]` to run tests. Running without a path executes all tests found by the PHPUnit/Pest configuration.
* **IMPORTANT:** We are building the WEB application FIRST (Laravel + Livewire/React). Mobile app (React Native) will be developed in a later phase.
* Many core features are already partially implemented - tasks focus on completing, enhancing, and testing existing functionality.
* All Livewire components should use the existing `layouts.app` layout
* Use Filament Apex Charts package (already installed: `leandrocfe/filament-apex-charts`) for analytics visualizations
* Use Laravel Queue system for background jobs (calendar sync, notifications)
* Store emotional selfies using Laravel Storage (local for dev, S3 for production)

## Tasks

* [ ] 1.0 Complete Core Mood Tracking (Web - Livewire)

  * [ ] 1.1 Enhance `MoodEntryForm.php` to support calendar event context display
  * [ ] 1.2 Add mood entry editing capability (users can edit/delete past entries)
  * [ ] 1.3 Create `MoodHistory.php` Livewire component for viewing all mood entries with filters
  * [ ] 1.4 Add mood entry validation rules (500 character limit for notes, 1-10 scale)
  * [ ] 1.5 Implement mood category badges (low/medium/good/excellent) in UI
  * [ ] 1.6 Create `resources/views/livewire/mood-history.blade.php` with table/list view
  * [ ] 1.7 Add search and filter functionality (by date range, mood level, event type)
  * [ ] 1.8 Implement pagination for mood history (20 entries per page)
  * [ ] 1.9 Write Feature test `tests/Feature/MoodEntryTest.php` covering CRUD operations
  * [ ] 1.10 Add user feedback messages (success/error toasts) for all mood operations

* [ ] 2.0 Enhance Google Calendar Integration & Implement Smart Notifications

  * [ ] 2.1 Create `app/Jobs/SyncGoogleCalendar.php` job for scheduled calendar syncing
  * [ ] 2.2 Register job in `app/Console/Kernel.php` to run every 15 minutes
  * [ ] 2.3 Create `app/Jobs/GenerateMoodPrompts.php` to create prompts after events end
  * [ ] 2.4 Implement smart timing logic: delay prompts if back-to-back events detected
  * [ ] 2.5 Add quiet hours check in prompt generation (respect user's `quiet_hours_start/end`)
  * [ ] 2.6 Create `app/Notifications/MoodReminderNotification.php` for web notifications
  * [ ] 2.7 Enhance `Dashboard.php` to display pending prompts with event context
  * [ ] 2.8 Add calendar sync manual trigger button in settings/dashboard
  * [ ] 2.9 Implement error handling for calendar sync failures (show user-friendly messages)
  * [ ] 2.10 Create `app/Livewire/CalendarSettings.php` for managing sync preferences
  * [ ] 2.11 Add UI for selecting which calendars to sync (primary vs all)
  * [ ] 2.12 Add UI for configuring quiet hours (time picker components)
  * [ ] 2.13 Write Feature test `tests/Feature/GoogleCalendarSyncTest.php`
  * [ ] 2.14 Write Unit test `tests/Unit/GoogleCalendarServiceTest.php`

* [ ] 3.0 Build Reports & Analytics Dashboard

  * [ ] 3.1 Create `app/Services/MoodAnalyticsService.php` with analysis methods
  * [ ] 3.2 Implement `getCorrelationsByEventType()` method (event types vs mood scores)
  * [ ] 3.3 Implement `getMoodTrendsByTimeRange()` method (daily/weekly/monthly trends)
  * [ ] 3.4 Implement `getWeeklySummaryReport()` method (average, high, low, event count)
  * [ ] 3.5 Implement `getMoodPatternsByDayOfWeek()` method (which days are better/worse)
  * [ ] 3.6 Create `app/Livewire/Reports.php` component for reports dashboard
  * [ ] 3.7 Create `resources/views/livewire/reports.blade.php` with chart containers
  * [ ] 3.8 Integrate Filament Apex Charts for mood trend line charts
  * [ ] 3.9 Add bar charts for event type correlations
  * [ ] 3.10 Add day-of-week mood pattern visualization
  * [ ] 3.11 Implement date range filter (week/month/quarter/year/custom)
  * [ ] 3.12 Implement event type filter dropdown
  * [ ] 3.13 Add insight cards with text insights ("You feel better after X", "Mood lower on Y")
  * [ ] 3.14 Add weekly/monthly summary email option (schedule in `Kernel.php`)
  * [ ] 3.15 Write Unit test `tests/Unit/MoodAnalyticsServiceTest.php`
  * [ ] 3.16 Add route to `routes/web.php` for reports page

* [ ] 4.0 Implement Social Features - Private Sharing

  * [ ] 4.1 Create `app/Livewire/SharingSettings.php` for managing trusted contacts
  * [ ] 4.2 Create `resources/views/livewire/sharing-settings.blade.php`
  * [ ] 4.3 Implement invite system (send invite via email with unique token)
  * [ ] 4.4 Create `app/Models/SharingInvite.php` model for pending invites
  * [ ] 4.5 Create migration for `sharing_invites` table
  * [ ] 4.6 Implement granular permission controls UI (checkboxes for moods/notes/selfies)
  * [ ] 4.7 Create `app/Livewire/SharedWithMe.php` to view data others shared with user
  * [ ] 4.8 Create `resources/views/livewire/shared-with-me.blade.php`
  * [ ] 4.9 Implement privacy filters in `MoodEntry` queries (only show shared data)
  * [ ] 4.10 Add revoke access button with confirmation modal
  * [ ] 4.11 Create notification when new data is shared (Laravel Notifications)
  * [ ] 4.12 Add visual indicators showing what data is currently shared
  * [ ] 4.13 Write Feature test `tests/Feature/PrivateSharingTest.php`
  * [ ] 4.14 Ensure default is NO ACCESS (privacy-first approach)

* [ ] 5.0 Implement Social Features - Anonymous Group Mood Tracking

  * [ ] 5.1 Create `app/Livewire/GroupDashboard.php` for group mood view
  * [ ] 5.2 Create `resources/views/livewire/group-dashboard.blade.php`
  * [ ] 5.3 Implement aggregated mood calculation (average mood over time, anonymized)
  * [ ] 5.4 Create group mood trend chart (line chart showing group average)
  * [ ] 5.5 Create `app/Livewire/JoinGroup.php` for joining via invite code
  * [ ] 5.6 Create `resources/views/livewire/join-group.blade.php`
  * [ ] 5.7 Implement invite code validation and group joining logic
  * [ ] 5.8 Add "My Groups" section to dashboard showing all groups user belongs to
  * [ ] 5.9 Implement leave group functionality with confirmation
  * [ ] 5.10 Ensure individual data remains anonymous to other members
  * [ ] 5.11 Add member count display (but not member names for regular users)
  * [ ] 5.12 Create group activity indicator (% of members who logged mood today)
  * [ ] 5.13 Enhance Filament `GroupResource.php` to show member list (admin only)
  * [ ] 5.14 Add group creation wizard in Filament (name, description, invite code generation)
  * [ ] 5.15 Write Feature test `tests/Feature/GroupMoodTrackingTest.php`

* [ ] 6.0 Build Subscription & Payment System

  * [ ] 6.1 Install and configure Laravel Cashier for Stripe
  * [ ] 6.2 Run `composer require laravel/cashier` and publish migrations
  * [ ] 6.3 Update `User` model to use `Billable` trait
  * [ ] 6.4 Create Stripe products and prices in Stripe Dashboard (free/premium/enterprise)
  * [ ] 6.5 Create `config/karma.php` to store plan IDs and features
  * [ ] 6.6 Create `app/Livewire/SubscriptionPlans.php` for plan selection page
  * [ ] 6.7 Create `resources/views/livewire/subscription-plans.blade.php` with pricing cards
  * [ ] 6.8 Implement subscription checkout flow using Stripe Checkout
  * [ ] 6.9 Create `app/Livewire/BillingPortal.php` for managing subscription
  * [ ] 6.10 Create `resources/views/livewire/billing-portal.blade.php`
  * [ ] 6.11 Implement upgrade/downgrade/cancel subscription functionality
  * [ ] 6.12 Add webhook handler for Stripe events (payment success, failure, cancellation)
  * [ ] 6.13 Create `app/Http/Controllers/WebhookController.php` for Stripe webhooks
  * [ ] 6.14 Implement email notifications for payment confirmations and failures
  * [ ] 6.15 Add subscription status badges to user dashboard
  * [ ] 6.16 Implement feature gating based on subscription tier (middleware/policies)
  * [ ] 6.17 Enhance Filament `SubscriptionResource.php` with revenue reports
  * [ ] 6.18 Add Filament widget showing MRR (Monthly Recurring Revenue)
  * [ ] 6.19 Write Feature test `tests/Feature/SubscriptionTest.php`

* [ ] 7.0 Enhance Admin Dashboard (Filament)

  * [ ] 7.1 Create `app/Filament/Widgets/MoodStatisticsWidget.php` for system-wide mood analytics
  * [ ] 7.2 Display total users, active users (last 7 days), total mood entries
  * [ ] 7.3 Create `app/Filament/Widgets/UserActivityWidget.php` showing engagement trends
  * [ ] 7.4 Create `app/Filament/Widgets/RevenueWidget.php` for subscription revenue
  * [ ] 7.5 Add chart showing daily active users over last 30 days
  * [ ] 7.6 Enhance `UserResource.php` to show user activity logs and mood history
  * [ ] 7.7 Add manual calendar sync trigger button in `UserResource.php` (for support)
  * [ ] 7.8 Create `app/Filament/Pages/SystemSettings.php` for platform configuration
  * [ ] 7.9 Add settings for default reminder timing, mood slider ranges, feature toggles
  * [ ] 7.10 Store settings in database (create `settings` table or use Laravel Settings package)
  * [ ] 7.11 Add ability to send system-wide notifications from admin panel
  * [ ] 7.12 Create retention metrics widget (7-day, 30-day retention rates)
  * [ ] 7.13 Add failed payment alerts in admin dashboard
  * [ ] 7.14 Implement user search with filters (role, subscription status, activity)
  * [ ] 7.15 Add export functionality for user data (CSV) in admin panel

* [ ] 8.0 Build Emotional Selfies Feature (Web Upload Version)

  * [ ] 8.1 Create `app/Livewire/EmotionalSelfie.php` component for selfie upload
  * [ ] 8.2 Create `resources/views/livewire/emotional-selfie.blade.php`
  * [ ] 8.3 Implement file upload with validation (image types, max size 5MB)
  * [ ] 8.4 Store uploaded images using Laravel Storage (disk: 'public' for dev)
  * [ ] 8.5 Link uploaded selfie to current mood entry (mood_entry_id)
  * [ ] 8.6 Create `app/Services/ImageFilterService.php` for applying heatmap-style filters
  * [ ] 8.7 Implement basic heatmap overlay based on mood score (using GD or Intervention Image)
  * [ ] 8.8 Add color gradient logic (cool colors for low mood, warm for high mood)
  * [ ] 8.9 Create `app/Livewire/SelfieGallery.php` for viewing past emotional selfies
  * [ ] 8.10 Create `resources/views/livewire/selfie-gallery.blade.php` with grid layout
  * [ ] 8.11 Add selfie mode settings (random prompts vs scheduled time)
  * [ ] 8.12 Create `app/Jobs/SendSelfiePrompt.php` for scheduled/random selfie prompts
  * [ ] 8.13 Implement delete selfie functionality with confirmation
  * [ ] 8.14 Add privacy controls (selfies private by default, can share with trusted contacts)
  * [ ] 8.15 Write Feature test `tests/Feature/EmotionalSelfieTest.php`

* [ ] 9.0 Implement Adaptive UI & User Preferences

  * [ ] 9.1 Create `app/Services/AdaptiveUIService.php` for color calculation
  * [ ] 9.2 Implement color scheme calculation based on current/recent mood
  * [ ] 9.3 Add method to generate CSS variables for background gradients and accents
  * [ ] 9.4 Create Livewire component `app/Livewire/AdaptiveTheme.php` to inject dynamic styles
  * [ ] 9.5 Update `layouts/app.blade.php` to include adaptive theme component
  * [ ] 9.6 Implement subtle gradient backgrounds that change with mood
  * [ ] 9.7 Create `app/Livewire/UserPreferences.php` for managing preferences
  * [ ] 9.8 Create `resources/views/livewire/user-preferences.blade.php`
  * [ ] 9.9 Add toggle to enable/disable adaptive UI colors
  * [ ] 9.10 Add notification preferences (email, push, quiet hours)
  * [ ] 9.11 Add selfie mode preference (random/scheduled, with time picker)
  * [ ] 9.12 Ensure color changes are subtle and accessible (WCAG contrast ratios)
  * [ ] 9.13 Write Unit test `tests/Unit/AdaptiveUIServiceTest.php`

* [ ] 10.0 Build Data Export & Privacy Features (GDPR Compliance)

  * [ ] 10.1 Create `app/Livewire/DataExport.php` for data export interface
  * [ ] 10.2 Create `resources/views/livewire/data-export.blade.php`
  * [ ] 10.3 Implement CSV export for mood entries (with date range filter)
  * [ ] 10.4 Implement PDF export using `barryvdh/laravel-dompdf` (already installed)
  * [ ] 10.5 Create PDF template for mood summary report
  * [ ] 10.6 Include mood entries, calendar events, emotional selfies in export
  * [ ] 10.7 Create `app/Livewire/DataDeletion.php` for account deletion
  * [ ] 10.8 Create `resources/views/livewire/data-deletion.blade.php` with warnings
  * [ ] 10.9 Implement full data deletion (cascade delete mood entries, selfies, etc.)
  * [ ] 10.10 Add grace period before permanent deletion (soft delete for 30 days)
  * [ ] 10.11 Create privacy policy page (`resources/views/privacy-policy.blade.php`)
  * [ ] 10.12 Create terms of service page (`resources/views/terms-of-service.blade.php`)
  * [ ] 10.13 Add consent checkbox during registration for data processing
  * [ ] 10.14 Implement email notification when data export is ready
  * [ ] 10.15 Write Feature test `tests/Feature/DataExportTest.php`

* [ ] 11.0 Comprehensive Testing & Quality Assurance

  * [ ] 11.1 Set up test database configuration in `phpunit.xml`
  * [ ] 11.2 Create factory for `MoodEntry` model in `database/factories/MoodEntryFactory.php`
  * [ ] 11.3 Create factory for `CalendarEvent` model in `database/factories/CalendarEventFactory.php`
  * [ ] 11.4 Create factory for `Group` model in `database/factories/GroupFactory.php`
  * [ ] 11.5 Write authentication tests (Google OAuth, login, logout)
  * [ ] 11.6 Write authorization tests (ensure users can only access their own data)
  * [ ] 11.7 Write privacy tests (shared data only visible to authorized users)
  * [ ] 11.8 Write integration tests for calendar sync end-to-end flow
  * [ ] 11.9 Write tests for all Livewire components (mood entry, calendar, reports)
  * [ ] 11.10 Write tests for all service classes (Analytics, Google Calendar, Stripe)
  * [ ] 11.11 Add test coverage for edge cases (expired tokens, failed API calls, etc.)
  * [ ] 11.12 Run `php artisan test --coverage` and aim for >80% coverage
  * [ ] 11.13 Set up GitHub Actions or CI/CD for automated testing
  * [ ] 11.14 Create `tests/Feature/E2EUserJourneyTest.php` for complete user flow
  * [ ] 11.15 Document all test cases and coverage in `tests/README.md`
