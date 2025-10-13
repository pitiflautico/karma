# Fix para Producción - feelith.com

## Problema
Las cookies de sesión no se envían durante el OAuth redirect en WebView de dispositivos físicos.

## Solución

### 1. SSH a tu servidor de producción
```bash
ssh tu-usuario@feelith.com
cd /ruta/a/tu/proyecto/karma
```

### 2. Editar el archivo .env
```bash
nano .env
# o
vim .env
```

### 3. Agregar/Modificar estas líneas:
```env
# Asegúrate que APP_URL esté correcto
APP_URL=https://feelith.com

# Agregar configuración de sesión para WebView
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=none
SESSION_DOMAIN=.feelith.com
```

### 4. Limpiar cache de configuración
```bash
php artisan config:cache
php artisan config:clear
```

### 5. Verificar que nativeApp.js está desplegado
```bash
ls -la public/js/app/nativeApp.js
```

Si no existe, copiarlo desde tu repo local.

### 6. Reiniciar servicios (si usas supervisor/php-fpm)
```bash
# Ejemplo para supervisor
sudo supervisorctl restart all

# O para php-fpm
sudo systemctl restart php8.2-fpm
```

## Por qué funciona esto
- `SESSION_SECURE_COOKIE=true`: Requerido para HTTPS
- `SESSION_SAME_SITE=none`: Permite cookies durante OAuth redirects en WebView
- `SESSION_DOMAIN=.feelith.com`: Cookies funcionan en www y sin www

## Probar
1. Abrir la app en tu iPhone físico
2. Hacer login con Google
3. Revisar los logs en el DebugOverlay
4. Deberías ver: `[NativeApp] Notifying login success`
