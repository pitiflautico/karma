# Sistema de Onboarding - Karma/Feelith

Este documento describe el sistema de onboarding completo del proyecto, incluyendo todos los pasos, componentes y funcionalidad.

## Descripción General

El sistema de onboarding guía al usuario a través de 7 pasos para recopilar información inicial antes de entrar al dashboard. Los datos se almacenan en la sesión durante el proceso y se guardan en la base de datos al completar.

## Arquitectura

### Flujo del Onboarding

```
Home → Step 1 → Step 2 → Step 3 → Step 4 → Step 5 → Step 6 → Step 7 → Complete → Dashboard
```

### Tecnologías Utilizadas

- **Backend**: Laravel + Livewire 3
- **Frontend**: Blade Templates + Alpine.js
- **Estilos**: Tailwind CSS
- **Detección de Dispositivo**: Custom Headers + User-Agent
- **Almacenamiento**: Session → Database

---

## Pasos del Onboarding

### Step 1: Welcome / Nombre
**Ruta**: `/onboarding/step-1`
**Componente**: `App\Livewire\Onboarding\Step1`
**Vistas**:
- Desktop: `livewire.onboarding.step1`
- Mobile: `livewire.onboarding.step1-mobile`

**Campos**:
- `name` (string, required): Nombre del usuario

**Validación**:
```php
'name' => 'required|string|max:255'
```

**Sesión**:
```php
session(['onboarding.name' => $this->name]);
```

**Navegación**:
- Siguiente: Step 2
- Atrás: No disponible (primer paso)
- Skip: No disponible

---

### Step 2: Género
**Ruta**: `/onboarding/step-2`
**Componente**: `App\Livewire\Onboarding\Step2`
**Vistas**:
- Desktop: `livewire.onboarding.step2`
- Mobile: `livewire.onboarding.step2-mobile`

**Campos**:
- `gender` (string, required): 'male', 'female', 'other', 'prefer_not_to_say'

**Validación**:
```php
'gender' => 'required|in:male,female,other,prefer_not_to_say'
```

**Sesión**:
```php
session(['onboarding.gender' => $this->gender]);
```

**Navegación**:
- Siguiente: Step 3
- Atrás: Step 1
- Skip: Disponible (guarda null)

**Diseño Mobile**:
- 4 opciones con iconos SVG
- Selección visual con borde morado
- Opción seleccionada resaltada

---

### Step 3: Fecha de Nacimiento
**Ruta**: `/onboarding/step-3`
**Componente**: `App\Livewire\Onboarding\Step3`
**Vistas**:
- Desktop: `livewire.onboarding.step3`
- Mobile: `livewire.onboarding.step3-mobile`

**Campos**:
- `birthDate` (date, required): Fecha en formato YYYY-MM-DD

**Validación**:
```php
'birthDate' => 'required|date|before:today'
```

**Sesión**:
```php
session(['onboarding.birthDate' => $this->birthDate]);
```

**Navegación**:
- Siguiente: Step 4
- Atrás: Step 2
- Skip: Disponible

**Implementación Mobile**:
- 3 pickers iOS-style: Mes, Día, Año
- Rango de años: 1924-2024
- Sincronización con Livewire usando `wire:ignore` + `window.Livewire.find()`
- Manejo especial para evitar conflictos entre Alpine.js y Livewire

**Detalles Técnicos**:
```javascript
// Sincronización manual con Livewire
window.Livewire.find(this.$el.closest('[wire\\:id]').getAttribute('wire:id'))
    .set('birthDate', this.formattedDate);
```

---

### Step 4: Objetivo
**Ruta**: `/onboarding/step-4`
**Componente**: `App\Livewire\Onboarding\Step4`
**Vistas**:
- Desktop: `livewire.onboarding.step4`
- Mobile: `livewire.onboarding.step4-mobile`

**Campos**:
- `goal` (string, required): Objetivo del usuario

**Opciones**:
- Perder peso
- Ganar músculo
- Mantener peso
- Mejorar salud
- Otro

**Validación**:
```php
'goal' => 'required|string'
```

**Sesión**:
```php
session(['onboarding.goal' => $this->goal]);
```

**Navegación**:
- Siguiente: Step 5
- Atrás: Step 3
- Skip: Disponible

---

### Step 5: Estado de Ánimo Inicial
**Ruta**: `/onboarding/step-5`
**Componente**: `App\Livewire\Onboarding\Step5`
**Vistas**:
- Desktop: `livewire.onboarding.step5`
- Mobile: `livewire.onboarding.step5-mobile`

