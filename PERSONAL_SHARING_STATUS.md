# Personal Sharing System - Estado Actual y Tareas Pendientes

## 📋 Resumen
Sistema de compartir moods de forma **personal y selectiva** con personas específicas. A diferencia de los grupos anónimos, aquí compartes TUS datos individuales con personas que TÚ eliges (pareja, terapeuta, amigo, familiar).

---

## 🔄 Diferencia con Grupos

| Feature | **Personal Sharing** 👥 | **Grupos Anónimos** 🏢 |
|---------|----------------------|---------------------|
| **Privacidad** | Personal - ven tu nombre y tus moods | Anónimo - solo promedios agregados |
| **Control** | Decides qué compartir (moods, notas, selfies) | Compartes automáticamente todo al grupo |
| **Propósito** | Relaciones cercanas, accountability | Team building, collective awareness |
| **Ejemplo** | "Mi terapeuta ve mis moods y notas" | "Mi equipo ve mood promedio de 7.2" |

---

## ✅ Lo que YA está HECHO

### 1. **Base de Datos** ✅

#### Tabla: `sharing_invites`
Invitaciones pendientes/aceptadas/rechazadas

```sql
- id
- sender_id (UUID) - Quien envía la invitación
- recipient_email - Email del destinatario
- token (unique) - Token único para aceptar
- status (pending|accepted|rejected|expired)
- can_view_moods (boolean) - Permiso para ver moods
- can_view_notes (boolean) - Permiso para ver notas
- can_view_selfies (boolean) - Permiso para ver selfies
- expires_at - Fecha de expiración (7 días)
- accepted_at - Cuándo fue aceptada
- timestamps
```

#### Tabla: `shared_access`
Relaciones de compartir activas

```sql
- id (UUID)
- owner_id (UUID) - Dueño de los datos
- shared_with_user_id (UUID) - Usuario con quien se comparte
- can_view_moods (boolean)
- can_view_notes (boolean)
- can_view_selfies (boolean)
- timestamps
- UNIQUE(owner_id, shared_with_user_id)
```

### 2. **Modelos** ✅

- [x] **SharingInvite** (`app/Models/SharingInvite.php`)
  - Método `generateToken()` para crear tokens únicos
  - Relaciones con `sender` (User)

- [x] **SharedAccess** (`app/Models/SharedAccess.php`)
  - Relación `owner()` - Dueño de los datos
  - Relación `sharedWithUser()` - Usuario con acceso
  - Permisos granulares

### 3. **Vistas Web (Livewire)** ✅

- [x] **SharingSettings** (`app/Livewire/SharingSettings.php`)
  - Enviar invitaciones por email
  - Seleccionar permisos (moods, notas, selfies)
  - Ver lista de personas con quienes compartes
  - Editar permisos de accesos existentes
  - Revocar accesos

- [x] **SharedWithMe** (`app/Livewire/SharedWithMe.php`)
  - Ver lista de personas que comparten contigo
  - Ver sus moods (si tienes permiso)
  - Interfaz para dejar de recibir datos

- [x] **AcceptInvite** (componente para aceptar invitaciones)
  - Aceptar/rechazar invitaciones
  - Crear `SharedAccess` al aceptar

### 4. **Notificaciones** ✅

- [x] **SharingInviteNotification**
  - Email enviado al destinatario
  - Contiene link con token para aceptar
  - Indica qué permisos se otorgarán

### 5. **Rutas Web** ✅

```php
Route::get('/sharing-settings', SharingSettings::class)
Route::get('/shared-with-me', SharedWithMe::class)
Route::get('/accept-invite/{token}', AcceptInvite::class)
```

---

## ❌ Lo que FALTA por HACER

### 1. **API Endpoints** 🔴 PRIORIDAD ALTA

Necesitamos crear endpoints en `routes/api.php`:

