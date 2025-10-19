# 📱 Pantallas Necesarias para Groups & Sharing - Resumen Ejecutivo

## ✅ BACKEND COMPLETADO
- [x] GroupController con 5 endpoints (join, leave, myGroups, show, stats)
- [x] Modelos: Group, SharedAccess, SharingInvite
- [x] Base de datos completa

## 🎨 PANTALLAS QUE NECESITAS MAQUETAR

### **SISTEMA DE GRUPOS ANÓNIMOS** (4 pantallas)

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

---

### **SISTEMA DE SHARING PERSONAL** (5 pantallas)

#### 5️⃣ Sharing Settings Screen ⭐ PRIORITARIA
**Archivo:** `screens/SharingSettingsScreen.js`

**3 Secciones:**
1. **I'm sharing with** - Lista de personas + permisos
2. **Invite Someone** - Form (email + checkboxes permisos)
3. **Pending Invitations** - Lista de invitaciones enviadas

#### 6️⃣ Shared With Me Screen ⭐⭐ MUY IMPORTANTE
**Archivo:** `screens/SharedWithMeScreen.js`
- Lista de personas que comparten contigo
- Para cada persona: avatar, último mood, permisos
- Tap para ver detalles
- Empty state

#### 7️⃣ Shared Person Detail Screen
**Archivo:** `screens/SharedPersonDetailScreen.js`
- Timeline de moods de la persona
- Filtros por fecha
- Mostrar solo datos permitidos (moods/notas/selfies)
- Botón "Stop receiving"

#### 8️⃣ Received Invitations Screen
**Archivo:** `screens/ReceivedInvitationsScreen.js`
- Lista de invitaciones pendientes
- Muestra permisos que otorgará
- Botones Accept/Decline por cada invitación

#### 9️⃣ Edit Permissions Modal
**Archivo:** `components/EditPermissionsModal.js`
- Checkboxes: Can view moods/notes/selfies
- Botones Save/Cancel

---

## 📊 RESUMEN TOTAL

### Pantallas a Maquetar: **9 pantallas**

#### Prioridad Alta (hacer primero): **4 pantallas**
1. Groups List Screen
2. Join Group Screen
3. Sharing Settings Screen
4. Shared With Me Screen

#### Prioridad Media: **3 pantallas**
5. Group Dashboard Screen (compleja, gráficos)
6. Shared Person Detail Screen
7. Received Invitations Screen

#### Componentes/Modales: **2 componentes**
8. Leave Group Modal
9. Edit Permissions Modal

---

## 🎯 API ENDPOINTS YA LISTOS

### Groups API
- ✅ `POST /api/groups/join` - Unirse con código
- ✅ `POST /api/groups/{id}/leave` - Salir del grupo
- ✅ `GET /api/groups/my-groups` - Mis grupos
- ✅ `GET /api/groups/{id}` - Dashboard del grupo
- ✅ `GET /api/groups/{id}/stats?period=7d` - Estadísticas

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
