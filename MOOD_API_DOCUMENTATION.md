# Mood Entry API Documentation

## Overview

The Mood API provides endpoints for creating, reading, updating, and deleting mood entries. It supports both manual mood entries and AI-powered selfie mood entries with facial expression analysis.

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

### 1. Create Mood Entry

Creates a new mood entry for the authenticated user.

**Endpoint**: `POST /api/moods`

**Headers**:
```http
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

**Request Body**:

| Field | Type | Required | Validation | Description |
|-------|------|----------|------------|-------------|
| `mood_score` | integer | Yes | 1-10 | User's mood score |
| `note` | string | No | max:500 | Text note or description |
| `calendar_event_id` | uuid | No | exists:calendar_events | Associated calendar event |
| `entry_type` | string | No | in:manual,selfie | Type of entry |
| `face_expression` | string | No | max:50 | Detected expression |
| `face_expression_confidence` | decimal | No | 0.0-1.0 | Confidence score |
| `face_energy_level` | string | No | max:20 | Energy level (low/medium/high) |
| `face_eyes_openness` | decimal | No | 0.0-1.0 | Eyes openness score |
| `face_social_context` | string | No | max:20 | Social context |
| `face_total_faces` | integer | No | min:0 | Number of faces detected |
| `bpm` | integer | No | 30-220 | Heart rate |
| `environment_brightness` | string | No | max:20 | Environment brightness |
| `face_analysis_raw` | object | No | - | Raw ML Kit data (JSON) |

**Example Request (Manual Entry)**:
```json
{
  "mood_score": 7,
  "note": "Feeling productive today!",
  "calendar_event_id": null,
  "entry_type": "manual"
}
```

**Example Request (Selfie Entry)**:
```json
{
  "mood_score": 8,
  "note": "Happy after morning workout",
  "calendar_event_id": null,
  "entry_type": "selfie",
  "face_expression": "happy",
  "face_expression_confidence": 0.87,
  "face_energy_level": "high",
  "face_eyes_openness": 0.92,
  "face_social_context": "alone",
  "face_total_faces": 1,
  "environment_brightness": "pleasant",
  "bpm": 72
}
```

**Success Response (201)**:
```json
{
  "message": "Mood entry created successfully",
  "mood": {
    "id": "uuid",
    "user_id": "uuid",
    "mood_score": 8,
    "note": "Happy after morning workout",
    "calendar_event_id": null,
    "entry_type": "selfie",
    "face_expression": "happy",
    "face_expression_confidence": 0.87,
    "face_energy_level": "high",
    "face_eyes_openness": 0.92,
    "face_social_context": "alone",
    "face_total_faces": 1,
    "bpm": 72,
    "environment_brightness": "pleasant",
    "face_analysis_raw": null,
    "created_at": "2025-10-19T10:30:00.000000Z",
    "updated_at": "2025-10-19T10:30:00.000000Z",
    "mood_name": "Happy",
    "mood_icon": "Happy_icon.svg",
    "mood_color": "#FBBF24"
  }
}
```

**Error Response (422)**:
```json
{
  "message": "Validation failed",
  "errors": {
    "mood_score": ["The mood score field is required."],
    "bpm": ["The bpm must be between 30 and 220."]
  }
}
```

---

### 2. Get Mood Entries

Retrieves the authenticated user's mood entries with pagination.

**Endpoint**: `GET /api/moods`

**Query Parameters**:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 20 | Items per page |

**Example Request**:
```http
GET /api/moods?page=1
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "moods": {
    "current_page": 1,
    "data": [
      {
        "id": "uuid",
        "user_id": "uuid",
        "mood_score": 8,
        "note": "Great day",
        "entry_type": "selfie",
        "face_expression": "happy",
        "face_energy_level": "high",
        "environment_brightness": "pleasant",
        "created_at": "2025-10-19T10:30:00.000000Z",
        "mood_name": "Happy",
        "mood_icon": "Happy_icon.svg",
        "calendar_event": null
      }
    ],
    "first_page_url": "https://your-domain.com/api/moods?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "https://your-domain.com/api/moods?page=5",
    "next_page_url": "https://your-domain.com/api/moods?page=2",
    "path": "https://your-domain.com/api/moods",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 95
  }
}
```

---

### 3. Get Single Mood Entry

Retrieves a specific mood entry by ID.

**Endpoint**: `GET /api/moods/{id}`

**Example Request**:
```http
GET /api/moods/uuid-here
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "mood": {
    "id": "uuid",
    "user_id": "uuid",
    "mood_score": 7,
    "note": "Feeling good",
    "entry_type": "manual",
    "created_at": "2025-10-19T10:30:00.000000Z",
    "mood_name": "Happy",
    "mood_icon": "Happy_icon.svg",
    "calendar_event": {
      "id": "uuid",
      "title": "Team Meeting",
      "start_time": "2025-10-19T14:00:00.000000Z"
    }
  }
}
```

**Error Response (404)**:
```json
{
  "message": "Mood entry not found"
}
```

---

### 4. Update Mood Entry

Updates an existing mood entry.

**Endpoint**: `PUT /api/moods/{id}`

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `mood_score` | integer | No | Updated mood score (1-10) |
| `note` | string | No | Updated note (max:500) |
| `calendar_event_id` | uuid | No | Updated calendar event association |

**Example Request**:
```json
{
  "mood_score": 9,
  "note": "Actually feeling amazing now!"
}
```

**Success Response (200)**:
```json
{
  "message": "Mood entry updated successfully",
  "mood": {
    "id": "uuid",
    "mood_score": 9,
    "note": "Actually feeling amazing now!",
    ...
  }
}
```

---

### 5. Delete Mood Entry

Deletes a mood entry.

**Endpoint**: `DELETE /api/moods/{id}`

**Example Request**:
```http
DELETE /api/moods/uuid-here
Authorization: Bearer {token}
```

**Success Response (200)**:
```json
{
  "message": "Mood entry deleted successfully"
}
```

**Error Response (500)**:
```json
{
  "message": "Failed to delete mood entry",
  "error": "Error message here"
}
```

---

## Data Models

### MoodEntry Model

**Attributes**:

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| `id` | UUID | No | Primary key |
| `user_id` | UUID | No | Foreign key to users |
| `calendar_event_id` | UUID | Yes | Foreign key to calendar_events |
| `group_id` | UUID | Yes | Foreign key to groups |
| `mood_score` | Integer | No | Mood score (1-10) |
| `note` | Text | Yes | User's note |
| `is_manual` | Boolean | No | Manual entry flag (deprecated) |
| `entry_type` | String | Yes | 'manual' or 'selfie' |
| `selfie_photo_path` | String | Yes | Path to selfie photo |
| `selfie_heatmap_path` | String | Yes | Path to heatmap image |
| `selfie_taken_at` | Timestamp | Yes | Selfie capture time |
| `face_expression` | String | Yes | Expression category |
| `face_expression_confidence` | Decimal(5,4) | Yes | Confidence (0-1) |
| `face_energy_level` | String | Yes | 'low', 'medium', 'high' |
| `face_eyes_openness` | Decimal(5,4) | Yes | Eyes openness (0-1) |
| `face_social_context` | String | Yes | Social context |
| `face_total_faces` | Integer | Yes | Number of faces |
| `bpm` | Integer | Yes | Heart rate |
| `environment_brightness` | String | Yes | Environment category |
| `face_analysis_raw` | JSON | Yes | Raw analysis data |
| `created_at` | Timestamp | No | Creation time |
| `updated_at` | Timestamp | No | Last update time |

**Computed Attributes**:

- `mood_name` - Human-readable mood name based on score
- `mood_icon` - SVG icon filename based on score
- `mood_color` - Hex color based on score
- `mood_category` - Category: low/medium/good/excellent

**Relationships**:

- `user` - BelongsTo User
- `calendarEvent` - BelongsTo CalendarEvent
- `group` - BelongsTo Group

---

## Face Expression Categories

The `face_expression` field can contain the following values:

| Expression | Emoji | Mood Score | Description |
|-----------|-------|------------|-------------|
| `very_happy` | 😄 | 10 | Very happy, big smile |
| `happy` | 😊 | 9 | Happy, genuine smile |
| `content` | 🙂 | 7 | Content, pleasant |
| `slight_smile` | 😌 | 6 | Slight smile, calm |
| `neutral` | 😐 | 5 | Neutral or serious |
| `tired` | 😪 | 3 | Tired, low energy |
| `very_tired` | 😴 | 2 | Very tired, eyes closing |
| `sad` | 😢 | 1 | Sad, head down |

---

## Energy Levels

The `face_energy_level` field values:

- `high` ⚡ - Alert, eyes wide open (>= 0.75)
- `medium` 🔋 - Normal alertness (0.4-0.75)
- `low` 🪫 - Tired, eyes closing (<= 0.4)

---

## Environment Brightness

The `environment_brightness` field values:

- `pleasant` ☀️ - Bright, well-lit (>= 0.60)
- `neutral` 🌤️ - Normal lighting (0.35-0.60)
- `dim` 🌙 - Low light (0.15-0.35)
- `dark` 🌑 - Very dark (< 0.15)

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created successfully |
| 401 | Unauthorized (missing or invalid token) |
| 404 | Resource not found |
| 422 | Validation error |
| 500 | Server error |

---

## Rate Limiting

API requests are rate-limited to prevent abuse:

- **Authenticated requests**: 60 requests per minute
- **Mood creation**: 30 requests per minute

---

## Changelog

### Version 1.1 (2025-10-19)

- Added `entry_type` field to distinguish manual vs selfie entries
- Added face analysis fields (expression, energy, environment)
- Added selfie photo storage fields (for future use)
- Deprecated `is_manual` field in favor of `entry_type`

### Version 1.0

- Initial mood entry API
- Basic CRUD operations
- Calendar event integration

---

**Last Updated**: October 19, 2025
**API Version**: 1.1
**Contact**: development@feelith.com