```php
Route::prefix('sharing')->middleware('auth:api')->group(function () {
    // Enviar invitación
    Route::post('/invite', [SharingController::class, 'sendInvite']);

    // Mis invitaciones enviadas (pending/accepted/rejected)
    Route::get('/my-invites', [SharingController::class, 'myInvites']);

    // Invitaciones recibidas
    Route::get('/invites-received', [SharingController::class, 'receivedInvites']);

    // Aceptar invitación
    Route::post('/accept/{token}', [SharingController::class, 'acceptInvite']);

    // Rechazar invitación
    Route::post('/reject/{token}', [SharingController::class, 'rejectInvite']);

    // Personas con quienes comparto (yo soy owner)
    Route::get('/sharing-with', [SharingController::class, 'sharingWith']);

    // Personas que comparten conmigo (soy shared_with_user)
    Route::get('/shared-with-me', [SharingController::class, 'sharedWithMe']);

    // Revocar acceso (eliminar shared_access)
    Route::delete('/revoke/{sharedAccessId}', [SharingController::class, 'revokeAccess']);

    // Editar permisos de un shared_access
    Route::put('/{sharedAccessId}/permissions', [SharingController::class, 'updatePermissions']);

    // Obtener moods de alguien que comparte conmigo
    Route::get('/moods/{ownerId}', [SharingController::class, 'getSharedMoods']);
});
```

**Tareas:**
- [ ] Crear `app/Http/Controllers/Api/SharingController.php`
- [ ] Implementar todos los métodos
- [ ] Validaciones (que el usuario tenga permisos)
- [ ] Respuestas JSON con formato consistente

---

### 2. **Pantallas Móviles** 🔴 PRIORIDAD ALTA

#### Pantalla 1: **Sharing Settings (Gestión de Compartir)**
**Ubicación:** Settings > Sharing

**Contenido:**
- **Sección "I'm sharing with"** (Yo comparto con...)
  - Lista de personas con quienes compartes
  - Para cada persona:
    - Nombre/Email
    - Permisos actuales (✓ Moods, ✓ Notes, ✗ Selfies)
    - Botón "Edit Permissions"
    - Botón "Revoke Access"

- **Sección "Invite Someone"**
  - Input: Email
  - Checkboxes:
    - [x] Share Moods
    - [ ] Share Notes
    - [ ] Share Selfies
  - Botón "Send Invitation"

- **Sección "Pending Invitations"**
  - Lista de invitaciones enviadas pendientes
  - Email del destinatario
  - Estado: "Waiting for response"
  - Botón "Cancel Invitation"

**Tareas:**
- [ ] Crear `screens/SharingSettingsScreen.js`
- [ ] Lista de shared_access (API: `/api/sharing/sharing-with`)
- [ ] Formulario de invitación (API: `/api/sharing/invite`)
- [ ] Lista de invitaciones pendientes (API: `/api/sharing/my-invites`)
- [ ] Editar permisos modal
- [ ] Confirmación para revocar acceso

---

#### Pantalla 2: **Shared With Me (Lo que comparten conmigo)**
**Ubicación:** Tab principal o sección en Dashboard

**Contenido:**
- **Lista de personas que comparten contigo**
  - Para cada persona:
    - Nombre/Avatar
    - Último mood compartido
    - Indicador de permisos (qué puedes ver)
    - Tap para ver detalles

- **Vista detalle de persona:**
  - Nombre
  - Timeline de moods (si tienes permiso)
  - Notas (si tienes permiso)
  - Selfies (si tienes permiso)
  - Filtros por fecha
  - Botón "Stop receiving" (dejar de recibir)

- **Empty State:**
  - "No one is sharing with you yet"
  - "Ask a friend or therapist to share their moods with you"

**Tareas:**
- [ ] Crear `screens/SharedWithMeScreen.js`
- [ ] Lista de shared_access (API: `/api/sharing/shared-with-me`)
- [ ] Crear `screens/SharedPersonDetailScreen.js`
- [ ] Timeline de moods de la persona (API: `/api/sharing/moods/{ownerId}`)
- [ ] Filtros por fecha
- [ ] Respetar permisos (solo mostrar lo permitido)

---

#### Pantalla 3: **Invitations Received (Invitaciones Recibidas)**
**Ubicación:** Notificación push + sección en Settings

**Contenido:**
- **Lista de invitaciones pendientes**
  - Nombre/Email del remitente
  - Permisos que otorgará:
    - "Wants to share: Moods, Notes"
  - Botones:
    - "Accept"
    - "Decline"

