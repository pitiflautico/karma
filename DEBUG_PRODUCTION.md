# 🔍 Diagnóstico del Problema de Login en Producción

## Paso 1: Desplegar el Código con Rutas de Debug

1. Sube los cambios al servidor de producción (feelith.com)
2. Ejecuta en el servidor:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Paso 2: Verificar Configuración del Servidor

### URL de Diagnóstico 1: Información de Sesión
Visita en tu navegador:
```
https://feelith.com/debug/session-info?key=debug123
```

**¿Qué buscar?**

```json
{
  "session_config": {
    "driver": "database",        // ✅ Debe ser "database" o "file"
    "domain": ".feelith.com",    // ✅ Debe ser ".feelith.com" o "feelith.com"
    "secure": true,              // ✅ Debe ser true (porque usas HTTPS)
    "http_only": true,           // ✅ Debe ser true
    "same_site": "lax",          // ✅ Debe ser "lax" o "none"
    "lifetime": 10080,           // ✅ Duración en minutos
    "cookie_name": "karma_session"
  },
  "server_info": {
    "https": "Yes"               // ✅ Debe ser "Yes"
  }
}
```

**❌ Si ves estos valores, HAY UN PROBLEMA:**
- `"domain": null` → Cookies no funcionarán correctamente
- `"secure": null` o `"secure": false` → Cookies se bloquean en HTTPS
- `"https": "No"` → No estás usando HTTPS (problema de proxy/nginx)

## Paso 3: Test de Persistencia de Sesión

### Test 1: Crear sesión
Visita:
```
https://feelith.com/debug/test-session?key=debug123
```

Verás: "Session value set! Click here to check"

### Test 2: Verificar sesión
Haz clic en el link o visita:
```
https://feelith.com/debug/check-session?key=debug123
```

**Resultado esperado ✅:**
```json
{
  "test_value": "2024-10-12 14:30:45",
  "session_id": "algo123...",
  "message": "Session is working correctly!"
}
```

**Resultado de error ❌:**
```json
{
  "test_value": "Session NOT working!",
  "message": "Session is NOT persisting! Check configuration."
}
```

## Paso 4: Verificar Headers de Cookie (DevTools)

1. Abre DevTools (F12)
2. Ve a la pestaña **Network**
3. Visita `https://feelith.com/debug/test-session?key=debug123`
4. Busca la request en Network
5. Ve a **Headers** → **Response Headers**

**Busca el header `Set-Cookie`:**

✅ **CORRECTO:**
```
Set-Cookie: karma_session=eyJpdiI6...; expires=...; Max-Age=604800; path=/; domain=.feelith.com; secure; HttpOnly; SameSite=lax
```

❌ **INCORRECTO (falta `secure`):**
```
Set-Cookie: karma_session=eyJpdiI6...; expires=...; path=/; HttpOnly; SameSite=lax
```

❌ **INCORRECTO (falta `domain`):**
```
Set-Cookie: karma_session=eyJpdiI6...; expires=...; secure; HttpOnly; SameSite=lax
```

## Paso 5: Soluciones según el Diagnóstico

### Problema 1: `domain: null`

**Solución:** Agrega en `.env` del servidor:
```bash
SESSION_DOMAIN=.feelith.com
```

Luego:
```bash
php artisan config:clear
php artisan config:cache
```

### Problema 2: `secure: null` o `secure: false`

**Solución:** Agrega en `.env` del servidor:
```bash
SESSION_SECURE_COOKIE=true
```

Luego:
```bash
php artisan config:clear
php artisan config:cache
```

### Problema 3: `https: "No"` (pero estás en HTTPS)

Esto significa que Laravel no detecta HTTPS correctamente (problema de proxy/nginx).

**Solución:** Edita `app/Http/Middleware/TrustProxies.php`:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*'; // Confía en todos los proxies

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

O en el `.env`:
```bash
TRUSTED_PROXIES=*
```

### Problema 4: Session database table no existe

**Solución:** Conecta al servidor y ejecuta:
```bash
php artisan migrate --force
```

Verifica que existe la tabla:
```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

## Paso 6: Test de Login Real

Después de aplicar las correcciones:

1. **Limpia cookies del navegador** (importante!)
   - Chrome: DevTools → Application → Cookies → Eliminar todas de feelith.com

2. Visita `https://feelith.com` en **modo incógnito**

3. Haz click en "Login with Google"

4. Completa el login

5. Deberías quedar logueado

## Paso 7: Verificar el Login

Después de hacer login, visita:
```
https://feelith.com/debug/session-info?key=debug123
```

Deberías ver:
```json
{
  "auth_info": {
    "is_authenticated": true,
    "user_id": 123,
    "user_email": "tu@email.com"
  }
}
```

## Configuración Final Recomendada para .env de Producción

```bash
# APP
APP_ENV=production
APP_DEBUG=false
APP_URL=https://feelith.com

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=10080
SESSION_DOMAIN=.feelith.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Google OAuth
GOOGLE_REDIRECT_URI=https://feelith.com/auth/google/callback

# Trust Proxies (si usas nginx/cloudflare)
TRUSTED_PROXIES=*
```

## Comandos para Ejecutar en Servidor

```bash
# 1. Actualizar código
git pull origin main  # o tu branch

# 2. Instalar dependencias (si hay cambios)
composer install --no-dev --optimize-autoloader

# 3. Migrar base de datos
php artisan migrate --force

# 4. Limpiar y regenerar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Reiniciar servicios
sudo systemctl restart php8.2-fpm  # Ajusta versión de PHP
sudo systemctl restart nginx       # o apache2
```

## 🔴 IMPORTANTE: Eliminar Rutas de Debug

Una vez resuelto el problema, **ELIMINA** las rutas de debug de `routes/web.php`:

```php
// Elimina todo este bloque:
Route::get('/debug/session-info', ...);
Route::get('/debug/test-session', ...);
Route::get('/debug/check-session', ...);
```

O cambia la key por algo más seguro.

## Si Nada Funciona: Plan B

Si después de todo esto sigue sin funcionar, prueba cambiar a file sessions:

```bash
# En .env
SESSION_DRIVER=file
```

Y asegura permisos:
```bash
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

## Reporte de Diagnóstico

Una vez que hagas las pruebas, comparte:

1. JSON completo de `/debug/session-info?key=debug123`
2. Screenshot del header `Set-Cookie` en DevTools
3. Resultado de `/debug/check-session?key=debug123`
4. Si hay errores, el contenido de `storage/logs/laravel.log`

Con esa información podré darte una solución exacta.
