# Groups API Documentation

Sistema de compartir mood tracking de forma **anónima** en grupos (familias, equipos, escuelas).

---

## 🔑 Autenticación

Todos los endpoints requieren token Bearer:
```
Authorization: Bearer {token}
```

---

## 📋 Endpoints

### 1. Unirse a un Grupo

**`POST /api/groups/join`**

Permite al usuario unirse a un grupo mediante código de invitación.

**Request Body:**
```json
{
  "invite_code": "ABC12XYZ"
}
```

**Validaciones:**
- `invite_code`: requerido, string de 8 caracteres

**Success Response (200):**
```json
{
  "success": true,
  "message": "Successfully joined the group!",
  "data": {
    "group": {
      "id": "uuid",
      "name": "Work Team",
      "description": "Our team mood tracker",
      "member_count": 12,
      "joined_at": "2025-10-20T10:30:00Z"
    }
  }
}
```

**Error Responses:**

- **404 - Código inválido:**
```json
{
  "success": false,
  "message": "Invalid invite code. Group not found."
}
```

- **403 - Grupo inactivo:**
```json
{
  "success": false,
  "message": "This group is no longer accepting new members."
}
```

- **409 - Ya miembro:**
```json
{
  "success": false,
  "message": "You are already a member of this group."
}
```

---

### 2. Salir de un Grupo

**`POST /api/groups/{groupId}/leave`**

Elimina al usuario del grupo.

**URL Params:**
- `groupId`: UUID del grupo

**Success Response (200):**
```json
{
  "success": true,
  "message": "Successfully left the group."
}
```

**Error Responses:**

- **404 - No miembro:**
```json
{
  "success": false,
  "message": "You are not a member of this group."
}
```

---

### 3. Mis Grupos

**`GET /api/groups/my-groups`**

Lista todos los grupos a los que pertenece el usuario con estadísticas rápidas.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "groups": [
      {
        "id": "uuid",
        "name": "Work Team",
        "description": "Our team mood tracker",
        "invite_code": "ABC12XYZ",
        "member_count": 12,
        "mood_today": 7.2,
        "activity_rate": 75,
        "joined_at": "2025-10-15T08:00:00Z"
      },
      {
        "id": "uuid2",
        "name": "Family",
        "description": "Family mood sharing",
        "invite_code": "FAM789QW",
        "member_count": 5,
        "mood_today": 8.1,
        "activity_rate": 100,
        "joined_at": "2025-10-10T12:00:00Z"
      }
    ],
    "total": 2
  }
}
```

**Campos:**
- `mood_today`: Promedio de mood del grupo hoy (0 si no hay datos)
- `activity_rate`: Porcentaje de miembros que loguearon mood hoy (0-100)

---

### 4. Dashboard de Grupo

**`GET /api/groups/{groupId}`**

Obtiene detalles básicos de un grupo.

**URL Params:**
- `groupId`: UUID del grupo

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "group": {
      "id": "uuid",
      "name": "Work Team",
      "description": "Our team mood tracker",
      "member_count": 12,
      "created_at": "2025-09-01T00:00:00Z"
    }
  }
}
```

**Error Responses:**

- **403 - No autorizado:**
```json
{
  "success": false,
  "message": "You are not a member of this group."
}
```

---

### 5. Estadísticas de Grupo

**`GET /api/groups/{groupId}/stats?period=7d`**

Obtiene estadísticas agregadas y anónimas del grupo.

**URL Params:**
- `groupId`: UUID del grupo

**Query Params:**
- `period`: `24h`, `7d`, `30d` (default: `7d`)

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "period": "7d",
    "member_count": 12,
    "average_mood": 7.2,
    "activity_today": {
      "members_logged": 9,
      "total_members": 12,
      "percentage": 75
    },
    "mood_trend": [
      {
        "date": "2025-10-14",
        "average_mood": 6.8
      },
      {
        "date": "2025-10-15",
        "average_mood": 7.1
      },
      {
        "date": "2025-10-16",
        "average_mood": 7.4
      }
    ],
    "mood_distribution": [
      { "mood_score": 1, "count": 2 },
      { "mood_score": 2, "count": 3 },
      { "mood_score": 3, "count": 5 },
      { "mood_score": 4, "count": 8 },
      { "mood_score": 5, "count": 12 },
      { "mood_score": 6, "count": 15 },
      { "mood_score": 7, "count": 20 },
      { "mood_score": 8, "count": 18 },
      { "mood_score": 9, "count": 10 },
      { "mood_score": 10, "count": 7 }
    ]
  }
}
```

**🔒 Privacidad: Grupos con < 3 miembros:**
```json
{
  "success": true,
  "data": {
    "privacy_notice": "Stats will be available when the group has at least 3 members.",
    "member_count": 2,
    "minimum_required": 3
  }
}
```

**Campos:**
- `average_mood`: Promedio del período seleccionado
- `mood_trend`: Arreglo de promedios diarios
- `mood_distribution`: Conteo de moods por score (1-10)

**Error Responses:**

- **403 - No autorizado:**
```json
{
  "success": false,
  "message": "You are not a member of this group."
}
```

---

## 📊 Compartir Mood con Grupos

Al crear un mood entry, puedes compartirlo con grupos:

**`POST /api/moods`**

```json
{
  "mood_score": 8,
  "note": "Great team meeting!",
  "group_ids": ["uuid1", "uuid2"]
}
```

**Response:**
```json
{
  "message": "Mood entry created successfully",
  "mood": { ... },
  "shared_with_groups": 2
}
```

**Validaciones:**
- Solo puedes compartir con grupos a los que perteneces
- Los group_ids inválidos serán ignorados

---

## 🔒 Consideraciones de Privacidad

1. **Anonimato Total**: Los miembros del grupo NO ven quién es quién
2. **Solo Agregados**: Solo se muestran promedios y totales
3. **Mínimo 3 Miembros**: Si hay < 3 miembros, no se muestran stats
4. **Opt-in por Mood**: Decides si compartir cada mood con grupos
5. **Sin Nombres**: Nunca se exponen nombres o datos individuales

---

## 📱 Flujo de Usuario

### Unirse a Grupo
1. Admin crea grupo en panel admin
2. Admin comparte código de invitación (ej: `ABC12XYZ`)
3. Usuario abre app → "Join Group" → ingresa código
4. Usuario ve grupo en "My Groups"

### Compartir Mood
1. Usuario crea mood entry
2. Selecciona grupos con los que compartir (checkboxes)
3. Mood se agrega a estadísticas del grupo (anónimo)

### Ver Stats de Grupo
1. Usuario abre grupo desde lista
2. Ve dashboard con:
   - Mood promedio hoy/7d/30d
   - Gráfico de tendencia
   - Actividad del grupo (%)
   - Distribución de moods

---

## 🚨 Errores Comunes

| Código | Mensaje | Solución |
|--------|---------|----------|
| 404 | Invalid invite code | Verificar código de 8 caracteres |
| 403 | Group inactive | Grupo fue desactivado por admin |
| 409 | Already a member | Ya perteneces a este grupo |
| 403 | Not a member | Necesitas unirte primero |

---

**Última Actualización**: Octubre 20, 2025
**Versión**: 1.0.0