**Campos**:
- `moodLevel` (integer, required): Nivel de ánimo 1-5

**Validación**:
```php
'moodLevel' => 'required|integer|min:1|max:5'
```

**Sesión**:
```php
session(['onboarding.moodLevel' => $this->moodLevel]);
```

**Navegación**:
- Siguiente: Step 6
- Atrás: Step 4
- Skip: Disponible (guarda null)

**Moods Disponibles**:
1. **Deprimido** (level: 1) - Color: #C084FC - Icon: depressed_icon.svg
2. **Mal** (level: 2) - Color: #FB923C - Icon: Sad_icon.svg
3. **Normal** (level: 3) - Color: #B1865E - Icon: Normal_icon.svg
4. **Feliz** (level: 4) - Color: #FBBF24 - Icon: Happy_icon.svg
5. **Genial** (level: 5) - Color: #9BB167 - Icon: Great_icon.svg

**Diseño Mobile**:
- Icono grande centrado (264x264px) sin fondo
- Texto descriptivo: "Me siento [mood]."
- 5 iconos pequeños (56x56px) en contenedor blanco redondeado
- Selección visual: icono seleccionado a full opacity, no seleccionados con grayscale + opacity-40

**Características**:
- Default: mood level 3 (Normal)
- Actualización en tiempo real del icono grande
- Sincronización automática con Livewire

---

### Step 6: Peso
**Ruta**: `/onboarding/step-6`
**Componente**: `App\Livewire\Onboarding\Step6`
**Vistas**:
- Desktop: `livewire.onboarding.step6`
- Mobile: `livewire.onboarding.step6-mobile`

**Campos**:
- `weight` (numeric, required): Peso del usuario
- `unit` (string, required): 'kg' o 'lbs'

**Validación**:
```php
'weight' => 'required|numeric|min:20|max:500',
'unit' => 'required|in:kg,lbs'
```

**Sesión**:
```php
session([
    'onboarding.weight' => $this->weight,
    'onboarding.unit' => $this->unit
]);
```

**Navegación**:
- Siguiente: Step 7
- Atrás: Step 5
- Skip: Disponible (guarda null + default unit)

**Implementación Mobile - Draggable Ruler**:

El selector de peso mobile usa un concepto único de "regla arrastrable":
- **Indicador verde fijo** en el centro (no se mueve)
- **Regla arrastrable** detrás que se desliza horizontalmente
- El valor que queda bajo el indicador verde es el peso seleccionado

**Detalles Técnicos**:
```javascript
// Configuración
pixelsPerUnit: 10,  // Cada marca de la regla = 10px
minWeight: 20,      // kg
maxWeight: 200,     // kg (o 440 para lbs)

// Cálculo del offset
offset = -(weight - minWeight) * pixelsPerUnit

// Al arrastrar, calcular peso
calculatedWeight = minWeight - (offset / pixelsPerUnit)
```

**Conversión kg ↔ lbs**:
- kg → lbs: `weight * 2.20462`
- lbs → kg: `weight / 2.20462`
- Al cambiar unidad, se recalcula offset automáticamente

**Touch Events**:
- `touchstart`: Captura posición inicial
- `touchmove`: Actualiza offset y peso en tiempo real
- `touchend`: Redondea a entero y sincroniza con Livewire

**Estructura HTML**:
```html
<!-- Indicador verde fijo en el centro -->
<div class="absolute left-1/2 top-0 w-1 h-20 bg-[#8BC34A]"></div>

<!-- Regla arrastrable -->
<div :style="`transform: translateX(${offset}px)`">
    <!-- Marcas cada 10px -->
    <template x-for="i in (maxWeight - minWeight + 1)">
        <div style="width: 10px;">
            <!-- Marca larga cada 10 unidades -->
            <div :class="(i % 10 === 0) ? 'h-16' : 'h-8'"></div>
        </div>
    </template>
</div>
```

**Desktop Version**:
- Input numérico simple con unidad al lado
- Toggle kg/lbs
- Validación min/max
- Estilos consistentes con otros pasos

---

### Step 7: Altura
**Ruta**: `/onboarding/step-7`
**Componente**: `App\Livewire\Onboarding\Step7`
**Vistas**:
- Desktop: `livewire.onboarding.step7`
- Mobile: `livewire.onboarding.step7-mobile`

