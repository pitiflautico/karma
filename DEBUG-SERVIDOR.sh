#!/bin/bash
# Script para debuggear el problema de sesión en producción

echo "=== 1. VERIFICAR CONFIGURACIÓN DE SESIÓN ==="
php artisan config:show session | grep -E "(same_site|secure|domain)"

echo ""
echo "=== 2. VERIFICAR QUE .ENV TIENE EL CAMBIO ==="
grep SESSION_SAME_SITE .env

echo ""
echo "=== 3. VERIFICAR QUE NATIVEAPP.JS EXISTE ==="
ls -lah public/js/app/nativeApp.js
echo ""
cat public/js/app/nativeApp.js | head -20

echo ""
echo "=== 4. VERIFICAR APP.BLADE.PHP TIENE EL CÓDIGO ==="
grep -A 5 "native_app_login" resources/views/layouts/app.blade.php

echo ""
echo "=== 5. LIMPIAR CACHE COMPLETAMENTE ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache

echo ""
echo "=== 6. VER ÚLTIMAS LÍNEAS DEL LOG ==="
tail -50 storage/logs/laravel.log

echo ""
echo "=== 7. VERIFICAR TABLA DE SESIONES ==="
php artisan tinker --execute="echo 'Sessions table exists: ' . (Schema::hasTable('sessions') ? 'YES' : 'NO');"

echo ""
echo "=== COMPLETADO ==="
echo "Ahora intenta hacer login desde el iPhone y ejecuta:"
echo "tail -f storage/logs/laravel.log | grep -E '(Google|OAuth|native_app|Token)'"
