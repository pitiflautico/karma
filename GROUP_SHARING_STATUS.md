# Group Sharing & Mood Tracking - Estado Actual y Tareas Pendientes

## 📋 Resumen
Sistema de compartir mood tracking de forma anónima en grupos (familias, equipos, escuelas). Los usuarios pueden unirse a grupos mediante códigos de invitación y ver estadísticas agregadas sin comprometer privacidad individual.

**NUEVO**: Sistema de eventos de grupo que permite crear eventos (manuales o desde Google Calendar) donde todos los miembros del grupo pueden valorar con su mood, mostrando un mood promedio del grupo.

---

## ✅ Lo que YA está HECHO

### 1. **Base de Datos** ✅
- [x] Tabla `groups` actualizada
  - `id` (UUID)
  - `name` - Nombre del grupo
  - `slug` - URL-friendly slug
  - `invite_code` - Código único de 8 caracteres (ej: "ABC12XYZ")
  - `description` - Descripción del grupo
  - `avatar` - Imagen del grupo (nullable)
  - `color` - Color identificador (#8B5CF6 por defecto)
  - `created_by` - Usuario que creó el grupo
  - `is_active` - Estado activo/inactivo
  - `timestamps`

- [x] Tabla `group_members` (renombrada de group_user)
  - `group_id`
  - `user_id`
  - `role` - admin o member
  - `joined_at` - Timestamp de unión
  - `timestamps`

- [x] Tabla `group_events` creada ✨ NUEVO
  - `id`
  - `group_id` - FK al grupo
  - `calendar_event_id` - FK opcional a calendar_events
  - `title` - Título del evento
  - `description` - Descripción del evento
  - `event_date` - Fecha y hora del evento
  - `created_by` - Usuario que creó el evento
  - `is_custom` - true si es manual, false si viene de calendar
  - `timestamps`

- [x] Tabla `group_event_moods` creada ✨ NUEVO
  - `id`
  - `group_event_id` - FK al evento
  - `user_id` - FK al usuario que valora
  - `mood_score` - Puntuación 1-10
  - `mood_icon` - Icono del mood
  - `mood_name` - Nombre del mood
  - `note` - Nota opcional
  - `timestamps`
  - **Unique constraint**: (group_event_id, user_id) - Un usuario solo puede valorar una vez cada evento

### 2. **Modelos** ⚠️ PARCIALMENTE COMPLETO
- [x] **Group Model** (`app/Models/Group.php`)
  - Relación `belongsToMany` con User
  - Relación `hasMany` con MoodEntry
  - Método `getAverageMood()` - Calcula promedio de moods del grupo
  - Auto-generación de `invite_code` al crear grupo

- [x] **User Model** - Relación con grupos
  - Relación `belongsToMany` con Group

- [ ] **GroupEvent Model** - PENDIENTE ✨ NUEVO
  - Relación `belongsTo` con Group
  - Relación `belongsTo` con CalendarEvent (optional)
  - Relación `belongsTo` con User (created_by)
  - Relación `hasMany` con GroupEventMood
  - Método `getAverageMood()` - Calcula promedio de valoraciones del evento

- [ ] **GroupEventMood Model** - PENDIENTE ✨ NUEVO
  - Relación `belongsTo` con GroupEvent
  - Relación `belongsTo` con User

### 3. **Admin Panel (Filament)** ✅
- [x] **GroupResource** completo
  - Crear grupos (nombre, slug, código, descripción)
  - Editar grupos
  - Ver lista de grupos
  - Asignar miembros al grupo
  - Copiar código de invitación
  - Ver estadísticas (total miembros, moods del grupo)
  - Desactivar/activar grupos

---

## ❌ Lo que FALTA por HACER

### 1. **Modelos Eloquent** 🔴 PRIORIDAD ALTA

**Tareas:**
- [ ] Crear `app/Models/GroupEvent.php`
- [ ] Crear `app/Models/GroupEventMood.php`
- [ ] Actualizar `app/Models/Group.php` con relaciones a GroupEvent
- [ ] Actualizar `app/Models/User.php` con relaciones a GroupEvent y GroupEventMood

### 2. **API Endpoints para Eventos de Grupo** 🔴 PRIORIDAD ALTA ✨ NUEVO

Necesitamos crear estos endpoints adicionales:

```php
Route::prefix('groups')->middleware('auth:api')->group(function () {
    // Eventos del grupo
    Route::get('/{groupId}/events', [GroupEventController::class, 'index']);
    Route::post('/{groupId}/events', [GroupEventController::class, 'store']);
    Route::get('/events/{eventId}', [GroupEventController::class, 'show']);

    // Valorar evento con mood
    Route::post('/events/{eventId}/rate', [GroupEventMoodController::class, 'rate']);
    Route::put('/events/{eventId}/rate', [GroupEventMoodController::class, 'update']);
    Route::get('/events/{eventId}/moods', [GroupEventMoodController::class, 'index']);
});
```

**Tareas:**
- [ ] Crear `app/Http/Controllers/Api/GroupEventController.php`
- [ ] Implementar `index($groupId)` - Listar eventos del grupo
- [ ] Implementar `store($groupId)` - Crear evento manual
- [ ] Implementar `show($eventId)` - Detalles del evento
- [ ] Crear `app/Http/Controllers/Api/GroupEventMoodController.php`
- [ ] Implementar `rate($eventId)` - Valorar evento con mood
- [ ] Implementar `update($eventId)` - Actualizar valoración
- [ ] Implementar `index($eventId)` - Ver valoraciones del evento (agregadas)

### 3. **Detección Automática de Eventos desde Calendar** 🟡 PRIORIDAD MEDIA ✨ NUEVO

Cuando se sincroniza el Google Calendar, detectar si hay attendees que son miembros del mismo grupo:

**Tareas:**
- [ ] Crear `app/Services/GroupEventDetectionService.php`
- [ ] Lógica: Al sincronizar calendar_events, buscar si los attendees pertenecen al mismo grupo
- [ ] Si hay match, crear automáticamente GroupEvent con `is_custom=false`
- [ ] Añadir comando artisan: `php artisan groups:detect-calendar-events`
- [ ] Integrar detección en el proceso de sincronización de calendar

### 4. **Pantallas Móviles (React Native)** 🔴 PRIORIDAD ALTA

#### Pantalla 1: **Lista de Mis Grupos**
**Ubicación sugerida:** Pestaña o sección en el menú principal

**Contenido:**
- Lista de grupos a los que pertenece el usuario
- Para cada grupo mostrar:
  - Nombre del grupo
  - Número de miembros (ej: "12 members")
  - Mood promedio del grupo hoy (con emoji/color)
  - Actividad del grupo (% de miembros que loguearon mood hoy)
- Botón flotante "Join New Group" (código de invitación)
- Si no tiene grupos: Empty state con ilustración y botón "Join Your First Group"

**Tareas:**
- [ ] Crear `screens/GroupsListScreen.js`
- [ ] Diseñar card de grupo con estadísticas
- [ ] Implementar API call a `/api/groups/my-groups`
- [ ] Agregar navegación a esta pantalla desde el menú

---

#### Pantalla 2: **Dashboard de Grupo**
**Acceso:** Al hacer tap en un grupo de la lista

**Contenido:**
- **Header:**
  - Nombre del grupo
  - Descripción
  - Número de miembros
  - Botón "Leave Group" (con confirmación)

- **Estadísticas Agregadas (Anónimas):**
  - Mood promedio del grupo (últimas 24h, 7 días, 30 días)
  - Gráfico de tendencia de mood del grupo (línea)
  - Actividad hoy: "8/12 members logged mood today"
  - Distribución de moods (gráfico de barras o donut)

- **Restricciones de Privacidad:**
  - ❌ NO mostrar nombres de miembros
  - ❌ NO mostrar moods individuales
  - ✅ Solo datos agregados y anónimos

**Tareas:**
- [ ] Crear `screens/GroupDashboardScreen.js`
- [ ] Implementar gráficos con `react-native-chart-kit` o `victory-native`
- [ ] API call a `/api/groups/{id}/stats`
- [ ] Implementar "Leave Group" con confirmación
- [ ] Diseño de tarjetas de estadísticas

---

#### Pantalla 3: **Unirse a Grupo (Join Group)**
**Acceso:** Botón "Join New Group" desde la lista

**Contenido:**
- Input para código de invitación (8 caracteres)
- Botón "Join Group"
- Validación:
  - Código debe existir
  - Grupo debe estar activo
  - Usuario no debe estar ya en el grupo
- Éxito: Redirigir al dashboard del grupo recién unido
- Error: Mostrar mensaje (código inválido, grupo inactivo, etc.)

**Tareas:**
- [ ] Crear `screens/JoinGroupScreen.js`
- [ ] Input de código con validación (8 caracteres, uppercase)
- [ ] API call a `/api/groups/join` con código
- [ ] Manejo de errores (código inválido, ya miembro, etc.)
- [ ] Navegación al dashboard del grupo tras unirse

---

#### Pantalla 4: **Modal de Confirmación (Leave Group)**
**Acceso:** Botón "Leave Group" en dashboard de grupo

**Contenido:**
- Modal/Alert nativo
- Título: "Leave [Group Name]?"
- Mensaje: "You won't see this group's stats anymore. You can rejoin anytime with the invite code."
- Botones: "Cancel" / "Leave Group"

**Tareas:**
- [ ] Crear `components/LeaveGroupModal.js` o usar Alert nativo
- [ ] API call a `/api/groups/{id}/leave`
- [ ] Actualizar lista de grupos tras salir
- [ ] Navegación de regreso a lista de grupos

---

### 3. **Vistas Web (Livewire)** 🟡 PRIORIDAD MEDIA

Aunque la app es principalmente móvil, necesitamos vistas web básicas:

- [ ] Crear `app/Livewire/MyGroups.php`
- [ ] Crear `resources/views/livewire/my-groups.blade.php`
- [ ] Crear `app/Livewire/GroupDashboard.php`
- [ ] Crear `resources/views/livewire/group-dashboard.blade.php`
- [ ] Crear `app/Livewire/JoinGroup.php`
- [ ] Crear `resources/views/livewire/join-group.blade.php`
- [ ] Agregar ruta `/groups` en `routes/web.php`
- [ ] Agregar enlace "My Groups" en menú hamburguesa

---

### 4. **Lógica de Negocio y Servicios** 🟡 PRIORIDAD MEDIA

- [ ] Crear `app/Services/GroupStatsService.php`
  - `getAverageMood($groupId, $period)` - Mood promedio por período
  - `getMoodDistribution($groupId)` - Distribución de moods (1-10)
  - `getActivityRate($groupId)` - % de miembros activos hoy
  - `getMoodTrend($groupId, $days)` - Tendencia de mood (array de promedios)

- [ ] Implementar privacidad:
  - Verificar que usuario sea miembro antes de ver stats
  - Ocultar datos si grupo tiene menos de 3 miembros (privacidad)
  - Logs de acceso a grupos (auditoría)

---

### 5. **Asociar Moods a Grupos** 🔴 PRIORIDAD ALTA

Actualmente los mood entries tienen un campo `group_id` pero no se está usando. Necesitamos:

- [ ] Al crear un mood entry, si el usuario pertenece a grupos, preguntar:
  - "Share this mood with your groups?"
  - Checkboxes para cada grupo
  - Puede compartir con 0, 1 o múltiples grupos

- [ ] Modificar API de crear mood (`POST /api/moods`):
  - Agregar campo opcional `group_ids: [uuid1, uuid2]`
  - Validar que usuario pertenezca a esos grupos
  - Crear un mood entry por cada grupo seleccionado

- [ ] En la app móvil (pantalla de crear mood):
  - Mostrar lista de grupos del usuario
  - Multi-select para compartir con grupos
  - Por defecto: ninguno seleccionado (privacidad)

---

### 6. **Notificaciones de Grupo** 🟢 PRIORIDAD BAJA

- [ ] Push notification cuando el mood promedio del grupo cambia significativamente
- [ ] Notificación diaria: "Your group [Name] has logged moods today!"
- [ ] Configuración para activar/desactivar notificaciones de grupo

---

### 7. **Tests** 🟢 PRIORIDAD BAJA

- [ ] `tests/Feature/GroupControllerTest.php`
  - Test unirse a grupo con código válido
  - Test unirse con código inválido
  - Test salir de grupo
  - Test ver stats solo si eres miembro
  - Test privacidad de datos

- [ ] `tests/Feature/GroupStatsServiceTest.php`
  - Test cálculo de mood promedio
  - Test distribución de moods
  - Test actividad del grupo

---

## 🎯 ROADMAP SUGERIDO

### **Sprint 1: API y Backend** (3-4 días)
1. Crear `GroupController` con todos los endpoints
2. Crear `GroupStatsService` con cálculos de estadísticas
3. Modificar API de moods para soportar `group_ids`
4. Tests básicos

### **Sprint 2: Pantallas Móviles Core** (4-5 días)
1. Pantalla "Lista de Mis Grupos"
2. Pantalla "Unirse a Grupo"
3. Integración con API
4. Navegación entre pantallas

### **Sprint 3: Dashboard de Grupo** (3-4 días)
1. Pantalla "Dashboard de Grupo"
2. Gráficos y estadísticas
3. Modal de "Leave Group"
4. Polish y UX

### **Sprint 4: Asociar Moods a Grupos** (2-3 días)
1. UI en pantalla de crear mood para seleccionar grupos
2. API actualizada
3. Validaciones

### **Sprint 5: Vistas Web (Opcional)** (2-3 días)
1. Vistas Livewire básicas
2. Integración en web dashboard

---

## 📱 MOCKUPS / WIREFRAMES

### Lista de Grupos
```
┌─────────────────────────────────┐
│  My Groups              [+ Join]│
├─────────────────────────────────┤
│                                 │
│  🏢 Work Team                   │
│  12 members · Mood today: 😊 7.2│
│  ──────────────── 75% active    │
│                                 │
│  👨‍👩‍👧 Family                     │
│  5 members · Mood today: 😌 8.1 │
│  ──────────────── 100% active   │
│                                 │
│  🎓 Study Group                 │
│  8 members · Mood today: 😐 5.8 │
│  ──────────────── 50% active    │
│                                 │
└─────────────────────────────────┘
```

### Dashboard de Grupo
```
┌─────────────────────────────────┐
│  ← Work Team            [Leave] │
├─────────────────────────────────┤
│  A safe space for our team      │
│  12 members                     │
├─────────────────────────────────┤
│                                 │
│  Today's Average Mood           │
│  ┌─────────────────────────┐   │
│  │       😊 7.2            │   │
│  │   9/12 logged mood      │   │
│  └─────────────────────────┘   │
│                                 │
│  7-Day Trend                    │
│  ┌─────────────────────────┐   │
│  │     📈 Chart here       │   │
│  └─────────────────────────┘   │
│                                 │
│  Mood Distribution              │
│  ┌─────────────────────────┐   │
│  │  😞  😐  😊  😄         │   │
│  │  ▂▂  ▄▄  ██  ▆▆         │   │
│  └─────────────────────────┘   │
│                                 │
└─────────────────────────────────┘
```

### Unirse a Grupo
```
┌─────────────────────────────────┐
│  ← Join a Group                 │
├─────────────────────────────────┤
│                                 │
│  Enter the invite code          │
│  shared by the group admin      │
│                                 │
│  ┌─────────────────────────┐   │
│  │   [ A B C 1 2 X Y Z ]   │   │
│  └─────────────────────────┘   │
│                                 │
│  ┌─────────────────────────┐   │
│  │      Join Group         │   │
│  └─────────────────────────┘   │
│                                 │
└─────────────────────────────────┘
```

---

## 🔒 Consideraciones de Privacidad

1. **Anonimato:** Los miembros del grupo NO ven quién es quién
2. **Agregación:** Solo se muestran promedios y totales, nunca moods individuales
3. **Mínimo de miembros:** Si un grupo tiene < 3 miembros, no mostrar stats (evitar identificación)
4. **Opt-in:** El usuario decide si comparte cada mood con grupos
5. **Admin transparency:** Solo admins (en Filament) ven lista de miembros

---

## 📊 Métricas de Éxito

- [ ] Número de grupos activos
- [ ] Promedio de miembros por grupo
- [ ] % de moods compartidos con grupos
- [ ] Retención de usuarios en grupos (30 días)
- [ ] Engagement: % de miembros que loguean mood diario en grupos

---

---

## ✨ NUEVO: Sistema de Eventos de Grupo

### Funcionalidad
Los grupos ahora pueden tener **eventos** que todos los miembros pueden valorar con su mood:

1. **Eventos Manuales**: Cualquier miembro puede crear un evento (ej: "Comida familiar")
2. **Eventos de Calendar**: Si varios miembros del grupo asisten al mismo evento de Google Calendar, se crea automáticamente
3. **Valoraciones**: Cada miembro valora el evento con su mood (1-10) + nota opcional
4. **Mood de Grupo**: Se muestra el promedio de todas las valoraciones

### Permisos
- **Todos los miembros** pueden:
  - Ver eventos del grupo
  - Crear eventos manuales
  - Valorar cualquier evento
  - Ver mood promedio del grupo del evento
- **Admins** pueden:
  - Eliminar eventos
  - Eliminar valoraciones inapropiadas

### Pantallas Nuevas Necesarias

#### Pantalla: **Eventos de Grupo** ✨ NUEVO
**Acceso:** Desde el dashboard del grupo, tab "Events"

**Contenido:**
- Lista de eventos pasados y futuros del grupo
- Para cada evento mostrar:
  - Título y fecha
  - Icono indicando si es manual o de calendar
  - Mood promedio del grupo (si ya hay valoraciones)
  - Número de miembros que han valorado
  - Badge "Rate this!" si el usuario no ha valorado aún
- Botón flotante "Create Event"
- Filtros: Todos / Próximos / Pasados / Solo mis valoraciones

**Tareas:**
- [ ] Crear `screens/GroupEventsScreen.js`
- [ ] API call a `/api/groups/{groupId}/events`
- [ ] Navegación desde dashboard de grupo

---

#### Pantalla: **Crear Evento** ✨ NUEVO
**Acceso:** Botón "Create Event" desde lista de eventos

**Contenido:**
- Input: Título del evento (requerido)
- Input: Descripción (opcional)
- DateTimePicker: Fecha y hora del evento
- Botón "Create Event"

**Tareas:**
- [ ] Crear `screens/CreateGroupEventScreen.js`
- [ ] API call a `POST /api/groups/{groupId}/events`

---

#### Pantalla: **Valorar Evento** ✨ NUEVO
**Acceso:** Al hacer tap en un evento desde la lista

**Contenido:**
- Header con título y fecha del evento
- Descripción del evento (si tiene)
- Indicador de origen (manual o calendar)
- **Sección de Mi Valoración:**
  - Mood selector (1-10)
  - Textarea para nota opcional
  - Botón "Submit Rating" / "Update Rating"
- **Sección de Mood del Grupo:**
  - Mood promedio del grupo (grande, destacado)
  - "X/Y members rated this event"
  - NO mostrar valoraciones individuales (privacidad)
  - Distribución de moods (gráfico de barras)

**Tareas:**
- [ ] Crear `screens/RateGroupEventScreen.js`
- [ ] API call a `POST /api/groups/events/{eventId}/rate`
- [ ] API call a `GET /api/groups/events/{eventId}/moods` (stats agregadas)

---

#### Modal: **Detalles del Evento de Calendar** ✨ OPCIONAL
Si el evento viene de Google Calendar, mostrar:
- Link a Google Calendar
- Lista de attendees (solo emails, sin nombres)
- Conference link si hay

**Tareas:**
- [ ] Crear `components/CalendarEventDetailsModal.js`

---

## 🚀 Siguiente Paso Inmediato

**EMPEZAR POR:**
1. ✅ Migraciones de base de datos (COMPLETADO)
2. Crear modelos Eloquent: `GroupEvent.php` y `GroupEventMood.php`
3. Crear controllers: `GroupEventController.php` y `GroupEventMoodController.php`
4. Implementar endpoints de API
5. Crear pantallas móviles para eventos y valoraciones
6. Integrar detección automática desde Google Calendar

Una vez esto funcione, expandir con notificaciones y analytics.
