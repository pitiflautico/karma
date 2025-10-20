# 📱 Pantallas Necesarias para Groups & Sharing - Resumen Ejecutivo

## ✅ BACKEND COMPLETADO
- [x] GroupController con 5 endpoints (join, leave, myGroups, show, stats)
- [x] Modelos: Group, SharedAccess, SharingInvite
- [x] Base de datos completa para groups, group_members, group_events, group_event_moods
- [ ] PENDIENTE: Modelos GroupEvent y GroupEventMood
- [ ] PENDIENTE: Controllers para eventos y valoraciones

## 🎨 PANTALLAS QUE NECESITAS MAQUETAR

### **SISTEMA DE GRUPOS ANÓNIMOS** (7 pantallas - 3 nuevas ✨)

#### 1️⃣ Groups List Screen ⭐ PRIORITARIA
**Archivo:** `screens/GroupsListScreen.js`
- Lista de grupos del usuario
- Stats rápidas (mood promedio, actividad)
- Botón "Join New Group"
- Empty state si no tiene grupos

#### 2️⃣ Join Group Screen ⭐ PRIORITARIA
**Archivo:** `screens/JoinGroupScreen.js`
- Input código de invitación (8 caracteres)
- Botón "Join Group"
- Validación y mensajes de error

#### 3️⃣ Group Dashboard Screen ⭐⭐ MUY IMPORTANTE
**Archivo:** `screens/GroupDashboardScreen.js`
- Header con nombre y descripción
- Mood promedio (24h, 7d, 30d con tabs)
- Gráfico de tendencia (línea)
- Actividad del grupo (circular progress)
- Distribución de moods (barras)
- Botón "Leave Group"

#### 4️⃣ Leave Group Modal
**Archivo:** `components/LeaveGroupModal.js`
- Modal de confirmación simple
- Título, mensaje, botones Cancel/Leave

#### 5️⃣ Group Events Screen ⭐⭐ MUY IMPORTANTE ✨ NUEVO
**Archivo:** `screens/GroupEventsScreen.js`
- Lista de eventos del grupo (pasados y futuros)
- Cada evento muestra: título, fecha, mood promedio, # valoraciones
- Badge "Rate this!" si no has valorado
- Botón "Create Event"
- Filtros: Todos/Próximos/Pasados

#### 6️⃣ Create Group Event Screen ⭐ PRIORITARIA ✨ NUEVO
**Archivo:** `screens/CreateGroupEventScreen.js`
- Form para crear evento manual
- Input: Título (requerido)
- Input: Descripción (opcional)
- DateTimePicker: Fecha y hora
- Botón "Create Event"

#### 7️⃣ Rate Group Event Screen ⭐⭐ MUY IMPORTANTE ✨ NUEVO
**Archivo:** `screens/RateGroupEventScreen.js`
- Ver detalles del evento
- Sección "Mi Valoración": mood selector (1-10) + nota
- Sección "Mood del Grupo": promedio, distribución, stats
- Botón "Submit Rating" o "Update Rating"

---

### **SISTEMA DE SHARING PERSONAL** (5 pantallas)

#### 8️⃣ Sharing Settings Screen ⭐ PRIORITARIA
**Archivo:** `screens/SharingSettingsScreen.js`

**3 Secciones:**
1. **I'm sharing with** - Lista de personas + permisos
2. **Invite Someone** - Form (email + checkboxes permisos)
3. **Pending Invitations** - Lista de invitaciones enviadas

#### 9️⃣ Shared With Me Screen ⭐⭐ MUY IMPORTANTE
**Archivo:** `screens/SharedWithMeScreen.js`
- Lista de personas que comparten contigo
- Para cada persona: avatar, último mood, permisos
- Tap para ver detalles
- Empty state

#### 🔟 Shared Person Detail Screen
**Archivo:** `screens/SharedPersonDetailScreen.js`
- Timeline de moods de la persona
- Filtros por fecha
- Mostrar solo datos permitidos (moods/notas/selfies)
- Botón "Stop receiving"

#### 1️⃣1️⃣ Received Invitations Screen
**Archivo:** `screens/ReceivedInvitationsScreen.js`
- Lista de invitaciones pendientes
- Muestra permisos que otorgará
- Botones Accept/Decline por cada invitación

#### 1️⃣2️⃣ Edit Permissions Modal
**Archivo:** `components/EditPermissionsModal.js`
- Checkboxes: Can view moods/notes/selfies
- Botones Save/Cancel

