# Groups & Shared Events System Documentation

## Overview
Sistema completo de grupos con eventos compartidos, valoraciones de mood grupales, y notificaciones resumidas.

## Database Structure

### Tables

#### `groups`
- `id`: Primary key
- `name`: Nombre del grupo (ej: "Familia", "Amigos")
- `slug`: URL-friendly slug
- `description`: Descripción del grupo
- `avatar`: Imagen del grupo (opcional)
- `color`: Color identificador (#8B5CF6 default)
- `created_by`: User ID del creador
- `invite_code`: Código único para unirse
- `is_active`: Boolean
- `created_at`, `updated_at`

#### `group_members`
- `id`: Primary key
- `group_id`: Foreign key a groups
- `user_id`: Foreign key a users
- `role`: enum('admin', 'member')
  - **admin**: Puede eliminar miembros, editar grupo
  - **member**: Puede invitar otros miembros
- `joined_at`: Timestamp de unión
- `created_at`, `updated_at`
- **Unique**: (group_id, user_id)

#### `group_events`
- `id`: Primary key
- `group_id`: Foreign key a groups
- `calendar_event_id`: Foreign key a calendar_events (nullable)
- `title`: Nombre del evento
- `description`: Descripción del evento
- `event_date`: Fecha y hora del evento
- `created_by`: User ID del creador
- `is_custom`: Boolean
  - `true`: Evento creado manualmente
  - `false`: Evento importado de Google Calendar
- `created_at`, `updated_at`

#### `group_event_moods`
- `id`: Primary key
- `group_event_id`: Foreign key a group_events
- `user_id`: Foreign key a users
- `mood_score`: Integer 1-10
- `mood_icon`: String (ej: "Great_icon.svg")
- `mood_name`: String (ej: "Happy")
- `note`: Text (opcional)
- `created_at`, `updated_at`
- **Unique**: (group_event_id, user_id) - Un usuario solo valora una vez por evento

## Features

### 1. Group Management
- ✅ Crear grupos
- ✅ Añadir miembros (todos los miembros pueden invitar)
- ✅ Eliminar miembros (solo admins)
- ✅ Ver estadísticas del grupo
- ✅ Mood promedio del grupo

### 2. Shared Events
Eventos que se comparten entre miembros del grupo:

**Tipos de eventos:**
1. **From Google Calendar**: Si un evento tiene asistentes que están en el grupo
2. **Custom Events**: Creados manualmente por cualquier miembro

**Workflow:**
1. Miembro crea evento (o se importa de Calendar)
2. Todos los miembros del grupo pueden ver el evento
3. Después del evento, cada miembro valora su experiencia
4. Sistema calcula mood promedio del grupo

### 3. Group Mood Rating
Cada miembro valora un evento con:
- Mood score (1-10)
- Mood icon y nombre
- Nota opcional

**Cálculo del Group Mood:**
```php
Average = Sum(all ratings) / Count(ratings)

Example:
- User 1: 9/10 (Happy)
- User 2: 8/10 (Happy)  
- User 3: 7/10 (Neutral)
Group Mood: 8.0/10 (Happy)
```

### 4. Notifications System
Resumen periódico de moods compartidos:

**Configuración en Settings:**
- Off
- Once per day (9:00 AM)
- Twice per day (9:00 AM & 6:00 PM)
- Real-time

**Email Content:**
```
📬 Your Daily Mood Summary

3 people shared their moods today:

👤 John Doe
   🙂 Happy - "Great day!"
   
👤 Mom
   😊 Overjoyed - "Best day ever!"

[View in App →]
```

## User Permissions

### Group Roles

**Admin:**
- Todo lo que puede hacer un member
- Eliminar miembros
- Eliminar el grupo
- Editar información del grupo

**Member:**
- Ver eventos del grupo
- Crear eventos custom
- Valorar eventos
- Invitar nuevos miembros

## API Endpoints (To Implement)

### Groups
- `GET /api/groups` - List user's groups
- `POST /api/groups` - Create group
- `GET /api/groups/{id}` - Get group details
- `PUT /api/groups/{id}` - Update group
- `DELETE /api/groups/{id}` - Delete group (admin only)

### Group Members
- `GET /api/groups/{id}/members` - List members
- `POST /api/groups/{id}/members` - Add member
- `DELETE /api/groups/{id}/members/{userId}` - Remove member (admin only)

### Group Events
- `GET /api/groups/{id}/events` - List group events
- `POST /api/groups/{id}/events` - Create custom event
- `GET /api/group-events/{id}` - Get event details
- `PUT /api/group-events/{id}` - Update event
- `DELETE /api/group-events/{id}` - Delete event

### Event Ratings
- `POST /api/group-events/{id}/rate` - Rate event
- `GET /api/group-events/{id}/ratings` - Get all ratings
- `GET /api/group-events/{id}/average` - Get average mood

## UI Components (To Implement)

### Views Needed:
1. **Groups List** (`/groups`)
   - List all groups user belongs to
   - Create new group button
   - Group mood average display

2. **Group Detail** (`/groups/{id}`)
   - Members list
   - Upcoming events
   - Recent events with ratings
   - Group stats

3. **Create/Edit Group** (`/groups/create`, `/groups/{id}/edit`)
   - Name, description
   - Avatar upload
   - Color picker
   - Invite members

4. **Group Events List** (`/groups/{id}/events`)
   - Upcoming events
   - Past events with ratings
   - Filter: All / Calendar / Custom

5. **Create Event** (`/groups/{id}/events/create`)
   - Title, description
   - Date & time picker
   - Link to group

6. **Rate Event** (`/group-events/{id}/rate`)
   - Mood picker (1-10)
   - Note textarea
   - Submit rating

7. **Event Detail** (`/group-events/{id}`)
   - Event info
   - All members' ratings
   - Group mood average
   - Comments/notes

## Integration Points

### Calendar Events Detection
When syncing Google Calendar:
```php
// Check if event attendees are in any of user's groups
$event->attendees; // Get from Google
$userGroups = Auth::user()->groups;

foreach ($userGroups as $group) {
    $groupEmails = $group->members->pluck('email');
    $attendeeEmails = collect($event->attendees)->pluck('email');
    
    if ($groupEmails->intersect($attendeeEmails)->count() > 1) {
        // Create group_event linked to calendar_event
        GroupEvent::create([
            'group_id' => $group->id,
            'calendar_event_id' => $event->id,
            'is_custom' => false,
            ...
        ]);
    }
}
```

### Notification Scheduling
Using Laravel Scheduler:
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Daily summary at 9 AM
    $schedule->command('groups:send-summary')
        ->dailyAt('09:00');
    
    // Evening summary at 6 PM  
    $schedule->command('groups:send-summary')
        ->dailyAt('18:00');
}
```

## Current Status

### ✅ Completed
- Database migrations
- Table structure
- Relationships defined
- Menu navigation updated

### 🚧 In Progress
- Models creation
- CRUD views
- Event rating system

### 📋 Todo
- API endpoints
- Calendar integration
- Notification system
- Group mood calculations
- UI components

## Next Steps

1. Create Eloquent Models (Group, GroupMember, GroupEvent, GroupEventMood)
2. Create Livewire components for CRUD
3. Implement event rating UI
4. Add calendar events detection
5. Setup notification scheduler
6. Create email templates
7. Add to mobile navigation

---

Last Updated: 2025-10-20
