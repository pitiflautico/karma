# Convenciones del Proyecto Karma/Feelith

Este documento describe las convenciones, patrones y mejores prácticas del proyecto.

## Estructura del Proyecto

### Backend (Laravel 11)
```
app/
├── Http/Controllers/
│   ├── Api/                    # Controladores API
│   │   ├── AuthController.php  # Autenticación API
│   │   └── ...
│   └── Auth/
│       └── GoogleAuthController.php
├── Livewire/                   # Componentes Livewire
│   ├── Dashboard.php
│   ├── Home.php
│   └── ...
└── Models/
    ├── User.php
    ├── MoodEntry.php
    └── ...

resources/views/
├── components/                 # Componentes Blade reutilizables
│   ├── input.blade.php
│   ├── button.blade.php
│   └── ...
├── layouts/
│   ├── app.blade.php          # Layout desktop
│   └── app-mobile.blade.php   # Layout mobile
└── livewire/
    ├── home.blade.php         # Vista desktop
    ├── home-mobile.blade.php  # Vista mobile
    ├── dashboard.blade.php
    └── dashboard-mobile.blade.php

routes/
├── web.php                    # Rutas web
├── api.php                    # Rutas API
└── auth.php                   # Rutas de autenticación
```

### Mobile App (React Native + Expo)
```
karma-mobile/
├── app/                       # Expo Router
│   └── (tabs)/
│       └── index.js          # Pantalla principal (WebView)
├── src/
│   ├── components/
│   ├── config/
│   │   └── config.js         # Configuración central
│   ├── screens/
│   │   └── WebViewScreen.js  # WebView principal
│   ├── services/             # Servicios de la app
│   └── hooks/
│       └── useAuth.js        # Hook de autenticación
```

---

## Sistema de Autenticación

### Autenticación Web (Desktop)
- **Login con Google OAuth**
- **Login con Email/Password**
- Sesión basada en cookies de Laravel
- Middleware: `auth` (guard: web)

### Autenticación API (Mobile)
- **Laravel Passport** para tokens
- Tokens almacenados en **SecureStore** (nativo)
- Middleware: `auth:api`

### Flujo de Autenticación Mobile

1. **Login inicial:**
   - Usuario hace login (Google o Email)
   - Backend crea token Passport
   - Token se envía al app nativa vía `postMessage`
   - App guarda token en SecureStore
   - App guarda userId también

2. **Reabrir app (con token guardado):**
   - App detecta token en SecureStore
   - WebView carga: `/auth/session?token={token}`
   - Backend valida token y establece sesión web
   - Redirige a `/dashboard`
   - Usuario ve dashboard directamente

3. **Logout:**
   - Usuario hace click en Logout (menú hamburguesa)
   - Formulario POST a `route('logout')`
   - Script intercepta submit y llama `NativeAppBridge.notifyLogout()`
   - App nativa borra token de SecureStore
   - Backend cierra sesión web
   - Redirige a login

**Código clave:**
- Backend: `AuthController::sessionFromToken()` en `app/Http/Controllers/Api/AuthController.php:119`
- Mobile: `getInitialUrl()` en `app/(tabs)/index.js:305`
- Script logout: `app-mobile.blade.php` línea 184-191

---

## Vistas Mobile vs Desktop

### 🚨 REGLA CRÍTICA: SIEMPRE usar layouts correctos

**TODOS los componentes Livewire DEBEN:**
1. Incluir el método `isMobileDevice()`
2. Usar `->layout('layouts.app-mobile')` para mobile
3. Usar `->layout('layouts.app')` para desktop

**Por qué es importante:**
- `layouts.app` incluye header/footer de navegación desktop
- `layouts.app-mobile` NO incluye header/footer (la app nativa ya tiene su navegación)
- Si usas el layout incorrecto, verás doble header/footer en mobile ❌

### Detección de Mobile
Los componentes Livewire detectan si es mobile con el método:

```php
private function isMobileDevice()
{
    // 1. Session variable
    if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
        return true;
    }

    // 2. Query parameter ?mobile=1
    if (request()->has('mobile') && request()->input('mobile') == '1') {
        session()->put('is_mobile_app', true);
        return true;
    }

    // 3. User-Agent
    $userAgent = request()->header('User-Agent');
    $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }

    return false;
}
```

### Renderizado Condicional - PATRÓN OBLIGATORIO

```php
public function render()
{
    // ... preparar datos ...

    // Detectar mobile y usar layout correcto
    if ($this->isMobileDevice()) {
        return view('livewire.dashboard', [...])
            ->layout('layouts.app-mobile');  // ✅ SIN header/footer
    }

    return view('livewire.dashboard', [...])
        ->layout('layouts.app');  // ✅ CON header/footer
}
```

