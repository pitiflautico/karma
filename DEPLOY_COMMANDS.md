# Comandos para Desplegar en Producción

Ejecuta estos comandos en el servidor (SSH):

```bash
# 1. Ir al directorio del proyecto
cd /ruta/de/tu/proyecto

# 2. Actualizar código
git pull origin main

# 3. Generar claves de Passport (IMPORTANTE - solo primera vez o si no existen)
php artisan passport:keys

# 4. Instalar/actualizar dependencias
composer install --no-dev --optimize-autoloader

# 5. Publicar assets de Livewire (IMPORTANTE)
php artisan livewire:publish --force

# 6. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 7. Regenerar cachés optimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Migrar base de datos (si hay cambios)
php artisan migrate --force

# 9. Verificar permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 10. Reiniciar servicios
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
