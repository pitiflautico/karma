# PRD: Karma – Emotional Tracking & Wellbeing Application

## 1. Introduction/Overview

**Karma** is an emotional wellbeing application that helps users track, visualize, and understand their emotions in real-time. Unlike traditional social networks, Karma creates a private, judgement-free space where users can capture their emotional state after life events, visualize patterns, and optionally share their journey with trusted individuals.

The core innovation is seamless **Google Calendar integration** that automatically prompts users to log their emotional state after scheduled events, creating a rich dataset that correlates activities with emotional wellbeing.

**Problem Statement:** People struggle to identify emotional patterns and understand what activities, events, or contexts affect their mental wellbeing. Traditional mood tracking apps require manual initiative and lack context about what the user was doing.

**Solution:** Karma automatically knows when events happen (via calendar integration) and proactively asks users how they felt, creating an effortless emotional tracking habit tied to real-life moments.

---

## 2. Goals

1. Enable users to effortlessly track their emotional state with minimal friction (2 taps)
2. Automatically correlate emotional data with life events via Google Calendar integration
3. Provide actionable insights showing which event types positively or negatively impact emotional wellbeing
4. Create a private, safe space for emotional expression without social pressure
5. Allow optional connection with trusted individuals through private sharing
6. Support group emotional awareness for teams, families, or schools through anonymous aggregated tracking
7. Build sustainable revenue through subscription/payment model

---

## 3. User Roles

### 3.1 Regular Users (Clients)
- Track their own emotional state
- View personal reports and insights
- Take emotional selfies
- Share data privately with trusted contacts
- Join group mood tracking spaces (created by admins)
- Manage their subscription/payment

### 3.2 Administrators
- Full user management (create, read, update, delete user accounts)
- Payment and subscription management
- Create and manage group mood tracking spaces
- View system-wide analytics (anonymized)
- Configure platform settings (default configurations, feature toggles)
- Access admin dashboard (web-based via Filament)

---

## 4. User Stories

### Core Mood Tracking
- **As a user**, I want to quickly log my mood after an event with a simple slider, so I can track how I felt without interrupting my day.
- **As a user**, I want to add an optional note to my mood entry, so I can capture context or thoughts about why I felt that way.
- **As a user**, I want to receive automatic reminders after calendar events end, so I don't forget to log my emotional state.

### Calendar Integration
- **As a user**, I want Karma to connect with my Google Calendar, so the app knows when my events happen without manual input.
- **As a user**, I want smart reminder timing that respects back-to-back meetings, so I'm not interrupted during busy periods.
- **As a user**, I want to see my mood entries organized by the calendar events they relate to, so I understand the context of my emotions.

### Emotional Selfies
- **As a user**, I want to take emotional selfies with heatmap filters, so I can visually express my inner state in a creative way.
- **As a user**, I want to choose between random prompts or scheduled selfie times, so the feature fits my preferences.
- **As a user**, I want the heatmap color to reflect my current mood entry, so the visual matches how I actually feel.

### Insights & Reports
- **As a user**, I want to see correlations between event types and my moods, so I can identify which activities make me feel better or worse.
- **As a user**, I want to view my emotional trends over time, so I can see if I'm improving or need to make changes.

### Social Features
- **As a user**, I want to share specific aspects of my emotional data with trusted people, so I can let loved ones understand how I'm doing.
- **As a user**, I want granular control over what I share (scores, notes, selfies), so I maintain my privacy boundaries.
- **As a user**, I want to join anonymous group mood tracking, so I can feel connected to my team/family without losing privacy.

### Admin Features
- **As an administrator**, I want to manage user accounts, so I can handle support requests and maintain platform integrity.
- **As an administrator**, I want to create and manage group spaces, so organizations can use Karma for collective wellbeing.
- **As an administrator**, I want to view aggregated analytics, so I can understand platform usage and improve the product.
- **As an administrator**, I want to manage payments and subscriptions, so the business model is sustainable.

---

## 5. Functional Requirements

### 5.1 Authentication & User Management

**FR-1.1** The system must support email/password authentication.

**FR-1.2** The system must support Google OAuth as an optional sign-in method.