**Campos**:
- `height` (numeric, required): Altura del usuario
- `unit` (string, required): 'cm' o 'inch'

**Validación**:
```php
'height' => 'required|numeric|min:50|max:300',
'unit' => 'required|in:cm,inch'
```

**Sesión**:
```php
session([
    'onboarding.height' => $this->height,
    'onboarding.unit_height' => $this->unit
]);
```

**Navegación**:
- Siguiente: Complete page
- Atrás: Step 6
- Skip: Disponible (guarda null + default unit)

**Implementación Mobile - iOS-style Picker**:

Selector tipo rueda iOS con una sola columna:
- **Rango**: 100-250 cm (o equivalente en inches)
- **Default**: 170 cm
- **Visual**: 7 valores visibles, seleccionado en el centro con fondo morado claro

**Características**:
- Scroll suave con inercia
- Snap a valores
- Conversión automática al cambiar unidad
- Toggle cm/inch con botones redondeados

**Conversión cm ↔ inch**:
- cm → inch: `cm / 2.54`
- inch → cm: `inch * 2.54`

**Desktop Version**:
- Input numérico simple
- Toggle cm/inch
- Validación min/max
- Diseño consistente

---

### Complete: Página de Celebración
**Ruta**: `/onboarding/complete`
**Componente**: `App\Livewire\Onboarding\Complete`
**Vistas**:
- Desktop: `livewire.onboarding.complete`
- Mobile: `livewire.onboarding.complete-mobile`

**Funcionalidad**:
1. Muestra imagen de celebración
2. Muestra progreso con stepper (3 pasos completados)
3. Botón "Empezar" marca onboarding como completado
4. Redirige al dashboard

**Acción al Completar**:
```php
public function start()
{
    // Marca onboarding como completado
    auth()->user()->update(['onboarding_completed' => true]);

    // Redirige al dashboard
    return redirect()->route('dashboard');
}
```

**IMPORTANTE**:
- Se usa `onboarding_completed` (boolean), NO `onboarding_completed_at` (timestamp)
- Esto coincide con el middleware que verifica el estado del onboarding

**Diseño Mobile**:
- Imagen de celebración: `/images/celebration.png` (320x320px)
- Badge "gracias" con icono de pulgar arriba
- Progress stepper mostrando:
  - Welcome ✓
  - Info Personal ✓
  - Empezar ✓
- Título: "Muchas gracias ya lo tenemos todo!"
- Descripción: "Ahora puedes empezar a disfrutar de todas las funcionalidades."
- Botón morado "Empezar"

---

## Detección de Dispositivo

El sistema detecta si el usuario está en mobile o desktop para mostrar la vista correcta.

### Métodos de Detección (en orden de prioridad)

1. **Custom Header** (más confiable para apps nativas):
   ```php
   $isNativeApp = request()->header('X-Native-App') === 'true';
   ```

2. **Query Parameter** (para testing):
   ```php
   $isMobile = request()->has('mobile');
   ```

3. **User-Agent** (fallback):
   ```php
   $userAgent = request()->header('User-Agent');
   $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod'];
   ```

### Implementación en React Native

En el WebView de React Native, se envía el header custom:

```javascript
<WebView
    source={{
        uri: webUrl,
        headers: {
            'X-Native-App': 'true'
        }
    }}
    // ... other props
/>
```

**Archivos relacionados**:
- `/Users/danielperezpinazo/Projects/karma-mobile/src/screens/WebViewScreen.js`
- `/Users/danielperezpinazo/Projects/karma-mobile/app/(tabs)/index.js`

### Renderizado de Vista

Todos los componentes de onboarding usan el mismo patrón:

```php
public function render()
{
    $isNativeApp = request()->header('X-Native-App') === 'true';
    $isMobile = request()->has('mobile');

    if (!$isMobile && !$isNativeApp) {
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    $isMobile = true;
                    break;
                }
            }
        }
    }

    if ($isMobile || $isNativeApp) {
        return view('livewire.onboarding.stepX-mobile')->layout('layouts.app-mobile');
    }

    return view('livewire.onboarding.stepX')->layout('layouts.app');
}
```

---

## Rutas

Las rutas del onboarding están definidas en `/routes/web.php`:

