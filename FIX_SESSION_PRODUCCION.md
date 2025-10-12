# Fix: Sesión no persiste en producción (HTTPS)

## 🐛 **PROBLEMA**
- ✅ Login funciona correctamente
- ✅ Token se genera
- ❌ El token NO llega al JavaScript del dashboard
- ❌ El mensaje NO se envía al React Native app

## 🔍 **CAUSA**
En producción con **HTTPS**, las cookies de sesión necesitan configuración especial:
- `SESSION_SECURE=true` (para HTTPS)
- `SESSION_DOMAIN=.feelith.com` (para que funcione en subdominios)
- `SESSION_SAME_SITE=lax` (para OAuth redirects)

Sin esta configuración, las cookies no persisten entre el OAuth callback y el dashboard.

---

## ✅ **SOLUCIÓN**

### **1. Actualizar `.env` en PRODUCCIÓN**

Asegúrate que tu `.env` en **producción** tenga esto:

```env
APP_URL=https://feelith.com

# Configuración de sesión para HTTPS
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.feelith.com       # ← IMPORTANTE: con punto al inicio
SESSION_SECURE_COOKIE=true        # ← IMPORTANTE: para HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax             # ← IMPORTANTE: para OAuth
```

### **2. Verificar `config/session.php`**

El archivo `config/session.php` debe usar estas variables:

```php
'domain' => env('SESSION_DOMAIN', null),
'secure' => env('SESSION_SECURE_COOKIE', null),
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

### **3. Limpiar cachés en producción**

```bash
php artisan config:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
```

---

## 🧪 **VERIFICACIÓN**

### **Test 1: Ver configuración actual**

```bash
# En producción, ejecuta:
php artisan tinker

# Dentro de tinker:
config('session.domain');        // Debe ser: ".feelith.com"
config('session.secure');        // Debe ser: true
config('session.same_site');     // Debe ser: "lax"
config('session.driver');        // Debe ser: "database"
```

### **Test 2: Verificar que la sesión persiste**

1. **Haz login** en https://feelith.com
2. **Abre la consola del navegador** (F12)
3. **Ve a Application → Cookies → feelith.com**
4. **Busca la cookie** `karma_session`
5. **Verifica estos valores:**
   - `Domain`: `.feelith.com` (con punto)
   - `Secure`: ✓ (marcada)
   - `HttpOnly`: ✓ (marcada)
   - `SameSite`: `Lax`

### **Test 3: Ver los logs en producción durante login**

```bash
# En el servidor, ejecuta ANTES de hacer login:
tail -f storage/logs/laravel.log | grep -E "(Session ID|native_app_token|native_app_login)"

# Luego haz login y verás:
# - Si el token se guarda: "has_token":true
# - Si la sesión persiste entre requests
```

### **Test 4: Verificar con el debug endpoint**

```bash
# Después de hacer login, visita:
curl -H "Cookie: karma_session=TU_SESSION_COOKIE" \
  "https://feelith.com/debug/session-info?key=debug123"

# Debe mostrar:
# "has_native_app_token": true
# "native_app_login": true
```

---

## 🚨 **ERRORES COMUNES**

### **Error 1: SESSION_DOMAIN sin punto**
```env
❌ SESSION_DOMAIN=feelith.com    # Sin punto
✅ SESSION_DOMAIN=.feelith.com   # Con punto al inicio
```

El punto al inicio hace que funcione en `feelith.com` y `www.feelith.com`.

### **Error 2: SESSION_SECURE_COOKIE=false en HTTPS**
```env
❌ SESSION_SECURE_COOKIE=false   # No funciona en HTTPS
✅ SESSION_SECURE_COOKIE=true    # Necesario para HTTPS
```

### **Error 3: SESSION_SAME_SITE=strict**
```env
❌ SESSION_SAME_SITE=strict      # Bloquea OAuth redirects
✅ SESSION_SAME_SITE=lax         # Permite OAuth redirects
```

### **Error 4: Olvidar limpiar la caché**
```bash
# SIEMPRE después de cambiar .env:
php artisan config:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
```

---

## 🔥 **SOLUCIÓN RÁPIDA (Copiar y Pegar)**

```bash
# 1. SSH al servidor
ssh usuario@feelith.com

