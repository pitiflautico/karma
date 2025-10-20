# Personal Sharing API Documentation

Sistema de compartir mood tracking de forma **personal** con permisos granulares.

---

## 🔑 Autenticación

Todos los endpoints requieren token Bearer:
```
Authorization: Bearer {token}
```

---

## 📋 Endpoints

### **INVITACIONES**

---

### 1. Enviar Invitación

**`POST /api/sharing/invite`**

Invita a alguien por email para que vea tus moods.

**Request Body:**
```json
{
  "email": "friend@example.com",
  "can_view_moods": true,
  "can_view_notes": false,
  "can_view_selfies": false
}
```

**Validaciones:**
- `email`: requerido, email válido
- `can_view_moods`: boolean, default: true
- `can_view_notes`: boolean, default: false
- `can_view_selfies`: boolean, default: false

**Success Response (200):**
```json
{
  "success": true,
  "message": "Invitation sent successfully!",
  "data": {
    "invite": {
      "id": "uuid",
      "email": "friend@example.com",
      "can_view_moods": true,
      "can_view_notes": false,
      "can_view_selfies": false,
      "expires_at": "2025-10-27T10:00:00Z",
      "created_at": "2025-10-20T10:00:00Z"
    }
  }
}
```

**Error Responses:**

- **400 - Email propio:**
```json
{
  "success": false,
  "message": "You cannot share with yourself."
}
```

- **409 - Ya compartiendo:**
```json
{
  "success": false,
  "message": "You are already sharing with this user."
}
```

- **409 - Invitación pendiente:**
```json
{
  "success": false,
  "message": "There is already a pending invitation for this email."
}
```

---

### 2. Mis Invitaciones Enviadas

**`GET /api/sharing/my-invites`**

Lista de todas las invitaciones que envié.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "invites": [
      {
        "id": "uuid",
        "email": "friend@example.com",
        "status": "pending",
        "can_view_moods": true,
        "can_view_notes": false,
        "can_view_selfies": false,
        "expires_at": "2025-10-27T10:00:00Z",
        "created_at": "2025-10-20T10:00:00Z"
      },
      {
        "id": "uuid2",
        "email": "partner@example.com",
        "status": "accepted",
        "can_view_moods": true,
        "can_view_notes": true,
        "can_view_selfies": true,
        "expires_at": null,
        "created_at": "2025-10-15T08:00:00Z"
      }
    ],
    "total": 2
  }
}
```

**Estados posibles:**
- `pending`: Esperando respuesta
- `accepted`: Aceptada
- `rejected`: Rechazada
- `expired`: Expirada (> 7 días)

---

### 3. Invitaciones Recibidas

**`GET /api/sharing/invites-received`**

Invitaciones que recibí de otros usuarios.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "invites": [
      {
        "id": "uuid",
        "token": "random32chartoken",
        "owner": {
          "name": "John Doe",
          "email": "john@example.com"
        },
        "can_view_moods": true,
        "can_view_notes": true,
        "can_view_selfies": false,
        "expires_at": "2025-10-27T10:00:00Z",
        "created_at": "2025-10-20T10:00:00Z"
      }
    ],
    "total": 1
  }
}
```

**Nota:** Solo muestra invitaciones `pending` y no expiradas.

---

### 4. Aceptar Invitación

**`POST /api/sharing/accept/{token}`**

Acepta una invitación para ver los moods de alguien.

**URL Params:**
- `token`: Token único de la invitación (32 caracteres)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Invitation accepted! You can now view shared data."
}
```

**Error Responses:**

- **404 - Token inválido:**
```json
{
  "success": false,
  "message": "Invalid or expired invitation."
}
```

- **410 - Expirada:**
```json
{
  "success": false,
  "message": "This invitation has expired."
}
```

---

### 5. Rechazar Invitación

**`POST /api/sharing/reject/{token}`**

Rechaza una invitación.

**URL Params:**
- `token`: Token único de la invitación

**Success Response (200):**
```json
{
  "success": true,
  "message": "Invitation rejected."
}
```

---

### **GESTIÓN DE ACCESOS**

---

### 6. Con Quién Estoy Compartiendo

**`GET /api/sharing/sharing-with`**

Lista de personas con las que comparto mis moods.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "shares": [
      {
        "id": "uuid",
        "user": {
          "id": "user-uuid",
          "name": "Jane Smith",
          "email": "jane@example.com"
        },
        "permissions": {
          "can_view_moods": true,
          "can_view_notes": true,
          "can_view_selfies": false
        },
        "created_at": "2025-10-15T08:00:00Z"
      }
    ],
    "total": 1
  }
}
```

---

### 7. Quién Comparte Conmigo

**`GET /api/sharing/shared-with-me`**