```php
Route::middleware(['auth'])->prefix('onboarding')->group(function () {
    Route::get('/step-1', \App\Livewire\Onboarding\Step1::class)->name('onboarding.step1');
    Route::get('/step-2', \App\Livewire\Onboarding\Step2::class)->name('onboarding.step2');
    Route::get('/step-3', \App\Livewire\Onboarding\Step3::class)->name('onboarding.step3');
    Route::get('/step-4', \App\Livewire\Onboarding\Step4::class)->name('onboarding.step4');
    Route::get('/step-5', \App\Livewire\Onboarding\Step5::class)->name('onboarding.step5');
    Route::get('/step-6', \App\Livewire\Onboarding\Step6::class)->name('onboarding.step6');
    Route::get('/step-7', \App\Livewire\Onboarding\Step7::class)->name('onboarding.step7');
    Route::get('/complete', \App\Livewire\Onboarding\Complete::class)->name('onboarding.complete');
});
```

**IMPORTANTE**:
- Todas las rutas requieren autenticación (`auth` middleware)
- Los usuarios son redirigidos automáticamente al onboarding si `onboarding_completed` es `false`

---

## Middleware de Onboarding

El middleware verifica si el usuario ha completado el onboarding:

```php
// En el middleware RedirectIfOnboardingIncomplete
if (!auth()->user()->onboarding_completed) {
    return redirect()->route('onboarding.step1');
}
```

**Campo en la base de datos**:
- Tabla: `users`
- Campo: `onboarding_completed` (boolean, default: false)

---

## Almacenamiento de Datos

### Durante el Proceso (Session)

Los datos se guardan en la sesión con el prefijo `onboarding.`:

```php
session([
    'onboarding.name' => 'Juan',
    'onboarding.gender' => 'male',
    'onboarding.birthDate' => '1990-01-01',
    'onboarding.goal' => 'Perder peso',
    'onboarding.moodLevel' => 3,
    'onboarding.weight' => 70,
    'onboarding.unit' => 'kg',
    'onboarding.height' => 170,
    'onboarding.unit_height' => 'cm'
]);
```

### Al Completar (Database)

Al hacer clic en "Empezar" en la página Complete:
1. Se marca `onboarding_completed = true`
2. Los datos de la sesión deberían guardarse en el perfil del usuario
3. Se redirige al dashboard

**TODO**: Implementar guardado de datos de sesión a base de datos en el método `start()` de Complete.php

---

## Archivos del Sistema

### Backend (Livewire Components)

```
app/Livewire/Onboarding/
├── Step1.php
├── Step2.php
├── Step3.php
├── Step4.php
├── Step5.php
├── Step6.php
├── Step7.php
└── Complete.php
```

### Frontend (Blade Views)

```
resources/views/livewire/onboarding/
├── step1.blade.php
├── step1-mobile.blade.php
├── step2.blade.php
├── step2-mobile.blade.php
├── step3.blade.php
├── step3-mobile.blade.php
├── step4.blade.php
├── step4-mobile.blade.php
├── step5.blade.php
├── step5-mobile.blade.php
├── step6.blade.php          ← Recién creado
├── step6-mobile.blade.php
├── step7.blade.php
├── step7-mobile.blade.php
├── complete.blade.php
└── complete-mobile.blade.php
```

### Assets

```
public/images/moods/
├── depressed_icon.svg
├── Sad_icon.svg
├── Normal_icon.svg
├── Happy_icon.svg
└── Great_icon.svg

public/images/
└── celebration.png
```

---

## Layouts

### Mobile Layout
**Archivo**: `resources/views/layouts/app-mobile.blade.php`
- Full screen
- Sin navegación
- Optimizado para touch
- Estilos específicos mobile

### Desktop Layout
**Archivo**: `resources/views/layouts/app.blade.php`
- Centrado con max-width
- Gradiente de fondo
- Card blanca con sombra
- Estilos responsive

---

## Estilos y Diseño

### Colores Principales

```css
/* Purple Primary */
--purple-600: #7C4DFF;
--purple-hover: #6A3DE8;

/* Green Accent (para indicadores) */
--green-accent: #8BC34A;
--green-moods: #9BB167;

/* Background */
--bg-cream: #F5F1EB;          /* Mobile background */
--bg-gradient: from-purple-50 to-blue-50;  /* Desktop */

/* Text */
--text-primary: #1F2937;      /* Gray-900 */
--text-secondary: #6B7280;    /* Gray-600 */
```

### Componentes Comunes