**Ejemplos de componentes que lo hacen correctamente:**
- `app/Livewire/Auth/AuthHome.php:54-62`
- `app/Livewire/SharingSettings.php:196-255`

**⚠️ NO HACER:**
```php
// ❌ MAL - siempre usa el mismo layout
return view('livewire.dashboard')->layout('layouts.app');
```

**✅ HACER:**
```php
// ✅ BIEN - detecta mobile y usa layout correcto
if ($this->isMobileDevice()) {
    return view('livewire.dashboard')->layout('layouts.app-mobile');
}
return view('livewire.dashboard')->layout('layouts.app');
```

### Diferencias entre layouts

| Feature | `layouts.app` | `layouts.app-mobile` |
|---------|---------------|----------------------|
| Header de navegación | ✅ Sí | ❌ No |
| Footer | ✅ Sí | ❌ No |
| Safe area support | Parcial | ✅ Completo |
| Scroll container | Página | `<main>` con -webkit-overflow-scrolling |
| Uso | Desktop/Web | Mobile app (WebView) |

### Probar en mobile durante desarrollo

Para probar cómo se ve en mobile desde un navegador desktop:
```
http://localhost:8000/sharing-settings?mobile=1
```

El parámetro `?mobile=1` fuerza la detección de mobile y guarda en sesión.

---

## Comunicación WebView ↔ Native App

### De Web a Native

```javascript
if (window.ReactNativeWebView) {
    window.ReactNativeWebView.postMessage(JSON.stringify({
        action: 'loginSuccess',
        userId: '...',
        userToken: '...',
        pushTokenEndpoint: '...'
    }));
}
```

**Acciones disponibles:**
- `loginSuccess`: Usuario inició sesión
- `logout`: Usuario cerró sesión
- `share`: Compartir contenido

### De Native a Web

La app nativa puede:
1. Agregar query parameters a la URL inicial
2. Cargar URLs especiales (ej: `/auth/session?token=...`)
3. Navegar usando `webViewRef.current.navigateToUrl()`

---

## Sistema de Componentes

Ver [`COMPONENT_SYSTEM.md`](./COMPONENT_SYSTEM.md) para detalles completos.

**Componentes disponibles:**
- `<x-label>` - Labels de formulario
- `<x-input>` - Inputs con iconos
- `<x-button>` - Botones con variantes
- `<x-divider>` - Separador con texto
- `<x-checkbox>` - Checkbox con label
- `<x-flash-notification>` - Notificaciones toast (error, success, info, warning)
- `<x-password-strength>` - Indicador de fortaleza de contraseña

**Ejemplo:**
```blade
<x-label>Email Address</x-label>
<x-input
    type="email"
    name="email"
    wireModel="email"
    placeholder="Enter your email..."
    :icon="'<svg>...</svg>'"
    required
/>
```

---

## Rutas Importantes

### Web Routes
```php
Route::get('/', Home::class)->name('home');
Route::get('/auth/session', [AuthController::class, 'sessionFromToken'])->name('auth.session');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // ...
});
```

### API Routes
```php
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/establish-session', [AuthController::class, 'establishSession']);
    // ...
});
```

---

## Configuración Mobile App

**Archivo:** `karma-mobile/src/config/config.js`

```javascript
const config = {
  // URL del backend
  WEB_URL: __DEV__ ? 'http://192.168.86.222:8000' : 'https://feelith.com',

  // App info
  APP_TITLE: 'Feelith',
  APP_SLUG: 'feelith',

  // Features
  FEATURES: {
    PUSH_NOTIFICATIONS: true,
    SHARING: true,
    DEEP_LINKING: true,
  },

  // Auth storage keys
  AUTH_STORAGE_KEYS: {
    USER_ID: 'user_id',
    USER_TOKEN: 'user_token',
    IS_LOGGED_IN: 'is_logged_in',
  },

  // Colors
  COLORS: {
    PRIMARY: '#9333EA',
    SECONDARY: '#7C3AED',
  },
};
```

**Importante:** Actualizar `WEB_URL` según el entorno.

---

## Modelos Principales

### User
```php
$user->id                      // UUID
$user->name
$user->email
$user->calendar_sync_enabled   // Boolean
$user->google_token            // JSON encrypted
```

### MoodEntry
```php
$moodEntry->user_id
$moodEntry->mood_score         // 1-10
$moodEntry->note               // Optional
$moodEntry->calendar_event_id  // Optional
$moodEntry->is_manual          // Boolean
```

### MoodPrompt
```php
$prompt->user_id
$prompt->event_title
$prompt->event_end_time
$prompt->is_completed
$prompt->mood_entry_id         // Once completed
```

---

## Tailwind y Estilos