Lista de personas que me permiten ver sus moods.

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "shares": [
      {
        "id": "uuid",
        "owner": {
          "id": "user-uuid",
          "name": "John Doe",
          "email": "john@example.com"
        },
        "permissions": {
          "can_view_moods": true,
          "can_view_notes": true,
          "can_view_selfies": false
        },
        "latest_mood": {
          "mood_score": 8,
          "notes": "Feeling great today!",
          "created_at": "2025-10-20T09:30:00Z"
        },
        "created_at": "2025-10-15T08:00:00Z"
      }
    ],
    "total": 1
  }
}
```

**Nota:** `latest_mood` solo aparece si tienes permiso `can_view_moods`.

---

### 8. Revocar Acceso

**`DELETE /api/sharing/revoke/{shareId}`**

Deja de compartir con alguien.

**URL Params:**
- `shareId`: UUID del acceso compartido

**Success Response (200):**
```json
{
  "success": true,
  "message": "Access revoked successfully."
}
```

**Error Responses:**

- **404 - No encontrado:**
```json
{
  "success": false,
  "message": "Shared access not found."
}
```

---

### 9. Actualizar Permisos

**`PUT /api/sharing/{shareId}/permissions`**

Modifica los permisos de alguien con quien compartes.

**URL Params:**
- `shareId`: UUID del acceso compartido

**Request Body:**
```json
{
  "can_view_moods": true,
  "can_view_notes": true,
  "can_view_selfies": false
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Permissions updated successfully.",
  "data": {
    "permissions": {
      "can_view_moods": true,
      "can_view_notes": true,
      "can_view_selfies": false
    }
  }
}
```

---

### **VER DATOS COMPARTIDOS**

---

### 10. Ver Moods de Alguien

**`GET /api/sharing/moods/{ownerId}?limit=50&start_date=2025-10-01&end_date=2025-10-20`**

Obtiene los moods de alguien que comparte contigo.

**URL Params:**
- `ownerId`: UUID del usuario que comparte

**Query Params (opcionales):**
- `limit`: Número máximo de moods (default: 50)
- `start_date`: Fecha inicio (ISO 8601)
- `end_date`: Fecha fin (ISO 8601)

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "moods": [
      {
        "id": "uuid",
        "mood_score": 8,
        "notes": "Great day at work!",
        "selfie_url": "https://example.com/storage/selfies/abc.jpg",
        "created_at": "2025-10-20T10:00:00Z"
      },
      {
        "id": "uuid2",
        "mood_score": 6,
        "created_at": "2025-10-19T15:30:00Z"
      }
    ],
    "total": 2,
    "permissions": {
      "can_view_moods": true,
      "can_view_notes": true,
      "can_view_selfies": true
    }
  }
}
```

**Nota:** Los campos `notes` y `selfie_url` solo aparecen si tienes los permisos correspondientes.

**Error Responses:**

- **403 - Sin acceso:**
```json
{
  "success": false,
  "message": "You do not have access to this user's data."
}
```

- **403 - Sin permiso de moods:**
```json
{
  "success": false,
  "message": "You do not have permission to view moods."
}
```

---

## 🔐 Permisos Granulares

Cada compartición tiene 3 permisos independientes:

| Permiso | Descripción | Datos Visibles |
|---------|-------------|----------------|
| `can_view_moods` | Ver mood scores | Mood score (1-10), fecha/hora |
| `can_view_notes` | Ver notas de texto | Texto del mood entry |
| `can_view_selfies` | Ver selfies | URL de la foto |

**Ejemplo de Combinaciones:**

- **Solo Moods**: `{moods: true, notes: false, selfies: false}`
  - Ve scores pero no contexto

- **Moods + Notas**: `{moods: true, notes: true, selfies: false}`
  - Ve scores y explicaciones textuales

- **Todo**: `{moods: true, notes: true, selfies: true}`
  - Ve todo el contexto completo

---

## 📱 Flujo de Usuario

### Invitar a Alguien
1. Usuario A → "Sharing Settings" → "Invite Someone"
2. Ingresa email de Usuario B
3. Selecciona permisos (checkboxes)
4. Usuario B recibe notificación push
5. Usuario B acepta/rechaza desde app

### Ver Moods de Alguien
1. Usuario B → "Shared With Me"
2. Ve lista de personas que comparten
3. Tap en persona → Timeline de moods
4. Ve solo datos permitidos

### Revocar Acceso
1. Usuario A → "Sharing Settings" → "Sharing With"
2. Encuentra a Usuario B
3. Tap → "Stop Sharing"
4. Usuario B pierde acceso inmediatamente

---

## 🔒 Consideraciones de Seguridad

1. **Tokens Únicos**: Cada invitación tiene un token de 32 caracteres
2. **Expiración**: Invitaciones expiran a los 7 días
3. **Verificación de Email**: Solo puedes aceptar invitaciones a tu email registrado
4. **Revocable**: El dueño puede revocar acceso en cualquier momento
5. **Auditoría**: Todas las acciones se registran con timestamps

---

## 🚨 Errores Comunes

| Código | Mensaje | Solución |
|--------|---------|----------|
| 400 | Cannot share with yourself | Usa otro email |
| 409 | Already sharing | Ya existe acceso activo |
| 404 | Invalid token | Token incorrecto o expirado |
| 403 | No access | Necesitas aceptar invitación |
| 403 | No permission | Solicita permisos al dueño |

---

## 🔔 Notificaciones Push

El sistema envía notificaciones en estos casos:

1. **Invitación Recibida**: Cuando alguien te invita
2. **Invitación Aceptada**: Cuando aceptan tu invitación
3. **Permisos Actualizados**: Cuando cambian tus permisos
4. **Acceso Revocado**: Cuando alguien deja de compartir

---

**Última Actualización**: Octubre 20, 2025
**Versión**: 1.0.0
