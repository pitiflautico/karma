# Comandos para Desplegar en Producción

Ejecuta estos comandos en el servidor (SSH):

```bash
# 1. Ir al directorio del proyecto
cd /ruta/de/tu/proyecto

# 2. Actualizar código
git pull origin main

# 3. Generar claves de Passport (IMPORTANTE - solo primera vez o si no existen)
php artisan passport:keys

# 4. Migrar tablas de Passport (IMPORTANTE - solo primera vez)
php artisan migrate --path=vendor/laravel/passport/database/migrations

# 5. Crear cliente de Passport (IMPORTANTE - solo primera vez)
php artisan passport:client --personal --name="Karma Native App"
# Presiona Enter cuando pregunte por el user provider (usa 'users')

# 6. Instalar/actualizar dependencias
composer install --no-dev --optimize-autoloader

# 7. Publicar assets de Livewire (IMPORTANTE)
php artisan livewire:publish --force

# 8. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 9. Regenerar cachés optimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Migrar base de datos (si hay cambios)
php artisan migrate --force

# 11. Verificar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 12. Reiniciar servicios
sudo systemctl restart php8.2-fpm  # Ajusta la versión de PHP
sudo systemctl restart nginx       # o apache2
```

## Si el error persiste

Verifica que Livewire esté instalado:

```bash
composer show | grep livewire
```

Si no está instalado:

```bash
composer require livewire/livewire
php artisan livewire:publish --force
```

## Verificar configuración de Livewire

Abre `config/livewire.php` y asegúrate que tenga:

```php
'asset_url' => env('ASSET_URL', null),
```

Y en `.env` de producción (si usas CDN o subdirectorio):

```bash
ASSET_URL=https://feelith.com
```

## Quick Fix (solo si tienes acceso SSH ahora)

```bash
cd /ruta/proyecto
php artisan livewire:publish --force
php artisan config:clear
php artisan cache:clear
sudo systemctl restart php8.2-fpm
```
