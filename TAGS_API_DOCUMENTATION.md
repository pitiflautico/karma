# Tags API Documentation

## Overview

The Tags API provides endpoints for managing mood entry tags. Users can access system-provided tags and create custom tags to categorize their mood entries.

## Base URL

```
https://your-domain.com/api
```

## Authentication

All endpoints require Bearer token authentication.

```http
Authorization: Bearer {user_token}
```

## Endpoints

### 1. Get Tags

Retrieves both system tags and user's custom tags. Optionally filter by mood score.

**Endpoint**: `GET /api/tags`

**Headers**:
```http
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters**:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `mood_score` | integer | No | Filter tags relevant to specific mood score (1-10) |

**Example Request (All tags)**:
```http
GET /api/tags
Authorization: Bearer {token}
```

**Example Request (Filtered by mood score)**:
```http
GET /api/tags?mood_score=8
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "system_tags": [
    {
      "id": 1,
      "name": "Work",
      "emoji": "💼",
      "category": "activity",
      "mood_associations": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
      "is_custom": false,
      "user_id": null,
      "created_at": "2025-10-19T10:00:00.000000Z",
      "updated_at": "2025-10-19T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Exercise",
      "emoji": "🏃",
      "category": "activity",
      "mood_associations": [6, 7, 8, 9, 10],
      "is_custom": false,
      "user_id": null,
      "created_at": "2025-10-19T10:00:00.000000Z",
      "updated_at": "2025-10-19T10:00:00.000000Z"
    }
  ],
  "custom_tags": [
    {
      "id": 42,
      "name": "Gaming",
      "emoji": "🎮",
      "category": "custom",
      "mood_associations": null,
      "is_custom": true,
      "user_id": "uuid",
      "created_at": "2025-10-19T11:30:00.000000Z",
      "updated_at": "2025-10-19T11:30:00.000000Z"
    }
  ]
}
```

**Error Response (500)**:
```json
{
  "message": "Failed to fetch tags",
  "error": "Error message here"
}
```

---

### 2. Create Custom Tag

Creates a new custom tag for the authenticated user.

**Endpoint**: `POST /api/tags`

**Headers**:
```http
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

**Request Body**:

| Field | Type | Required | Validation | Description |
|-------|------|----------|------------|-------------|
| `name` | string | Yes | max:50 | Tag name |
| `emoji` | string | No | max:10 | Emoji icon |
| `category` | string | No | max:50 | Tag category (defaults to 'custom') |

**Example Request**:
```json
{
  "name": "Gaming",
  "emoji": "🎮",
  "category": "hobby"
}
```

**Success Response (201)**:
```json
{
  "message": "Tag created successfully",
  "tag": {
    "id": 42,
    "name": "Gaming",
    "emoji": "🎮",
    "category": "hobby",
    "mood_associations": null,
    "is_custom": true,
    "user_id": "uuid",
    "created_at": "2025-10-19T11:30:00.000000Z",
    "updated_at": "2025-10-19T11:30:00.000000Z"
  }
}
```

**Error Response (422)**:
```json
{
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "emoji": ["The emoji must not be greater than 10 characters."]
  }
}
```

**Error Response (500)**:
```json
{
  "message": "Failed to create tag",
  "error": "Error message here"
}
```

---

## Data Models

### Tag Model

**Attributes**:

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `id` | Integer | No | Primary key |
| `name` | String | No | Tag name (max 50 chars) |
| `emoji` | String | Yes | Emoji icon (max 10 chars) |
| `category` | String | Yes | Tag category (max 50 chars) |
| `mood_associations` | JSON Array | Yes | Array of mood scores (1-10) this tag is relevant for |
| `is_custom` | Boolean | No | True if user-created, false if system tag |
| `user_id` | UUID | Yes | Owner user ID (null for system tags) |
| `created_at` | Timestamp | No | Creation time |
| `updated_at` | Timestamp | No | Last update time |

**Relationships**:

