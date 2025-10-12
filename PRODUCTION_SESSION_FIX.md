# 🔧 Fix: Login con Google no persiste en Producción

## Problema
El login con Google funciona en local pero cuando se despliega a feelith.com, el usuario hace login correctamente pero al redirigir parece no estar autenticado.

## Causa Raíz
Problemas con la configuración de sesión y cookies en producción. Laravel necesita configuración específica para HTTPS y dominios en producción.

## Solución

### 1. Variables de Entorno en Producción

Agrega/modifica estas variables en el archivo `.env` de **PRODUCCIÓN** (feelith.com):

```bash
# URL de la aplicación en producción
APP_URL=https://feelith.com
APP_ENV=production
APP_DEBUG=false

# Configuración de sesión
SESSION_DRIVER=database
SESSION_LIFETIME=10080
SESSION_DOMAIN=.feelith.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Google OAuth - IMPORTANTE: Actualizar la URL de callback
GOOGLE_REDIRECT_URI=https://feelith.com/auth/google/callback
```

### 2. Verificar en Google Cloud Console

Ve a [Google Cloud Console](https://console.cloud.google.com/apis/credentials):

1. Selecciona tu proyecto OAuth
2. Edita el cliente OAuth 2.0
3. En "URIs de redireccionamiento autorizados", asegúrate de tener:
   - `https://feelith.com/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (para desarrollo)

### 3. Comandos a Ejecutar en Producción

```bash
# 1. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Verificar que la tabla sessions existe
php artisan migrate --force

# 3. Regenerar configuración en producción
php artisan config:cache

# 4. Reiniciar el servidor (si usas supervisor/nginx/apache)
# Ejemplo para nginx:
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm  # Ajusta la versión de PHP
```

### 4. Verificación de Configuración

Después de hacer los cambios, verifica la configuración:

```bash
php artisan config:show session
```

Deberías ver:
```
domain .................................................... .feelith.com
secure ......................................................... true
same_site ....................................................... lax
```

## Explicación Técnica

### ¿Por qué funciona en local pero no en producción?

1. **SESSION_SECURE_COOKIE**: En producción usas HTTPS. Laravel necesita saber que las cookies de sesión solo deben enviarse por HTTPS.

2. **SESSION_DOMAIN**: Para que las cookies funcionen correctamente en feelith.com (y subdominios), necesitas especificar el dominio.

3. **SESSION_SAME_SITE=lax**: Permite que las cookies se envíen en redirecciones de Google OAuth (que es cross-site).

### Flujo del Problema

```
❌ SIN FIX:
1. Usuario hace clic en "Login with Google"
2. Google redirige a feelith.com/auth/google/callback
3. Laravel crea la sesión pero la cookie tiene settings incorrectos
4. La cookie no se guarda en el navegador (secure=false en HTTPS)
5. Usuario parece no estar logueado

✅ CON FIX:
1. Usuario hace clic en "Login with Google"
2. Google redirige a feelith.com/auth/google/callback
3. Laravel crea la sesión con settings correctos (secure=true)
4. La cookie se guarda correctamente en el navegador
5. Usuario está logueado correctamente
```

## Diagnóstico Adicional

Si el problema persiste después de aplicar el fix, ejecuta este diagnóstico:

### 1. Revisar Logs de Laravel

```bash
tail -f storage/logs/laravel.log
```

Busca errores relacionados con sesión o autenticación.

### 2. Verificar Headers de Respuesta

Usa las DevTools del navegador (F12) → Network → busca la request a `/auth/google/callback`:

Deberías ver un header `Set-Cookie` como:
```
Set-Cookie: karma_session=...; expires=...; Max-Age=604800; path=/; domain=.feelith.com; secure; HttpOnly; SameSite=lax
```

**Si NO ves `secure` o `domain` correctamente**, la configuración no se aplicó.

### 3. Test Manual de Sesión

Crea una ruta temporal en `routes/web.php`:

```php
Route::get('/test-session', function() {
    session(['test' => 'Hello World']);
    return 'Session set! Refresh to check.';
});

Route::get('/check-session', function() {
    return session('test', 'Session not working!');
});
```

1. Visita `https://feelith.com/test-session`
2. Luego visita `https://feelith.com/check-session`
3. Si ves "Hello World" → sesión funciona
4. Si ves "Session not working!" → hay un problema con la configuración

## Configuración Alternativa (Si Database Sessions Da Problemas)

Si los database sessions siguen dando problemas, prueba con file sessions:

```bash
# En .env de producción
SESSION_DRIVER=file
```

Asegúrate de que el directorio de sesiones tenga permisos correctos:

```bash
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions  # Ajusta el usuario según tu servidor
```

## Configuración Recomendada Final para Producción

```bash
# .env en PRODUCCIÓN (feelith.com)

APP_NAME=Karma
APP_ENV=production
APP_KEY=base64:tu-app-key-de-produccion
APP_DEBUG=false
APP_URL=https://feelith.com

# Database
DB_CONNECTION=mysql  # o el que uses
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=karma_production
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=10080
SESSION_DOMAIN=.feelith.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Cache
CACHE_STORE=redis  # o database
QUEUE_CONNECTION=redis  # o database

# Google OAuth
GOOGLE_CLIENT_ID=tu-client-id
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI=https://feelith.com/auth/google/callback
```

## Checklist de Deployment

- [ ] Actualizar `.env` en producción con las variables correctas
- [ ] Actualizar Google OAuth redirect URI en Google Cloud Console
- [ ] Ejecutar `php artisan config:clear`
- [ ] Ejecutar `php artisan cache:clear`
- [ ] Ejecutar `php artisan config:cache`
- [ ] Ejecutar `php artisan migrate --force`
- [ ] Reiniciar servidor web y PHP-FPM
- [ ] Probar login en navegador incógnito
- [ ] Verificar headers de Set-Cookie en DevTools

## Soporte

Si después de aplicar todos estos pasos el problema persiste:

1. Comparte los logs de `storage/logs/laravel.log`
2. Comparte la salida de `php artisan config:show session`
3. Comparte un screenshot de los headers de la respuesta de `/auth/google/callback`
