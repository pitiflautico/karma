# 📋 Tareas Pendientes - Sistema de Grupos & Eventos

**Fecha:** 20 de Octubre, 2025
**Proyecto:** Karma - Mood Tracking App

---

## 🎨 PANTALLAS CREADAS - PENDIENTES DE REVISIÓN

Todas las pantallas están diseñadas y listas para revisar en el navegador.

### 1. Lista de Mis Grupos ⭐ PRIORITARIA
**URL:** `http://127.0.0.1:8000/groups`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Diseño de cards de grupos
- Empty state si no hay grupos
- Botón flotante de "Join Group"
- Avatares circulares con color de fondo
- Barras de progreso de actividad

---

### 2. Unirse a Grupo ⭐ PRIORITARIA
**URL:** `http://127.0.0.1:8000/groups/join`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Input centrado para código de invitación
- Espaciado de letras en el input
- Info box azul de ayuda
- Botón con loading state
- Mensajes de error/éxito

---

### 3. Dashboard de Grupo ⭐⭐ MUY IMPORTANTE
**URL:** `http://127.0.0.1:8000/groups/{groupId}`
**Ejemplo:** `http://127.0.0.1:8000/groups/1`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Card de información del grupo
- Invite code con botón de copiar
- Sistema de tabs (Stats / Events)
- Selector de período (Today/7Days/30Days)
- Card de mood promedio grande
- Card de actividad con barra de progreso
- Card de distribución de moods con gráficas
- Menú "..." con opción Leave Group

---

### 4. Eventos del Grupo ⭐⭐ MUY IMPORTANTE
**URL:** `http://127.0.0.1:8000/groups/{groupId}/events`
**Ejemplo:** `http://127.0.0.1:8000/groups/1/events`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Filtros horizontales (All/Upcoming/Past/My Ratings)
- Cards de eventos con iconos diferenciados
- Badges de "Custom" vs "Calendar"
- Mood promedio y contador de valoraciones
- Badge amarillo "Rate this event!"
- Checkmark verde "You rated this"
- Empty state
- Botón flotante de crear evento

---

### 5. Crear Evento ⭐ PRIORITARIA
**URL:** `http://127.0.0.1:8000/groups/{groupId}/events/create`
**Ejemplo:** `http://127.0.0.1:8000/groups/1/events/create`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Form con título, descripción, fecha y hora
- Asteriscos rojos en campos requeridos
- Date picker y time picker
- Info box azul explicativo
- Botón con loading state

---

### 6. Valorar Evento ⭐⭐⭐ CRÍTICA
**URL:** `http://127.0.0.1:8000/groups/events/{eventId}/rate`
**Ejemplo:** `http://127.0.0.1:8000/groups/events/1/rate`
**Estado:** ✅ Diseño completo | ⏳ Sin lógica backend

**Qué revisar:**
- Card de información del evento
- Grid de moods 5x2 (10 opciones)
- Selección de mood con ring morado
- Nombre del mood seleccionado
- Textarea para nota
- Botón "Submit/Update Rating"
- Sección de mood promedio del grupo (emoji gigante)
- Gráfico de distribución horizontal
- Privacy notice
- Empty state si no hay valoraciones

---

## 🔴 TAREAS PENDIENTES - PRIORIDAD ALTA

### Backend - Lógica Livewire

Cada componente Livewire necesita implementación:

#### 1. GroupsList.php
```php
- [ ] Property: public $groups = []
- [ ] Method: mount() - Cargar grupos del usuario
- [ ] Calcular mood_today, activity_rate por grupo
- [ ] Generar mood_emoji según el score
```

#### 2. JoinGroup.php
```php
- [ ] Property: public $inviteCode
- [ ] Method: joinGroup() - Validar código y unir
- [ ] Validación: código de 8 caracteres
- [ ] Verificar grupo activo
- [ ] Verificar no sea ya miembro
- [ ] Añadir usuario a group_members con role='member'
- [ ] Redirect a dashboard del grupo
```

#### 3. GroupDashboard.php
```php
- [ ] Property: public $groupId, $group, $stats, $period = '7d'
- [ ] Method: mount($groupId) - Cargar grupo y verificar membresía
- [ ] Method: setPeriod($period) - Cambiar período de stats
- [ ] Calcular stats: average_mood, activity_today, mood_distribution
- [ ] Verificar que usuario sea miembro del grupo
- [ ] Generar mood_emoji según average_mood
```

#### 4. GroupEvents.php
```php
- [ ] Property: public $groupId, $events, $filter = 'all'
- [ ] Method: mount($groupId) - Cargar eventos del grupo
- [ ] Method: setFilter($filter) - Filtrar eventos
- [ ] Calcular average_mood, rating_count por evento
- [ ] Verificar si user_rated cada evento
- [ ] Ordenar por fecha (upcoming primero)
```

#### 5. CreateGroupEvent.php
```php
- [ ] Property: public $groupId, $title, $description, $eventDate, $eventTime
- [ ] Method: mount($groupId) - Verificar membresía
- [ ] Method: createEvent() - Crear evento
- [ ] Validación: title required, eventDate required, eventTime required
- [ ] Combinar fecha + hora en event_date
- [ ] created_by = auth()->id()
- [ ] is_custom = true
- [ ] Redirect a lista de eventos
```

#### 6. RateGroupEvent.php ⭐ MÁS COMPLEJO
```php
- [ ] Property: public $eventId, $event, $selectedMood, $note
- [ ] Property: public $moods = [] (array de 10 moods)
- [ ] Property: public $userRating, $groupStats
- [ ] Method: mount($eventId) - Cargar evento y stats
- [ ] Method: selectMood($score) - Seleccionar mood
- [ ] Method: submitRating() - Guardar/actualizar valoración
- [ ] Cargar valoración existente del usuario
- [ ] Calcular groupStats: average_mood, rating_count, distribution
- [ ] Verificar unique constraint (user + event)
```