---

## 📊 RESUMEN TOTAL

### Pantallas a Maquetar: **12 pantallas** (3 nuevas ✨)

#### Prioridad Alta (hacer primero): **7 pantallas**
1. Groups List Screen
2. Join Group Screen
3. ✨ **Create Group Event Screen** (NUEVO)
4. Sharing Settings Screen
5. Shared With Me Screen

#### Prioridad Media: **5 pantallas**
6. Group Dashboard Screen (compleja, gráficos)
7. ✨ **Group Events Screen** (NUEVO - lista de eventos)
8. ✨ **Rate Group Event Screen** (NUEVO - valorar eventos)
9. Shared Person Detail Screen
10. Received Invitations Screen

#### Componentes/Modales: **2 componentes**
11. Leave Group Modal
12. Edit Permissions Modal

---

## 🎯 API ENDPOINTS YA LISTOS

### Groups API (Grupos Básicos)
- ✅ `POST /api/groups/join` - Unirse con código
- ✅ `POST /api/groups/{id}/leave` - Salir del grupo
- ✅ `GET /api/groups/my-groups` - Mis grupos
- ✅ `GET /api/groups/{id}` - Dashboard del grupo
- ✅ `GET /api/groups/{id}/stats?period=7d` - Estadísticas

### Group Events API (NUEVOS - POR HACER ✨)
- ⏳ `GET /api/groups/{groupId}/events` - Listar eventos del grupo
- ⏳ `POST /api/groups/{groupId}/events` - Crear evento manual
- ⏳ `GET /api/groups/events/{eventId}` - Ver detalles del evento
- ⏳ `POST /api/groups/events/{eventId}/rate` - Valorar evento con mood
- ⏳ `PUT /api/groups/events/{eventId}/rate` - Actualizar valoración
- ⏳ `GET /api/groups/events/{eventId}/moods` - Ver stats agregadas del evento

### Sharing API (POR HACER - backend necesario)
- ⏳ `POST /api/sharing/invite`
- ⏳ `GET /api/sharing/my-invites`
- ⏳ `GET /api/sharing/invites-received`
- ⏳ `POST /api/sharing/accept/{token}`
- ⏳ `POST /api/sharing/reject/{token}`
- ⏳ `GET /api/sharing/sharing-with`
- ⏳ `GET /api/sharing/shared-with-me`
- ⏳ `DELETE /api/sharing/revoke/{id}`
- ⏳ `PUT /api/sharing/{id}/permissions`
- ⏳ `GET /api/sharing/moods/{ownerId}`

---

## 📋 ORDEN RECOMENDADO DE IMPLEMENTACIÓN

### Fase 1: Groups (Semana 1)
1. Maqueta: Groups List Screen
2. Maqueta: Join Group Screen
3. Integrar con API de grupos
4. Testear flujo completo

### Fase 2: Group Dashboard (Semana 2)
5. Maqueta: Group Dashboard (gráficos)
6. Implementar gráficos (react-native-chart-kit)
7. Integrar stats API

### Fase 3: Sharing Backend (Semana 3)
8. Crear SharingController (backend)
9. Crear rutas API de sharing

### Fase 4: Sharing UI (Semana 4)
10. Maqueta: Sharing Settings Screen
11. Maqueta: Shared With Me Screen
12. Maqueta: Received Invitations
13. Integrar todo

---

## 💡 NOTAS IMPORTANTES

### Para Groups:
- Mood promedio necesita formato: `😊 7.2`
- Actividad en %: `75% active`
- Gráfico de línea para tendencia
- Privacidad: Stats solo si ≥3 miembros

### Para Sharing:
- Permisos granulares: moods/notes/selfies
- Invitaciones por email
- Tokens únicos con expiración 7 días
- Notificaciones push al recibir invitación

### Gráficos Necesarios:
- Line chart (tendencia de mood)
- Bar chart (distribución de moods)
- Circular progress (actividad %)

---

## 📞 PRÓXIMO PASO

**Te pediré que maquetes las 4 pantallas prioritarias primero:**
1. Groups List Screen
2. Join Group Screen
3. Sharing Settings Screen
4. Shared With Me Screen

Una vez listas, las integro con el API y probamos el flujo completo.

**¿Necesitas mockups detallados de alguna pantalla específica?**