- `user` - BelongsTo User (only for custom tags)
- `moodEntries` - BelongsToMany MoodEntry (through `mood_entry_tag` pivot table)

**Scopes**:

- `system()` - Get only system tags (predefined)
- `customForUser($userId)` - Get user's custom tags
- `forMoodScore($score)` - Get tags relevant for specific mood score

---

## System Tag Categories

The system includes predefined tag categories:

### Activities
- 💼 Work
- 🏃 Exercise
- 📚 Study
- 🎨 Creative
- 🍳 Cooking
- 🧹 Chores

### Social
- 👥 Friends
- ❤️ Family
- 💑 Partner
- 🎉 Party
- 🏠 Alone

### Health
- 😴 Sleep
- 🍎 Healthy Eating
- 💊 Medication
- 🧘 Meditation
- 🌳 Nature

### Emotions
- 😊 Happy (associated with mood scores 8-10)
- 😢 Sad (associated with mood scores 1-3)
- 😰 Anxious (associated with mood scores 2-5)
- 😌 Calm (associated with mood scores 6-9)
- 😩 Stressed (associated with mood scores 1-4)

### Weather
- ☀️ Sunny
- ☁️ Cloudy
- 🌧️ Rainy
- ❄️ Cold
- 🔥 Hot

---

## Associating Tags with Mood Entries

Tags can be associated with mood entries through the mood entry API.

**Example**: Adding tags when creating a mood entry
```http
POST /api/moods
Content-Type: application/json

{
  "mood_score": 8,
  "note": "Great workout session!",
  "tag_ids": [2, 5, 42]  // Exercise, Sunny, Gaming (custom)
}
```

See [MOOD_API_DOCUMENTATION.md](./MOOD_API_DOCUMENTATION.md) for details.

---

## Mood Associations

System tags can have `mood_associations` that define which mood scores they're most relevant for:

- **General tags** (null associations): Available for all mood scores
- **Positive tags** ([6,7,8,9,10]): Shown primarily for higher mood scores
- **Negative tags** ([1,2,3,4,5]): Shown primarily for lower mood scores
- **Specific emotion tags**: Limited to very specific ranges (e.g., "Stressed" for [1,2,3,4])

When filtering by `mood_score`, the API returns:
1. All tags with that score in their `mood_associations`
2. All tags with `null` associations (available for all moods)

---

## Setup Requirements

### Database Setup

Run the tags migration and seeder:

```bash
# Run migrations
php artisan migrate

# Seed system tags
php artisan db:seed --class=TagSeeder
```

The `TagSeeder` creates all predefined system tags with appropriate categories and mood associations.

### API Routes

Ensure the following routes are registered in `routes/api.php`:

```php
// Tag routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tags', [TagApiController::class, 'index']);
    Route::post('/tags', [TagApiController::class, 'store']);
});
```

---

## Usage Examples

### Get all available tags
```bash
curl -X GET "https://your-domain.com/api/tags" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Get tags for happy mood (score 8)
```bash
curl -X GET "https://your-domain.com/api/tags?mood_score=8" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Create a custom tag
```bash
curl -X POST "https://your-domain.com/api/tags" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Gaming",
    "emoji": "🎮",
    "category": "hobby"
  }'
```

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created successfully |
| 401 | Unauthorized (missing or invalid token) |
| 422 | Validation error |
| 500 | Server error |

---

## Rate Limiting

API requests are rate-limited to prevent abuse:

- **Authenticated requests**: 60 requests per minute
- **Tag creation**: 10 requests per minute

---

## Best Practices

1. **Cache system tags**: System tags rarely change, consider caching them in your mobile app
2. **Debounce custom tag creation**: Prevent duplicate submissions
3. **Validate emojis**: Ensure emoji field contains valid Unicode emoji characters
4. **Limit custom tags**: Consider implementing a maximum number of custom tags per user
5. **Use mood associations**: Filter tags by mood score to show relevant suggestions

---

**Last Updated**: October 19, 2025
**API Version**: 1.0
**Contact**: development@feelith.com