---

## 🟡 TAREAS PENDIENTES - PRIORIDAD MEDIA

### Datos de Prueba (Seeders)

Para poder ver las pantallas funcionando:

```php
- [ ] Crear GroupSeeder
  - [ ] Crear 3-5 grupos de ejemplo
  - [ ] Asignar colores (#8B5CF6, #3B82F6, #10B981, etc.)
  - [ ] Generar invite_codes únicos

- [ ] Crear GroupMemberSeeder
  - [ ] Añadir usuario actual como admin de 2 grupos
  - [ ] Añadir usuario actual como member de 1 grupo
  - [ ] Añadir usuarios fake a los grupos

- [ ] Crear GroupEventSeeder
  - [ ] Crear 5-10 eventos por grupo
  - [ ] Mix de is_custom true/false
  - [ ] Fechas variadas (pasadas, futuras)

- [ ] Crear GroupEventMoodSeeder
  - [ ] Crear valoraciones fake para eventos
  - [ ] Scores variados (1-10)
  - [ ] Algunos eventos con todas las valoraciones
  - [ ] Algunos eventos sin valorar
```

---

## 🟢 TAREAS PENDIENTES - PRIORIDAD BAJA

### API Endpoints (Si se necesitan para app móvil nativa)

```php
- [ ] GroupController@index - GET /api/groups/my-groups
- [ ] GroupController@show - GET /api/groups/{id}
- [ ] GroupController@stats - GET /api/groups/{id}/stats
- [ ] GroupController@join - POST /api/groups/join
- [ ] GroupController@leave - POST /api/groups/{id}/leave

- [ ] GroupEventController@index - GET /api/groups/{id}/events
- [ ] GroupEventController@store - POST /api/groups/{id}/events
- [ ] GroupEventController@show - GET /api/groups/events/{id}

- [ ] GroupEventMoodController@rate - POST /api/groups/events/{id}/rate
- [ ] GroupEventMoodController@update - PUT /api/groups/events/{id}/rate
- [ ] GroupEventMoodController@stats - GET /api/groups/events/{id}/moods
```

---

## 🧪 TAREAS PENDIENTES - TESTING

### Tests a Crear

```php
- [ ] Feature/GroupTest.php
  - [ ] test_user_can_join_group_with_valid_code()
  - [ ] test_user_cannot_join_group_twice()
  - [ ] test_user_cannot_join_inactive_group()
  - [ ] test_user_can_leave_group()

- [ ] Feature/GroupEventTest.php
  - [ ] test_member_can_create_event()
  - [ ] test_member_can_rate_event_once()
  - [ ] test_member_can_update_rating()
  - [ ] test_non_member_cannot_see_events()

- [ ] Feature/GroupEventStatsTest.php
  - [ ] test_average_mood_calculation()
  - [ ] test_distribution_calculation()
  - [ ] test_privacy_with_less_than_3_members()
```

---

## 📊 RESUMEN DEL PROGRESO

### ✅ COMPLETADO (70%)

- [x] Migraciones de base de datos
- [x] Modelos Eloquent (Group, GroupEvent, GroupEventMood)
- [x] Relaciones entre modelos
- [x] 6 pantallas con diseño completo
- [x] Rutas web configuradas
- [x] Componentes Livewire creados (vacíos)
- [x] Documentación completa del sistema
- [x] "My Groups" añadido al menú

### ⏳ PENDIENTE (30%)

- [ ] Implementar lógica Livewire (6 componentes)
- [ ] Crear seeders con datos de prueba
- [ ] Testing básico
- [ ] API endpoints (opcional)

---

## 🎯 SIGUIENTE PASO RECOMENDADO

### Opción A: Revisar Diseños Primero
1. Levantar el servidor: `php artisan serve`
2. Visitar las 6 URLs
3. Revisar diseño mobile (inspeccionar como iPhone)
4. Hacer ajustes si necesitas
5. Luego implementar lógica

### Opción B: Implementar Lógica Directamente
1. Crear seeders para tener datos
2. Implementar lógica de cada componente Livewire
3. Ir probando cada pantalla mientras desarrollas

---

## 📝 NOTAS IMPORTANTES

### Ajustes de Nombres de Tablas

El código actual usa `group_members` como nombre de tabla pivot, pero la migración original creó `group_user`. **Necesitarás:**

```bash
# Opción 1: Renombrar la tabla en la base de datos
php artisan migrate

# Opción 2: Ajustar los modelos para usar 'group_user'
# En Group.php y User.php cambiar:
->belongsToMany(User::class, 'group_user')
```

### Moods Data

Para el grid de moods (1-10), necesitas definir los datos:

```php
$moods = [
    ['score' => 1, 'icon' => '😢', 'name' => 'Terrible'],
    ['score' => 2, 'icon' => '☹️', 'name' => 'Very Bad'],
    ['score' => 3, 'icon' => '😕', 'name' => 'Bad'],
    ['score' => 4, 'icon' => '😐', 'name' => 'Poor'],
    ['score' => 5, 'icon' => '😶', 'name' => 'Okay'],
    ['score' => 6, 'icon' => '🙂', 'name' => 'Fine'],
    ['score' => 7, 'icon' => '😊', 'name' => 'Good'],
    ['score' => 8, 'icon' => '😄', 'name' => 'Great'],
    ['score' => 9, 'icon' => '😁', 'name' => 'Amazing'],
    ['score' => 10, 'icon' => '🤩', 'name' => 'Perfect'],
];
```

---

**¿Por dónde quieres empezar? ¿Revisas los diseños o implementamos la lógica?**