- **Al aceptar:**
  - Crear `shared_access`
  - Mostrar confirmación: "You can now see [Name]'s moods"
  - Redirigir a "Shared With Me"

**Tareas:**
- [ ] Crear `screens/ReceivedInvitationsScreen.js`
- [ ] Lista de invitaciones (API: `/api/sharing/invites-received`)
- [ ] Aceptar invitación (API: `/api/sharing/accept/{token}`)
- [ ] Rechazar invitación (API: `/api/sharing/reject/{token}`)
- [ ] Integrar con notificaciones push

---

#### Pantalla 4: **Edit Permissions Modal**
**Acceso:** Desde "Sharing Settings" al editar permisos

**Contenido:**
- Nombre de la persona
- Checkboxes:
  - [ ] Can view my moods
  - [ ] Can view my notes
  - [ ] Can view my selfies
- Botones:
  - "Save Changes"
  - "Cancel"

**Tareas:**
- [ ] Crear `components/EditPermissionsModal.js`
- [ ] Actualizar permisos (API: `/api/sharing/{id}/permissions`)
- [ ] Validación (al menos un permiso debe estar activo)

---

### 3. **Notificaciones Push** 🟡 PRIORIDAD MEDIA

- [ ] Notificación cuando recibes una invitación
  - Título: "[Name] wants to share moods with you"
  - Body: "Tap to accept or decline"
  - Deep link a pantalla de invitaciones

- [ ] Notificación cuando alguien acepta tu invitación
  - Título: "[Name] accepted your invitation"
  - Body: "You can now see their moods"

- [ ] Notificación cuando alguien comparte un nuevo mood (opcional)
  - Configuración: Activar/desactivar por persona
  - Título: "[Name] logged a new mood"
  - Body: Emoji del mood + nota (si permitido)

**Tareas:**
- [ ] Crear notificación al enviar invitación
- [ ] Crear notificación al aceptar invitación
- [ ] Crear notificación de nuevo mood compartido
- [ ] Configuración de notificaciones por persona

---

### 4. **Filtrado de Datos Compartidos** 🔴 PRIORIDAD ALTA

Cuando alguien obtiene moods de una persona que comparte con él:

```php
GET /api/sharing/moods/{ownerId}
```

Debe:
- [ ] Verificar que existe `shared_access` entre los usuarios
- [ ] Filtrar campos según permisos:
  - Si `can_view_moods` = false: Error 403
  - Si `can_view_notes` = false: No incluir campo `note`
  - Si `can_view_selfies` = false: No incluir fotos

**Tareas:**
- [ ] Crear middleware `VerifySharingAccess`
- [ ] Implementar filtrado de campos en respuesta
- [ ] Crear `SharingPolicy` para autorización
- [ ] Tests de permisos

---

### 5. **Dashboard de "Shared With Me"** 🟡 PRIORIDAD MEDIA

Vista agregada de todas las personas que comparten contigo:

- [ ] Widget con resumen:
  - "3 people are sharing with you"
  - Últimas actualizaciones de moods

- [ ] Feed de actividad:
  - "[Name] logged a mood of 😊 8"
  - "[Name] added a note: 'Great day at work!'"
  - Ordenado por fecha descendente

**Tareas:**
- [ ] Crear endpoint `/api/sharing/activity-feed`
- [ ] Componente `SharedActivityFeed.js`
- [ ] Ordenar por timestamp
- [ ] Paginación

---

### 6. **Mejoras de UX** 🟢 PRIORIDAD BAJA

- [ ] Avatars/fotos de perfil para usuarios
- [ ] Badge de "new moods" no vistos
- [ ] Gráficos comparativos (mi mood vs. mood de quien comparte)
- [ ] Exportar moods compartidos a PDF
- [ ] Mensajería entre usuarios que comparten (futuro)

---

## 🎯 ROADMAP SUGERIDO

### **Sprint 1: API Backend** (3-4 días)
1. Crear `SharingController` con todos los endpoints
2. Middleware de verificación de permisos
3. Filtrado de datos según permisos
4. Tests de API

### **Sprint 2: Pantallas Core Mobile** (4-5 días)
1. Pantalla "Sharing Settings"
2. Formulario de invitación
3. Lista de shared_access
4. Revocar/editar permisos