### Colores del Proyecto
- **Primary:** `purple-600` (#9333EA)
- **Secondary:** `purple-700` (#7C3AED)
- **Success:** `green-500`
- **Error:** `red-500`

### Patrones Comunes

**Gradiente morado:**
```blade
class="bg-gradient-to-r from-purple-600 to-purple-700"
```

**Rounded corners grandes:**
```blade
class="rounded-2xl"      <!-- Tarjetas -->
class="rounded-full"     <!-- Botones/Inputs -->
class="rounded-b-[3rem]" <!-- Header mobile -->
```

**Sombras:**
```blade
class="shadow-sm"    <!-- Sutil -->
class="shadow-lg"    <!-- Mediana -->
class="shadow-xl"    <!-- Fuerte -->
class="shadow-2xl"   <!-- Muy fuerte -->
```

---

## Pantalla Completa en iOS (Dynamic Island / Notch)

### Viewport Meta Tag

El layout mobile **DEBE** incluir `viewport-fit=cover` para ocupar el 100% de la pantalla en iPhone:

```html
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
```

**Ubicación:** `resources/views/layouts/app-mobile.blade.php:5`

### CSS Global para Full Screen

```css
/* Full screen support for iOS */
html, body {
    width: 100%;
    height: 100%;
    position: fixed;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

main {
    width: 100%;
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* iOS Safe Area Support - Extend background to edges */
@supports (padding: env(safe-area-inset-top)) {
    body {
        padding: 0;
    }
}
```

**Ubicación:** `resources/views/layouts/app-mobile.blade.php:34-68`

### Patrón de Vista Full Screen

Para vistas que deben ocupar toda la pantalla (como auth screens):

```blade
<div class="fixed inset-0 w-full h-full overflow-hidden">
    <!-- Background (video, gradient, etc.) -->
    <video class="absolute inset-0 w-full h-full object-cover">
        ...
    </video>

    <!-- Overlay si es necesario -->
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/30"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col h-full">
        <!-- Content goes here -->
    </div>
</div>
```

**Características clave:**
- Usar `fixed inset-0` en lugar de `min-h-screen` para el contenedor principal
- Usar `h-full` en lugar de `min-h-screen` para contenido interno
- El background se extiende hasta los bordes (incluye Dynamic Island)
- El contenido respeta safe areas automáticamente

**Ejemplos:**
- `resources/views/livewire/auth/auth-home.blade.php:1`
- `resources/views/livewire/home-mobile.blade.php:20`

### Variables de Safe Area de iOS

Para casos donde necesites respetar el safe area manualmente:

```css
padding-top: env(safe-area-inset-top);
padding-bottom: env(safe-area-inset-bottom);
padding-left: env(safe-area-inset-left);
padding-right: env(safe-area-inset-right);
```

**Nota:** En general, no es necesario usar estas variables explícitamente ya que el layout global las maneja automáticamente.

---

## Alpine.js

**Incluido en ambos layouts.**

Uso típico:
```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>

    <div x-show="open" @click.away="open = false">
        Content
    </div>
</div>
```

**Llamar métodos Livewire desde Alpine:**
```blade
<button @click="$wire.logout()">Logout</button>
```

---

## Debugging

### Logs Laravel
```bash
tail -f storage/logs/laravel.log
```

### Logs Mobile App
- En consola de Metro: logs automáticos
- En consola del navegador: logs del WebView
- Mensajes con prefijo `[WebView]`, `[HomeScreen]`, `[Auth]`, etc.

### Debug Sessions
```php
\Log::info('[Tag] Message', ['data' => $value]);
```

---

## Testing

### Probar autenticación mobile:
1. Hacer login en el app
2. Cerrar app completamente (swipe up)
3. Reabrir app
4. Debe aparecer dashboard directamente (no login)

### Probar logout:
1. Desde dashboard mobile
2. Click en hamburguesa (arriba derecha)
3. Click en "Logout"
4. Debe mostrar página de login
5. Token debe estar borrado de SecureStore

---

## Comandos Útiles

### Laravel
```bash
php artisan serve --host=0.0.0.0 --port=8000
php artisan route:list
php artisan make:livewire NombreComponente
```

### Mobile App
```bash
npm start                    # Iniciar Metro
npx expo start --clear       # Limpiar cache
npx expo prebuild            # Generar carpetas nativas
```

---

## Estructura de Commits

```
feat: descripción
fix: descripción
refactor: descripción
docs: descripción
```

---

## Referencias Rápidas

- Sistema de componentes: [`COMPONENT_SYSTEM.md`](./COMPONENT_SYSTEM.md)
- Laravel Docs: https://laravel.com/docs/11.x
- Livewire Docs: https://livewire.laravel.com/docs
- Expo Docs: https://docs.expo.dev/
- Tailwind Docs: https://tailwindcss.com/docs

---

**Última actualización:** 2025-10-14