# 2. Ir al proyecto
cd /ruta/del/proyecto

# 3. Editar .env
nano .env

# Agrega/modifica estas líneas:
# SESSION_DOMAIN=.feelith.com
# SESSION_SECURE_COOKIE=true
# SESSION_SAME_SITE=lax

# 4. Limpiar cachés
php artisan config:clear
php artisan config:cache

# 5. Reiniciar PHP
sudo systemctl restart php8.3-fpm

# 6. Probar login
# Ve a https://feelith.com y haz login
```

---

## 📊 **DIFERENCIA LOCAL vs PRODUCCIÓN**

| Configuración | Local (funciona) | Producción (no funciona) | Producción (corregido) |
|---------------|------------------|--------------------------|------------------------|
| APP_URL | http://127.0.0.1:8000 | https://feelith.com | https://feelith.com |
| SESSION_DOMAIN | null | null ❌ | .feelith.com ✅ |
| SESSION_SECURE_COOKIE | false | false ❌ | true ✅ |
| SESSION_SAME_SITE | lax | lax | lax |
| Protocolo | HTTP | HTTPS | HTTPS |
| Cookies persisten | ✅ Sí | ❌ No | ✅ Sí |

---

## 🎯 **EXPLICACIÓN TÉCNICA**

### ¿Por qué funciona en local pero no en producción?

**En LOCAL (HTTP):**
```
1. Google OAuth → http://127.0.0.1:8000/auth/google/callback
2. Se crea cookie: karma_session (sin secure)
3. Redirect → http://127.0.0.1:8000/dashboard
4. Cookie se envía automáticamente ✅
5. JavaScript lee session('native_app_token') ✅
```

**En PRODUCCIÓN (HTTPS) - SIN FIX:**
```
1. Google OAuth → https://feelith.com/auth/google/callback
2. Se crea cookie: karma_session (sin secure=true)
3. Redirect → https://feelith.com/dashboard
4. Cookie NO se envía (navegador bloquea cookies inseguras en HTTPS) ❌
5. Nueva sesión, token perdido ❌
6. JavaScript no encuentra token ❌
```

**En PRODUCCIÓN (HTTPS) - CON FIX:**
```
1. Google OAuth → https://feelith.com/auth/google/callback
2. Se crea cookie: karma_session (secure=true, domain=.feelith.com)
3. Redirect → https://feelith.com/dashboard
4. Cookie se envía correctamente ✅
5. JavaScript lee session('native_app_token') ✅
6. Mensaje enviado al React Native app ✅
```

---

## 💡 **TIP: Verificación Manual**

Si quieres ver exactamente qué está pasando:

1. **Abre DevTools (F12) ANTES de hacer login**
2. **Ve a la pestaña Network**
3. **Marca "Preserve log"**
4. **Haz login con Google**
5. **Observa los requests:**
   - `/auth/google/callback` → Debe setear cookie
   - `/dashboard` → Debe enviar la misma cookie
6. **Ve a Console** → Deberías ver los logs de debug

Si ves que `/dashboard` tiene una cookie DIFERENTE a `/callback`, el problema es la configuración de sesión.

---

## ✅ **CHECKLIST FINAL**

Antes de probar, verifica que en **producción**:

- [ ] `.env` tiene `SESSION_DOMAIN=.feelith.com`
- [ ] `.env` tiene `SESSION_SECURE_COOKIE=true`
- [ ] `.env` tiene `SESSION_SAME_SITE=lax`
- [ ] Ejecutaste `php artisan config:clear`
- [ ] Ejecutaste `php artisan config:cache`
- [ ] Reiniciaste PHP-FPM
- [ ] Limpiaste cookies del navegador (Ctrl+Shift+Del)
- [ ] Probaste login de nuevo
- [ ] Abriste DevTools para ver los logs
