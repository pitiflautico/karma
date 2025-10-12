# Diagnóstico de Producción - Login Native App

Ejecuta estos comandos **EN EL SERVIDOR DE PRODUCCIÓN** para diagnosticar el problema:

## 1️⃣ Verificar si Passport está instalado

```bash
# Verificar si las tablas existen
php artisan db:show

# Buscar estas tablas:
# - oauth_access_tokens
# - oauth_auth_codes
# - oauth_clients
# - oauth_refresh_tokens
```

**Si NO existen las tablas:**
```bash
php artisan migrate --path=vendor/laravel/passport/database/migrations
```

---

## 2️⃣ Verificar si las claves de Passport existen

```bash
# Verificar si existen los archivos:
ls -la storage/oauth-*.key

# Deberías ver:
# storage/oauth-private.key
# storage/oauth-public.key
```

**Si NO existen:**
```bash
php artisan passport:keys
```

---

## 3️⃣ Verificar si existe el cliente personal

```bash
# Verificar clientes de Passport
php artisan db:table oauth_clients --take=5

# O con MySQL directo:
mysql -u tu_usuario -p tu_database -e "SELECT * FROM oauth_clients;"
```

**Si NO hay ningún cliente:**
```bash
php artisan passport:client --personal --name="Karma Native App"
```

---

## 4️⃣ Verificar el tipo de user_id en oauth_access_tokens

```bash
# MySQL
mysql -u tu_usuario -p tu_database -e "DESCRIBE oauth_access_tokens;"

# Busca la columna user_id:
# user_id | bigint  ❌ MALO (causará error con UUIDs)
# user_id | varchar ✅ BUENO
```

**Si es bigint, necesitas:**
```bash
# Primero instalar doctrine/dbal
composer require doctrine/dbal

# Luego ejecutar la migración
php artisan migrate
```

---

## 5️⃣ Verificar que nativeApp.js es accesible

```bash
# Verificar que el archivo existe
ls -la public/js/app/nativeApp.js

# Verificar que es accesible vía web (desde tu máquina local)
curl https://feelith.com/js/app/nativeApp.js
```

**Si no existe o da 404:**
```bash
# Asegúrate de que hiciste git pull
git pull origin main

# Verifica que el archivo esté en el repositorio
git ls-files | grep nativeApp.js
```

---

## 6️⃣ Verificar configuración de .env en producción

```bash
# Ver configuración actual
php artisan config:show app

# Verificar estas variables:
# app.url => "https://feelith.com"
# app.debug => false (en producción)
```

**Asegúrate que tu `.env` tenga:**
```env
APP_URL=https://feelith.com
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

---

## 7️⃣ Ver los logs en tiempo real

```bash
# Ver los logs de Laravel en producción
tail -f storage/logs/laravel.log | grep -E "(Google OAuth|Token|native_app|ERROR)"
```

Haz login mientras este comando está corriendo y verás exactamente qué error ocurre.

---

## 8️⃣ Probar el endpoint de debug (temporalmente)

```bash
# Habilitar debug SOLO para este test
php artisan config:clear

# Luego visita desde el navegador:
# https://feelith.com/debug/session-info?key=debug123

# Deberías ver JSON con info de configuración
```

---

## 9️⃣ Verificar permisos de archivos

```bash
# Las claves de Passport necesitan permisos correctos
chmod 600 storage/oauth-*.key
chown www-data:www-data storage/oauth-*.key

# Storage en general
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔟 Limpiar cachés después de cualquier cambio

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Luego cachear de nuevo:
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

---

## ⚡ **Script Completo de Setup (ejecutar TODO de una vez)**

```bash
#!/bin/bash

echo "🚀 Configurando Passport para Native App..."

cd /ruta/de/tu/proyecto

# 1. Actualizar código
echo "📥 Pulling latest code..."
git pull origin main

# 2. Instalar dependencias (incluye doctrine/dbal)
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
composer require doctrine/dbal

# 3. Generar claves de Passport
echo "🔑 Generating Passport keys..."
php artisan passport:keys --force

# 4. Migrar tablas de Passport
echo "🗄️ Creating Passport tables..."
php artisan migrate --path=vendor/laravel/passport/database/migrations --force

# 5. Ejecutar migración de fix para user_id
echo "🔧 Fixing user_id type..."
php artisan migrate --force

# 6. Crear cliente personal
echo "👤 Creating personal access client..."
php artisan passport:client --personal --name="Karma Native App" --provider=users

# 7. Publicar assets de Livewire
echo "📄 Publishing Livewire assets..."
php artisan livewire:publish --force

# 8. Limpiar cachés
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 9. Cachear para producción
echo "⚡ Caching configs..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Permisos
echo "🔒 Setting permissions..."
chmod 600 storage/oauth-*.key
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 11. Reiniciar servicios
echo "🔄 Restarting services..."
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx

echo "✅ Setup completado! Ahora prueba el login."
```

Guarda esto en un archivo `setup-passport.sh` y ejecútalo con:
```bash
chmod +x setup-passport.sh
./setup-passport.sh
```

---

## 🎯 **Lo más probable**

Si en local funciona pero en producción no, el problema es **99% probable** que sea uno de estos:

1. ❌ No ejecutaste `php artisan passport:keys` en producción
2. ❌ No ejecutaste las migraciones de Passport en producción
3. ❌ No ejecutaste `php artisan passport:client --personal` en producción
4. ❌ Falta `doctrine/dbal` en producción (necesario para cambiar el tipo de user_id)

**Solución rápida:**
```bash
composer require doctrine/dbal
php artisan passport:keys
php artisan migrate --path=vendor/laravel/passport/database/migrations
php artisan migrate
php artisan passport:client --personal --name="Karma Native App"
php artisan config:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
```