**FR-1.3** Users must be able to connect their Google Calendar after initial registration (even if they didn't use Google OAuth).

**FR-1.4** The system must distinguish between two user roles: Regular Users and Administrators.

**FR-1.5** Administrators must have access to a web-based admin dashboard (Filament).

**FR-1.6** Administrators must be able to create, view, edit, and delete user accounts.

**FR-1.7** Users must be able to reset their password via email.

### 5.2 Mood Tracking (Core Feature)

**FR-2.1** Users must be able to log their mood using a slider scale (1-10 or similar numerical scale).

**FR-2.2** The mood logging interface must be accessible within 2 taps from the reminder notification.

**FR-2.3** Users must be able to optionally add a text note with each mood entry (character limit: 500).

**FR-2.4** Each mood entry must be timestamped.

**FR-2.5** Each mood entry must be linked to the calendar event that triggered it (if applicable).

**FR-2.6** Users must be able to manually log a mood entry at any time (not just after calendar events).

**FR-2.7** Users must be able to view a history of all their mood entries.

**FR-2.8** Users must be able to edit or delete their past mood entries.

### 5.3 Google Calendar Integration

**FR-3.1** The system must integrate with Google Calendar API to read user events.

**FR-3.2** The system must request only necessary calendar permissions (read events, not create/modify).

**FR-3.3** The system must detect when a calendar event ends.

**FR-3.4** The system must implement smart reminder timing that delays notifications when the user has back-to-back events.

**FR-3.5** The system must send a push notification after an event ends, prompting the user to log their mood.

**FR-3.6** Users must be able to configure which calendars to sync (if they have multiple).

**FR-3.7** Users must be able to set "quiet hours" when no reminders should be sent.

**FR-3.8** The system must handle cases where calendar sync fails gracefully (show user-friendly error message).

### 5.4 Emotional Selfies with Heatmap Filters

**FR-4.1** Users must be able to take an emotional selfie using their device camera.

**FR-4.2** Users must choose between two selfie prompt modes:
   - **Aleatory Mode:** Random prompts at unpredictable times
   - **Scheduled Mode:** Daily prompt at a specific time chosen by the user

**FR-4.3** The system must apply a heatmap-style filter to the selfie based on the user's current mood entry.

**FR-4.4** The heatmap filter color/intensity must correspond to the mood slider value (e.g., cool colors for low mood, warm colors for high mood).

**FR-4.5** Users must be able to retake the selfie if they're not satisfied.

**FR-4.6** Users must be able to save or discard the emotional selfie.

**FR-4.7** Emotional selfies must be stored securely and privately by default.

**FR-4.8** Users must be able to view a gallery of their past emotional selfies.

### 5.5 Reports & Insights

**FR-5.1** The system must analyze correlations between calendar event types and mood scores.

**FR-5.2** The system must display insights such as:
   - "You tend to feel better after [event type]"
   - "Your mood is typically lower on [day of week]"
   - "[Event type] events are often followed by [mood level] moods"

**FR-5.3** Users must be able to view their mood trends over custom time periods (week, month, quarter, year).

**FR-5.4** The system must generate weekly and monthly summary reports.

**FR-5.5** Reports must be visual (charts, graphs) and easy to understand for non-technical users.

**FR-5.6** Users must be able to filter reports by event type, date range, or mood level.

### 5.6 Private Sharing

**FR-6.1** Users must be able to invite trusted contacts to view parts of their emotional data.

**FR-6.2** Users must have granular control over what they share:
   - Mood scores only
   - Mood scores + written notes
   - Mood scores + emotional selfies
   - All data

**FR-6.3** Users must be able to revoke sharing access at any time.

**FR-6.4** Trusted contacts must receive a notification when new data is shared with them.

**FR-6.5** Trusted contacts must only see data that is explicitly shared (default: no access).

### 5.7 Anonymous Group Mood Tracking

**FR-7.1** Administrators must be able to create group mood tracking spaces.

**FR-7.2** Users must be able to join a group using a code or invite link.

**FR-7.3** Group members must see aggregated average mood of the group over time (anonymized).

**FR-7.4** Group members' individual data must remain anonymous to other members.

**FR-7.5** The system must display group mood trends in a shared dashboard.

**FR-7.6** Administrators must be able to view which users belong to a group (but regular members cannot).

**FR-7.7** Users must be able to leave a group at any time.

### 5.8 Adaptive UI

**FR-8.1** The app's primary UI color scheme must adapt to the user's current emotional state.

**FR-8.2** Color adaptation must be subtle and not distracting (e.g., background gradients, accent colors).

**FR-8.3** Users must be able to disable adaptive UI colors in settings if they prefer a static theme.

### 5.9 Platform Administration

**FR-9.1** Administrators must be able to view system-wide analytics including:
   - Total active users
   - Average mood scores (anonymized)
   - Feature usage statistics
   - Retention metrics

**FR-9.2** Administrators must be able to manage payments and subscriptions:
   - View all active subscriptions
   - Issue refunds
   - Handle failed payments
   - View revenue reports

**FR-9.3** Administrators must be able to configure platform settings:
   - Default reminder timing
   - Available mood slider ranges
   - Feature toggles (enable/disable features globally)

**FR-9.4** Administrators must have access to user support tools:
   - View user activity logs
   - Manually trigger calendar sync for troubleshooting
   - Send system notifications

### 5.10 Mobile App (React Native)

**FR-10.1** The mobile app must support iOS and Android platforms.

**FR-10.2** The mobile app must support push notifications.

**FR-10.3** The mobile app must handle offline mode gracefully (queue mood entries, sync when online).

**FR-10.4** The mobile app must request appropriate permissions (camera, notifications, calendar).

### 5.11 Web Viewer (Laravel + React)

**FR-11.1** The web application must provide a viewer for users to see their mood history and reports.

**FR-11.2** The web application must serve as the primary platform for the admin dashboard.

**FR-11.3** The web application must support responsive design (desktop, tablet).

**FR-11.4** Users must be able to view their emotional selfie gallery on the web.

**FR-11.5** Users must be able to export their data (CSV, PDF) from the web interface.

### 5.12 Payments & Subscriptions

**FR-12.1** The system must integrate with a payment provider (e.g., Stripe, PayPal).

**FR-12.2** The system must support subscription-based billing (monthly/yearly plans).

**FR-12.3** Users must be able to manage their subscription (upgrade, downgrade, cancel) from their account settings.

**FR-12.4** The system must handle free trial periods (if applicable).

**FR-12.5** Users must receive email notifications for payment confirmations, renewals, and failed payments.

---

## 6. Non-Goals (Out of Scope)

**NG-1** Public social feeds or discovery features (this is not a social network)

**NG-2** Likes, comments, or reactions on emotional entries

**NG-3** Integration with calendars other than Google Calendar (Apple Calendar, Outlook - future consideration)

**NG-4** AI-powered therapy recommendations or medical advice

**NG-5** Third-party integrations with fitness trackers or health apps (initial release)

**NG-6** Video-based emotional tracking

**NG-7** Multi-language support (initial release - English only)

**NG-8** Desktop native applications (Mac/Windows apps)

**NG-9** Real-time chat or messaging between users

**NG-10** Public user profiles or directories

---

## 7. Design Considerations

### 7.1 UI/UX Principles

- **Minimalism:** Clean, uncluttered interface with focus on ease of use
- **Emotional Design:** Use of color psychology to reflect emotional states
- **Privacy-First:** Visual indicators that data is private and secure
- **Accessibility:** Ensure readability, sufficient color contrast, and screen reader support

### 7.2 Key Screens (Mobile)

1. **Onboarding Flow**
   - Welcome screens explaining Karma's value proposition
   - Authentication (email/password or Google OAuth)
   - Google Calendar connection request
   - Permission requests (notifications, camera)

2. **Home Dashboard**
   - Current emotional state indicator
   - Quick mood log button (always accessible)
   - Today's calendar events with mood status (logged/not logged)
   - Weekly mood trend mini-graph

3. **Mood Logging Screen**
   - Large, easy-to-use slider
   - Optional note text field
   - Related calendar event displayed at top
   - Submit button

4. **Reports & Insights**
   - Visual charts (line graphs, correlation insights)
   - Filter options (time range, event type)
   - Key insights highlighted at top

5. **Emotional Selfie Camera**
   - Camera interface with real-time heatmap filter preview
   - Capture and retake options
   - Gallery access

6. **Settings**
   - Notification preferences
   - Calendar sync settings
   - Sharing management
   - Subscription/payment
   - Adaptive UI toggle

### 7.3 Key Screens (Web - Admin Dashboard)

1. **Admin Dashboard Home**
   - System-wide metrics and KPIs
   - Recent user activity
   - Quick actions

2. **User Management**
   - Searchable user list
   - User detail view with activity logs
   - CRUD operations

3. **Group Management**
   - List of all groups
   - Create new group form
   - Group analytics

4. **Payments & Subscriptions**
   - Revenue overview
   - Subscription list with status
   - Payment issue alerts

---

## 8. Technical Considerations

### 8.1 Backend (Laravel) - Existing Codebase

**Current Stack:**
- Laravel 12.0
- Filament 3.3 (Admin Panel)
- Laravel Passport (API Authentication)
- Spatie Laravel Permission (Role Management)
- MySQL/PostgreSQL Database

**Adaptations Needed:**
- Rename Organization model to match Karma context (or keep as tenant structure)
- Replace Client/Invoice models with Karma-specific models (MoodEntry, CalendarEvent, Group, etc.)
- Keep existing User and Role/Permission structure
- Add Google Calendar API integration
- Add push notification system (Laravel Notifications + FCM)
- Add image storage for emotional selfies
- Implement analytics and insights engine

### 8.2 Mobile (React Native)

- **Framework:** React Native (latest stable version)
- **State Management:** Redux or Context API
- **Navigation:** React Navigation
- **Push Notifications:** Firebase Cloud Messaging (FCM)
- **Calendar Integration:** Google Calendar API via OAuth
- **Camera:** react-native-camera or Expo Camera
- **Image Processing:** Apply heatmap filter (react-native-image-filter-kit)

### 8.3 Web Viewer

- **Frontend:** React.js (or continue with Livewire for simpler pages)
- **Admin:** Filament 3.3 (already in place)
- **Charts:** Filament Apex Charts (already installed: leandrocfe/filament-apex-charts)

### 8.4 Third-Party Integrations

- **Google Calendar API** (OAuth 2.0)
- **Payment Provider** (Stripe recommended for subscriptions)
- **Push Notification Service** (Firebase Cloud Messaging)
- **Email Service** (Configure Laravel Mail with SendGrid/Mailgun)
- **Image Storage** (AWS S3 or Laravel local storage for development)

### 8.5 Security & Privacy

- **Data Encryption:** Encrypt sensitive data at rest and in transit (HTTPS/TLS)
- **GDPR Compliance:** Ensure user data can be exported and deleted upon request
- **Privacy Policy:** Clear documentation of data usage
- **Secure Storage:** Emotional selfies must be stored with proper access controls

---

## 9. Success Metrics

### 9.1 User Engagement

- **Daily Active Users (DAU):** Target 60% of registered users logging at least one mood per day
- **Mood Logging Rate:** 80% of calendar event reminders result in a mood log
- **Retention:** 70% of users return after 7 days, 50% after 30 days

### 9.2 Feature Adoption

- **Calendar Integration:** 85% of users connect their Google Calendar within first week
- **Emotional Selfies:** 40% of users take at least one emotional selfie per week
- **Group Tracking:** Average group size of 5-20 members with 70% active participation

### 9.3 Business Metrics

- **Conversion Rate:** 25% of free trial users convert to paid subscribers
- **Churn Rate:** Monthly churn below 5%
- **Average Revenue Per User (ARPU):** Target based on pricing strategy

### 9.4 User Satisfaction

- **Net Promoter Score (NPS):** Target score of 50+
- **App Store Ratings:** Maintain 4.5+ stars on iOS and Android
- **Support Ticket Volume:** Less than 2% of users require support per month

---

## 10. Migration Strategy from Verifaccil to Karma

### 10.1 What to Keep
- ✅ User authentication system
- ✅ Role and permission structure (Spatie)
- ✅ Organization model (can be repurposed for group tracking)
- ✅ Filament admin panel setup
- ✅ API authentication (Laravel Passport)
- ✅ Email system configuration

### 10.2 What to Replace/Remove
- ❌ Client model → Not needed
- ❌ Invoice/InvoiceItem models → Not needed
- ❌ Setting model → Adapt for Karma settings
- ❌ All invoice-related migrations and seeders
- ❌ Client-related Filament resources

### 10.3 What to Add
- ➕ MoodEntry model (core feature)
- ➕ CalendarEvent model (Google Calendar sync)
- ➕ EmotionalSelfie model
- ➕ Group model (for group mood tracking)
- ➕ SharedAccess model (for private sharing)
- ➕ Subscription model (payment management)
- ➕ Google Calendar integration service
- ➕ Push notification system
- ➕ Analytics and insights engine

---

## 11. Open Questions

**Q1:** What specific pricing tiers will be offered (free tier, premium, enterprise)?

**Q2:** What is the maximum storage limit per user for emotional selfies?

**Q3:** Should the system support multiple languages in future iterations?

**Q4:** What happens to user data if they cancel their subscription? Grace period before deletion?

**Q5:** Should there be a daily limit on mood entries to prevent spam/gaming the system?

**Q6:** How should the system handle users with very large numbers of calendar events (100+ per day)?

**Q7:** Should administrators have the ability to anonymously view individual user mood data for research purposes (with explicit consent)?

**Q8:** What age restrictions should be in place? (e.g., 13+, 18+)

**Q9:** Should the app include any guided exercises or prompts for emotional reflection?

**Q10:** What is the minimum viable feature set for beta launch vs. full launch?

---

## 12. Next Steps

1. ✅ **PRD Created** - Document feature requirements
2. ⏭️ **Database Schema Design** - Design new tables for Karma
3. ⏭️ **Migration Strategy** - Clean up verifaccil code, create new migrations
4. ⏭️ **API Design** - Define REST API endpoints for mobile app
5. ⏭️ **Google Calendar Integration** - Set up OAuth and API connection
6. ⏭️ **Core Models** - Create MoodEntry, CalendarEvent, etc.
7. ⏭️ **Filament Resources** - Build admin interface for Karma
8. ⏭️ **React Native Setup** - Initialize mobile app project
9. ⏭️ **Beta Testing Plan** - Identify target user group

---

**Document Version:** 1.0
**Last Updated:** 2025-10-07
**Author:** Product Team
**Status:** Draft - Ready for Development