### **Sprint 3: Shared With Me** (3-4 días)
1. Pantalla "Shared With Me"
2. Timeline de moods de personas
3. Detalle de persona
4. Filtros y navegación

### **Sprint 4: Invitaciones** (2-3 días)
1. Pantalla "Received Invitations"
2. Aceptar/rechazar invitaciones
3. Deep links desde notificaciones

### **Sprint 5: Notificaciones** (2-3 días)
1. Push notifications de invitaciones
2. Notificaciones de aceptación
3. Notificaciones de nuevos moods (opcional)

---

## 📱 MOCKUPS

### Sharing Settings
```
┌─────────────────────────────────┐
│  ← Sharing Settings             │
├─────────────────────────────────┤
│  I'm Sharing With (2)           │
│                                 │
│  👤 María García                │
│  ✓ Moods  ✓ Notes  ✗ Selfies   │
│  [Edit] [Revoke]                │
│  ─────────────────────────────  │
│  👤 Dr. Smith (Therapist)       │
│  ✓ Moods  ✓ Notes  ✓ Selfies   │
│  [Edit] [Revoke]                │
│                                 │
├─────────────────────────────────┤
│  Invite Someone                 │
│  ┌───────────────────────────┐ │
│  │ email@example.com         │ │
│  └───────────────────────────┘ │
│  ☑ Share Moods                  │
│  ☐ Share Notes                  │
│  ☐ Share Selfies                │
│  [Send Invitation]              │
│                                 │
├─────────────────────────────────┤
│  Pending Invitations (1)        │
│  📧 friend@example.com          │
│  Waiting for response...        │
│  [Cancel]                       │
└─────────────────────────────────┘
```

### Shared With Me
```
┌─────────────────────────────────┐
│  Shared With Me          [Filter]│
├─────────────────────────────────┤
│                                 │
│  👤 María García                │
│  😊 Mood: 8.5 · 2 hours ago     │
│  "Feeling great today!"         │
│  ─────────────────────────────  │
│  👤 Dr. Smith                   │
│  😐 Mood: 6.0 · 1 day ago       │
│  ─────────────────────────────  │
│  👤 John Doe                    │
│  😄 Mood: 9.0 · 3 days ago      │
│  "Best week ever!"              │
│                                 │
└─────────────────────────────────┘
```

### Received Invitations
```
┌─────────────────────────────────┐
│  ← Invitations                  │
├─────────────────────────────────┤
│                                 │
│  💌 New Invitation              │
│                                 │
│  From: María García             │
│  maria@example.com              │
│                                 │
│  Wants to share:                │
│  ✓ Moods                        │
│  ✓ Notes                        │
│  ✗ Selfies                      │
│                                 │
│  [Accept]       [Decline]       │
│                                 │
└─────────────────────────────────┘
```

---

## 🔒 Consideraciones de Seguridad y Privacidad

1. **Verificación de permisos:** Siempre validar en backend que el usuario tiene permiso
2. **Tokens seguros:** Invitations con tokens únicos y fecha de expiración
3. **Revocación inmediata:** Al revocar acceso, eliminar `shared_access` inmediatamente
4. **Auditoría:** Log de quién accede a qué datos
5. **Consentimiento explícito:** El receptor debe aceptar la invitación
6. **Granularidad:** Permisos separados para moods/notes/selfies
7. **No transitividad:** Si A comparte con B y B con C, C NO ve datos de A

---

## 📊 Métricas de Éxito

- [ ] Número de invitaciones enviadas
- [ ] Tasa de aceptación de invitaciones
- [ ] Número promedio de shared_access por usuario
- [ ] Engagement: % de usuarios que ven datos compartidos semanalmente
- [ ] Retención: Usuarios que mantienen shared_access > 30 días

---

## 🚀 SIGUIENTE PASO INMEDIATO

**EMPEZAR POR:**
1. Crear `SharingController.php` con endpoint `/invite`
2. Crear pantalla móvil "Sharing Settings" básica
3. Implementar envío de invitación end-to-end:
   - Usuario ingresa email y permisos
   - Se envía invitación
   - Receptor recibe email
   - Receptor acepta
   - Compartir está activo

Una vez esto funcione, expandir con las demás pantallas y features.