#### Progress Bar
```blade
<div class="w-full bg-gray-300 rounded-full h-2">
    <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300"
         style="width: {{ ($currentStep / 7) * 100 }}%">
    </div>
</div>
```

#### Button (Mobile)
```blade
<button
    wire:click="saveAndContinue"
    class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
    Siguiente
</button>
```

#### Back Button (Mobile)
```blade
<button wire:click="back" class="text-gray-700 text-2xl">←</button>
```

#### Skip Button (Mobile)
```blade
<button wire:click="skip" class="text-gray-600 text-base font-medium hover:text-gray-800">
    Skip
</button>
```

---

## Problemas Resueltos

### 1. Date Picker no sincronizaba con Livewire
**Problema**: Los pickers de fecha funcionaban visualmente pero no enviaban datos al backend.

**Solución**:
- Agregar `wire:ignore` al contenedor de Alpine.js
- Usar `window.Livewire.find()` para sincronización manual
- Evitar conflictos entre Alpine y Livewire

### 2. Mobile UI no se mostraba en app nativa
**Problema**: User-Agent no era confiable en WebView.

**Solución**:
- Implementar header custom `X-Native-App: true`
- Priorizar header sobre User-Agent
- Actualizar todos los componentes

### 3. Weight Ruler no coordinaba con número
**Problema**: El valor mostrado no coincidía con la posición de la regla.

**Solución**:
- Estandarizar: 10px por unidad (`pixelsPerUnit: 10`)
- Actualizar peso en tiempo real durante drag
- Redondear solo al soltar

### 4. Onboarding Loop después de completar
**Problema**: Usuario redirigido al onboarding después de completar.

**Solución**:
- Cambiar de `onboarding_completed_at` a `onboarding_completed`
- Asegurar que el campo coincide con el middleware

### 5. Mood Selector no coincidía con diseño
**Problema**: Iconos tenían fondos circulares no deseados.

**Solución**:
- Remover todos los fondos
- Mostrar SVG directo sin contenedor circular
- Aplicar grayscale + opacity a no seleccionados

---

## Testing

### URLs de Testing Mobile

Para forzar vista mobile en desktop, agregar `?mobile=1`:

```
http://localhost:8000/onboarding/step-1?mobile=1
http://localhost:8000/onboarding/step-5?mobile=1
http://localhost:8000/onboarding/complete?mobile=1
```

### Logging

Todos los componentes tienen logging para debugging:

```php
\Log::info('[Step5] Mounting Step5 component');
\Log::info('[Step5] saveAndContinue called', ['moodLevel' => $this->moodLevel]);
\Log::info('[Step5] Validation passed, saving to session');
\Log::info('[Step5] Redirecting to step6');
```

**Ver logs**:
```bash
tail -f storage/logs/laravel.log
```

---

## Deployment

### Comandos de Deployment

```bash
# En el servidor de producción
cd /path/to/project
git pull origin main
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Verificar permisos de assets
chmod -R 755 public/images
chmod -R 755 public/videos

# Verificar que los archivos existen
ls -la public/images/celebration.png
ls -la public/images/moods/
```

### Checklist de Deployment

- [ ] Push commits a git
- [ ] Pull en servidor
- [ ] Clear all caches
- [ ] Verificar assets existen
- [ ] Verificar permisos correctos
- [ ] Probar en mobile nativo
- [ ] Verificar redirects funcionan
- [ ] Confirmar datos se guardan en sesión
- [ ] Verificar onboarding_completed funciona

---

## Próximas Mejoras

1. **Guardar datos en base de datos**: Implementar guardado de todos los datos de sesión al perfil del usuario
2. **Animaciones de transición**: Agregar transiciones suaves entre pasos
3. **Validación en tiempo real**: Mostrar errores mientras el usuario escribe
4. **Progress saving**: Permitir retomar onboarding desde donde se quedó
5. **Analytics**: Tracking de abandono en cada paso
6. **A/B Testing**: Diferentes versiones de copy y diseño
7. **Internacionalización**: Soporte para múltiples idiomas

---

## Recursos Adicionales

- **Livewire Docs**: https://livewire.laravel.com/docs
- **Alpine.js Docs**: https://alpinejs.dev/
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Component System**: Ver `COMPONENT_SYSTEM.md`
- **Project Architecture**: Ver `PROJECT_ARCHITECTURE.md`

---

**Última actualización**: 2025-10-14
**Autor**: Claude Code
**Version**: 1.0
