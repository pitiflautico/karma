# Sistema de Componentes - Karma/Feelith

Este documento describe el sistema de componentes reutilizables del proyecto y cómo usarlos correctamente.

## Filosofía de Componentes

**SIEMPRE** usa componentes reutilizables en lugar de repetir HTML. Los componentes están en `/resources/views/components/`.

## Tipografía

### Font Family: Urbanist

El proyecto usa la fuente **Urbanist** de Google Fonts como tipografía principal para toda la aplicación (web y mobile).

**Ubicación:** Google Fonts CDN
**Pesos disponibles:** 300, 400, 500, 600, 700, 800, 900

**Configuración:**
- Se carga desde Google Fonts en ambos layouts (`app.blade.php` y `app-mobile.blade.php`)
- Configurada como font-family por defecto en Tailwind CSS
- Fallbacks: `system-ui`, `-apple-system`, `sans-serif`

**Integración en layouts:**
```html
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Urbanist', 'system-ui', '-apple-system', 'sans-serif'],
                },
            }
        }
    }
</script>
```

**Uso en Blade:**
```blade
<!-- La font se aplica automáticamente con font-sans (default) -->
<p class="text-base">This text uses Urbanist</p>

<!-- Puedes especificar explícitamente -->
<p class="font-sans">This also uses Urbanist</p>

<!-- Diferentes pesos -->
<h1 class="font-light">Light (300)</h1>
<p class="font-normal">Normal (400)</p>
<p class="font-medium">Medium (500)</p>
<p class="font-semibold">Semibold (600)</p>
<h2 class="font-bold">Bold (700)</h2>
<h1 class="font-extrabold">Extrabold (800)</h1>
<h1 class="font-black">Black (900)</h1>
```

**Ventajas:**
- Carga rápida desde CDN de Google
- Funciona en web y mobile WebView
- No requiere assets locales
- Soporte completo de pesos tipográficos
- Fallback a fuentes del sistema si falla la carga

---

## Componentes Disponibles

### 1. Label (`<x-label>`)

Etiqueta de formulario con estilos consistentes.

**Uso:**
```blade
<x-label>Email Address</x-label>
<x-label for="password">Password</x-label>
```

**Props:**
- `for` (opcional): ID del input asociado

**Ubicación:** `resources/views/components/label.blade.php`

---

### 2. Input (`<x-input>`)

Campo de entrada con icono opcional y manejo de errores.

**Uso básico:**
```blade
<x-input
    type="email"
    name="email"
    placeholder="Enter your email address..."
    wireModel="email"
    required
/>
```

**Con icono:**
```blade
<x-input
    type="email"
    name="email"
    placeholder="Enter your email address..."
    wireModel="email"
    :icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/></svg>'"
    required
/>
```

**Props:**
- `type`: Tipo de input (text, email, password, etc.)
- `name`: Nombre del campo (requerido)
- `placeholder`: Texto placeholder
- `wireModel`: Nombre de la propiedad Livewire
- `icon`: SVG del icono (se muestra a la izquierda)
- `required`: Boolean para campo requerido

**Características:**
- Validación automática con errores de Livewire
- Icono posicionado a la izquierda
- Estilos consistentes (rounded-full, purple focus)
- Padding izquierdo automático cuando hay icono

**Ubicación:** `resources/views/components/input.blade.php`

---

### 3. Button (`<x-button>`)

Botón con múltiples variantes e iconos opcionales.

**Uso:**
```blade
<!-- Botón primario -->
<x-button
    type="submit"
    variant="primary"
    :icon="'<svg>...</svg>'"
    iconPosition="right"
>
    Sign In
</x-button>

<!-- Botón de Google -->
<x-button
    variant="google"
    :icon="'<svg>...</svg>'"
>
    Sign In With Google
</x-button>

<!-- Botón secundario -->
<x-button
    variant="secondary"
>
    Cancel
</x-button>
```

**Props:**
- `type`: submit, button, etc. (default: 'button')
- `variant`: primary, secondary, google (default: 'primary')
- `icon`: SVG del icono (opcional)
- `iconPosition`: left, right (default: 'left')

**Variantes disponibles:**
- `primary`: Gradiente morado (from-purple-600 to-purple-700)
- `secondary`: Blanco con borde gris
- `google`: Negro (para Sign In With Google)

**Ubicación:** `resources/views/components/button.blade.php`

---

### 4. Divider (`<x-divider>`)

Separador horizontal con texto en el medio (ej: "OR").

**Uso:**
```blade
<x-divider text="OR" />
```

**Props:**
- `text`: Texto a mostrar en el centro

**Ubicación:** `resources/views/components/divider.blade.php`

---

### 5. Checkbox (`<x-checkbox>`)

Checkbox con label y estilos consistentes.

**Uso:**
```blade
<x-checkbox
    name="remember"
    label="Keep me signed in"
    wireModel="remember"
/>
```

**Props:**
- `name`: Nombre del campo (requerido)
- `label`: Texto del label (requerido)
- `wireModel`: Nombre de la propiedad Livewire (opcional)

**Ubicación:** `resources/views/components/checkbox.blade.php`

---

### 6. Flash Notification (`<x-flash-notification>`)

Notificaciones tipo toast para mostrar mensajes de error, éxito, info o advertencia.

**Uso básico:**
```blade
@if (session()->has('error'))
    <x-flash-notification
        type="error"
        message="ERROR: {{ session('error') }}"
        :autoHide="false"
    />
@endif

@if (session()->has('success'))
    <x-flash-notification
        type="success"
        message="{{ session('success') }}"
        :autoHide="true"
        :autoHideDelay="3000"
    />
@endif
```

**Props:**
- `type`: error, success, info, warning (default: 'error')
- `message`: Texto del mensaje (requerido)
- `autoHide`: Boolean para ocultar automáticamente (default: false)
- `autoHideDelay`: Tiempo en milisegundos antes de ocultar (default: 5000)

**Tipos disponibles:**
- `error`: Icono de alerta rojo con borde rojo
- `success`: Icono de check verde con borde verde
- `info`: Icono de información azul con borde azul
- `warning`: Icono de advertencia amarillo con borde amarillo

**Características:**
- Posición fixed en la parte superior de la pantalla
- Animación de entrada/salida con Alpine.js
- Botón X para cerrar manualmente
- Auto-hide opcional con delay personalizable
- Responsive y funciona en mobile y desktop

**Ubicación:** `resources/views/components/flash-notification.blade.php`

---

### 7. Password Strength (`<x-password-strength>`)

Indicador de fortaleza de contraseña con barras de progreso y mensajes.

**Uso básico:**
```blade
<!-- En el formulario de sign up -->
<input
    type="password"
    wire:model.live="registerPassword"
    placeholder="Enter your password...">

<x-password-strength :strength="$passwordStrength" />
```

**Props:**
- `strength`: Nivel de fortaleza (0-4)
  - 0: None (no muestra nada)
  - 1: Weak (rojo) - "Password strength: Weak! Add strength! 💪"
  - 2: Fair (amarillo) - "Password strength: Fair! Keep going!"
  - 3: Good (azul) - "Password strength: Good! Almost there!"
  - 4: Strong (verde) - "Password strength: Strong! 💪"

**Características:**
- 4 barras de progreso que se llenan según el nivel
- Colores que cambian según la fortaleza
- Mensajes motivacionales con emojis
- Cálculo automático en Livewire con `updatedRegisterPassword()`

**Cálculo de fortaleza (en el controller):**
```php
private function calculatePasswordStrength($password)
{
    $strength = 0;

    if (strlen($password) >= 8) $strength++;      // Mínimo 8 caracteres
    if (strlen($password) >= 12) $strength++;     // 12+ caracteres
    if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) $strength++; // Mayúsculas y minúsculas
    if (preg_match('/[0-9]/', $password)) $strength++; // Números
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength++; // Caracteres especiales

    return min($strength, 4);
}

// En el Livewire component:
public function updatedRegisterPassword($value)
{
    $this->passwordStrength = $this->calculatePasswordStrength($value);
}
```

**Ubicación:** `resources/views/components/password-strength.blade.php`

---

### 8. Auth Mobile Container (`<x-auth-mobile-container>`)

Contenedor para pantallas de autenticación mobile con gradiente de fondo y sección blanca redondeada.

**Uso básico:**
```blade
<x-auth-mobile-container>
    <x-auth-card
        title="Forgot Password"
        description="Please enter your email address to reset your password.">

        <!-- Content here -->

    </x-auth-card>
</x-auth-mobile-container>
```

**Con botón de atrás:**
```blade
<x-auth-mobile-container :showBackButton="true" backUrl="{{ route('home') }}">
    <x-auth-card title="Forgot Password" description="Enter your email...">
        <!-- Content here -->
    </x-auth-card>
</x-auth-mobile-container>
```

**Con logo personalizado:**
```blade
<x-auth-mobile-container>
    <x-slot:logo>
        <h1 class="text-white text-5xl font-serif">Feelith</h1>
    </x-slot:logo>

    <x-auth-card>
        <!-- Content -->
    </x-auth-card>
</x-auth-mobile-container>
```

**Props:**
- `showBackButton`: Boolean para mostrar botón de atrás (default: false)
- `backUrl`: URL para el botón de atrás (default: route('home'))
- `logo` (slot): Slot opcional para personalizar el logo

**Características:**
- Gradiente de fondo: from-blue-400 via-purple-400 to-blue-600
- Overlay radial gradient para efecto glow
- Sección blanca redondeada en la parte inferior
- Botón de atrás opcional
- Logo Feelith por defecto

**Ubicación:** `resources/views/components/auth-mobile-container.blade.php`

---

### 9. Auth Card (`<x-auth-card>`)

Tarjeta de contenido para pantallas de autenticación con título, descripción y footer opcional.

**Uso básico:**
```blade
<x-auth-card
    title="Forgot Password"
    description="Please enter your email address to reset your password.">

    <!-- Form fields here -->
    <input type="email" ...>
    <button>Send</button>

</x-auth-card>
```

**Con footer:**
```blade
<x-auth-card
    title="Password Reset Sent"
    description="Check your email for the recovery link.">

    <button>Open My Email</button>

    <x-slot:footer>
        <p class="text-center text-gray-600 text-sm">
            Didn't receive the email?<br>
            Contact us at <a href="mailto:hello@feelith.com">hello@feelith.com</a>
        </p>
    </x-slot:footer>
</x-auth-card>
```

**Props:**
- `title`: Título de la tarjeta (opcional)
- `description`: Descripción debajo del título (opcional)
- `footer` (slot): Slot opcional para contenido en el footer

**Características:**
- Título centrado con text-2xl
- Descripción centrada con text-sm
- Contenido principal flexible
- Footer opcional

**Ubicación:** `resources/views/components/auth-card.blade.php`

---

### 10. Auth Home with Video (`AuthHome` Livewire Component)

Pantalla de inicio de autenticación con video loop de fondo y botones de login.

**Ubicación:**
- Component: `app/Livewire/Auth/AuthHome.php`
- View: `resources/views/livewire/auth/auth-home.blade.php`
- Video: `public/videos/video_loop.mp4`

**Uso en rutas:**
```php
Route::get('/', \App\Livewire\Auth\AuthHome::class)->name('home');
```

**Características:**
- Video en loop de fondo (autoplay, muted, playsinline)
- Logo "Feelith" centrado
- Sección blanca redondeada en la parte inferior
- Botón "Sign In With Google"
- Botón "Sign In With Email" → redirige a `/sign-in-mail`
- Link "Sign Up" → redirige a `/sign-in-mail`
- Detecta mobile/desktop automáticamente
- Redirige a dashboard si el usuario ya está logueado

**Estructura de la vista:**
```blade
<div class="relative min-h-screen w-full overflow-hidden">
    <!-- Background Video Loop -->
    <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ asset('videos/video_loop.mp4') }}" type="video/mp4">
    </video>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/30"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col min-h-screen">
        <div class="flex-1 flex items-center justify-center">
            <h1 class="text-white text-6xl font-serif">Feelith</h1>
        </div>

        <!-- Bottom Rounded White Section -->
        <div class="relative overflow-hidden">
            <div class="relative w-[200%] -left-[50%]">
                <div class="bg-white rounded-t-[50%] pt-16 pb-8 px-6">
                    <div class="w-[50%] mx-auto">
                        <!-- Buttons here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## Iconos Comunes

Para mantener consistencia, aquí están los SVG de iconos más usados:

### Email Icon
```blade
:icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/></svg>'"
```

### Password Icon (Lock)
```blade
:icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'/></svg>'"
```

### User Icon
```blade
:icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'/></svg>'"
```

### Arrow Right Icon
```blade
:icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7l5 5m0 0l-5 5m5-5H6\'/></svg>'"
```

### Google Icon (Multi-color)
```blade
:icon="'<svg class=\'w-6 h-6\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path fill=\'#EA4335\' d=\'M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z\'/><path fill=\'#34A853\' d=\'M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z\'/><path fill=\'#4A90E2\' d=\'M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5818182 23.1818182,9.90909091 L12,9.90909091 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z\'/><path fill=\'#FBBC05\' d=\'M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z\'/></svg>'"
```

---

## Ejemplo Completo: Modal de Login

```blade
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Sign In</h2>

    <form wire:submit="login">
        <!-- Email Address -->
        <x-label>Email Address</x-label>
        <x-input
            type="email"
            name="email"
            wireModel="email"
            placeholder="Enter your email address..."
            :icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\'/></svg>'"
            required
        />

        <!-- Password -->
        <x-label>Password</x-label>
        <x-input
            type="password"
            name="password"
            wireModel="password"
            placeholder="Enter your password..."
            :icon="'<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'/></svg>'"
            required
        />

        <!-- Keep me signed in & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <x-checkbox name="remember" label="Keep me signed in" />
            <a href="#" class="text-purple-600 text-sm font-medium hover:text-purple-700">
                Forgot Password
            </a>
        </div>

        <!-- Sign In Button -->
        <x-button
            type="submit"
            variant="primary"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7l5 5m0 0l-5 5m5-5H6\'/></svg>'"
            iconPosition="right"
        >
            Sign In
        </x-button>

        <!-- Divider -->
        <x-divider text="OR" />

        <!-- Sign In with Google -->
        <x-button
            variant="google"
            :icon="'<svg class=\'w-6 h-6\' viewBox=\'0 0 24 24\'>...</svg>'"
        >
            Sign In With Google
        </x-button>
    </form>
</div>
```

---

## Reglas Importantes

1. **NUNCA duplicar HTML**: Siempre usa componentes existentes
2. **Mantén iconos consistentes**: Usa los SVG de este documento
3. **Props con dos puntos**: Para pasar HTML (iconos), usa `:icon="'...'"`
4. **wireModel**: Usa esta prop para conectar con Livewire
5. **Variantes de botones**:
   - `primary` para acciones principales
   - `secondary` para cancelar
   - `google` solo para Sign In With Google

---

## Añadir Nuevos Componentes

Si necesitas crear un nuevo componente:

1. Créalo en `/resources/views/components/nombre.blade.php`
2. Define sus `@props` al inicio
3. Documéntalo aquí con ejemplos
4. Úsalo con `<x-nombre>`

---

## Vista Mobile vs Desktop

- **Mobile**: Usa `layouts/app-mobile.blade.php`
- **Desktop**: Usa `layouts/app.blade.php`

Los componentes funcionan igual en ambas vistas.

---

## Colores del Proyecto

- **Primary**: Purple-600 (#9333EA)
- **Secondary**: Purple-700 (#7C3AED)
- **Background**: White
- **Text**: Gray-900 (primary), Gray-500 (secondary)
- **Error**: Red-500
- **Success**: Green-500

---

### 11. App Container (`<x-app-container>`)

Contenedor principal para vistas mobile con fondo consistente (#F7F3EF) y estructura común.

**Uso básico:**
```blade
<x-app-container
    title="Mood History"
    subtitle="Browse your mood history here"
    :showBackButton="true">

    <!-- Content here -->

</x-app-container>
```

**Con header personalizado:**
```blade
<x-app-container
    title="Settings"
    subtitle="Manage your preferences"
    :showBackButton="true">

    <x-slot:header>
        <!-- Custom header content -->
        <div class="mt-4">
            <!-- Tabs, buttons, etc. -->
        </div>
    </x-slot:header>

    <!-- Main content -->
    <p>Settings content goes here...</p>
</x-app-container>
```

**Props:**
- `title` (opcional): Título de la página
- `subtitle` (opcional): Subtítulo descriptivo
- `showBackButton` (boolean, default: false): Mostrar botón de atrás
- `backUrl` (opcional): URL personalizada para el botón (default: history.back())
- `header` (slot opcional): Contenido adicional en el header

**Características:**
- Fondo beige claro (`#F7F3EF`)
- Header blanco con padding consistente
- Botón de atrás opcional
- Slot para header personalizado
- Padding inferior para navegación flotante

**Ubicación:** `resources/views/components/app-container.blade.php`

---

### 12. Swipeable Card (`<x-swipeable-card>`)

Tarjeta con funcionalidad de deslizar hacia la izquierda para revelar botón de borrar.

**Uso:**
```blade
@foreach($items as $item)
    <x-swipeable-card :deleteId="$item->id">
        <!-- Card content -->
        <div class="flex items-center justify-between">
            <span>{{ $item->title }}</span>
            <span>{{ $item->time }}</span>
        </div>
    </x-swipeable-card>
@endforeach
```

**Props:**
- `deleteId` (requerido): ID del elemento a borrar (se pasa a wire:click)

**Características:**
- Swipe solo hacia la izquierda (no permite swipe derecho)
- Snap automático: si deslizas >50px, se abre; sino, se cierra
- Distancia máxima: 100px
- Botón de borrar rojo detrás de la tarjeta
- Transición suave con Alpine.js
- Touch gestures nativos

**Funcionalidad:**
- Al deslizar hacia la izquierda, aparece un botón de papelera rojo
- Al soltar, si el deslizamiento fue >50px, la tarjeta queda abierta mostrando el botón
- Hacer clic en la papelera ejecuta `confirmDelete(deleteId)`

**Ubicación:** `resources/views/components/swipeable-card.blade.php`

---

### 13. Mood Card (`<x-mood-card>`)

Tarjeta de contenido para mostrar un mood entry con icono, información y acciones.

**Uso:**
```blade
<x-mood-card :mood="$moodEntry" />
```

**Props:**
- `mood` (requerido): Objeto MoodEntry con sus relaciones

**Muestra:**
- Icono del mood (SVG) según el score
- Nombre del mood (Depressed, Sad, Happy, etc.)
- Nota del usuario o título del evento de calendario
- Warning "Consult with your doctor" si el mood es ≤3
- Hora del mood entry
- Botón de arrow para editar

**Características:**
- Layout horizontal con icono a la izquierda
- Truncate de texto para notas largas
- Warning visual para moods bajos
- Botón de edición con wire:click

**Ubicación:** `resources/views/components/mood-card.blade.php`

---

### 14. Delete Confirmation Modal (`<x-delete-confirmation-modal>`)

Modal de confirmación para operaciones de eliminación con ilustración personalizada y diseño bottom-sheet.

**Uso básico:**
```blade
<x-delete-confirmation-modal
    :show="$showDeleteConfirm"
    title="Delete Mood Entry?"
    message="This action cannot be undone."
    confirmText="Yes ✓"
    cancelText="Cancel"
    onConfirm="deleteMood"
    onCancel="cancelDelete"
/>
```

**Con textos personalizados:**
```blade
<x-delete-confirmation-modal
    :show="$showModal"
    title="Remove from Calendar?"
    message="Are you sure you want to remove this event from your calendar?"
    confirmText="Remove ✓"
    cancelText="Keep It"
    onConfirm="removeEvent"
    onCancel="closeModal"
/>
```

**Props:**
- `show` (boolean, requerido): Controla la visibilidad del modal
- `title` (string, default: 'Delete Mood Entry?'): Título del modal
- `message` (string, default: 'This action cannot be undone.'): Mensaje descriptivo
- `confirmText` (string, default: 'Yes ✓'): Texto del botón de confirmación
- `cancelText` (string, default: 'Cancel'): Texto del botón de cancelar
- `onConfirm` (string, requerido): Método Livewire a ejecutar al confirmar
- `onCancel` (string, requerido): Método Livewire a ejecutar al cancelar

**Características:**
- Diseño bottom-sheet (aparece desde abajo)
- Backdrop negro semi-transparente con click para cerrar
- Ilustración personalizada (`delete_popup_art.png`)
- Botón de confirmación con gradiente rosa llamativo
- Botón de cancelar blanco con borde y texto rojo
- Animaciones suaves con Alpine.js (x-transition)
- Responsive con max-width para pantallas grandes

**Estructura visual:**
1. Ilustración en la parte superior (mujer con bolsa de compras y gesto de espera)
2. Título en negrita centrado
3. Mensaje descriptivo centrado
4. Dos botones apilados verticalmente:
   - Confirmar (rosa con degradado)
   - Cancelar (blanco con texto rojo)

**Ubicación:** `resources/views/components/delete-confirmation-modal.blade.php`

---

## Iconos de Moods

**Ubicación:** `public/images/moods/`

Los iconos de moods son SVG personalizados con fondo circular de colores y expresiones faciales. Ya incluyen el color de fondo circular, **NO añadas círculos adicionales** al usarlos.

### Archivos disponibles:

| Archivo | Mood | Color | Rango Score | Hex Color |
|---------|------|-------|-------------|-----------|
| `depressed_icon.svg` | Depressed | Morado | 1-2 | `#C084FC` |
| `Sad_icon.svg` | Sad | Naranja | 3-4 | `#FB923C` |
| `Normal_icon.svg` | Normal/Neutral | Marrón | 5-6 | `#B1865E` |
| `Happy_icon.svg` | Happy | Amarillo | 7-8 | `#FBBF24` |
| `Great_icon.svg` | Overjoyed/Great | Verde | 9-10 | `#9BB167` |

### Uso en Blade:

```blade
<!-- En List View -->
<img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
     alt="{{ $mood->mood_name }}"
     class="w-12 h-12">

<!-- En Calendar View -->
<img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
     alt="{{ $mood->mood_name }}"
     class="w-10 h-10">
```

### Atributos del modelo MoodEntry:

El modelo `MoodEntry` tiene atributos computados para obtener los iconos y colores:

```php
$mood->mood_icon          // Devuelve: 'depressed_icon.svg', 'Sad_icon.svg', etc.
$mood->mood_name          // Devuelve: 'Depressed', 'Sad', 'Neutral', 'Happy', 'Overjoyed'
$mood->mood_color         // Devuelve: '#C084FC', '#FB923C', etc. (hex color)
$mood->mood_color_class   // Devuelve: 'bg-[#C084FC]', 'bg-[#FB923C]', etc.
```

### Mapeo Score → Icon:

- **1-2**: `depressed_icon.svg` (Morado #C084FC)
- **3-4**: `Sad_icon.svg` (Naranja #FB923C)
- **5-6**: `Normal_icon.svg` (Marrón #B1865E)
- **7-8**: `Happy_icon.svg` (Amarillo #FBBF24)
- **9-10**: `Great_icon.svg` (Verde #9BB167)

### Características de los SVG:

- Tamaño: 104x104px (viewBox)
- Incluyen drop shadow filter
- Ya tienen fondo circular con color
- No necesitan contenedor circular adicional
- Son completamente vectoriales y escalables

### ❌ NO hacer:

```blade
<!-- MAL: No añadas círculo de fondo -->
<div class="w-12 h-12 rounded-full bg-purple-400">
    <img src="{{ asset('images/moods/depressed_icon.svg') }}" class="w-10 h-10">
</div>
```

### ✅ Hacer:

```blade
<!-- BIEN: El icono ya tiene su círculo de fondo -->
<div class="w-12 h-12">
    <img src="{{ asset('images/moods/depressed_icon.svg') }}" class="w-12 h-12">
</div>
```

---

### 15. Mood Detail Modal (`MoodDetailModal` Livewire Component)

Modal bottom-sheet para ver el detalle completo de un mood entry en modo solo lectura, con opción de editar.

**Ubicación:**
- Component: `app/Livewire/MoodDetailModal.php`
- View: `resources/views/livewire/mood-detail-modal.blade.php`

**Uso en vista:**
```blade
<!-- Incluir el componente en la vista -->
@livewire('mood-detail-modal')
```

**Abrir el modal desde otro componente:**
```php
// En un componente Livewire
public function viewMood($moodId)
{
    $this->dispatch('openMoodDetailModal', moodId: $moodId);
}
```

**Características:**
- Modal bottom-sheet que aparece desde abajo
- Muestra el icono del mood (SVG grande, 96x96px)
- Título con el nombre del mood (Depressed, Happy, etc.)
- Fecha y hora del mood entry
- Información del evento de calendario asociado (si existe)
- Nota completa del usuario
- Warning para moods bajos que requieren consulta médica
- Dos botones de acción:
  - "Edit Mood Entry" (gradiente morado) → abre el modal de edición
  - "Close" (blanco con borde)
- Backdrop semi-transparente con click para cerrar
- Animaciones suaves con Alpine.js

**Eventos escuchados:**
- `openMoodDetailModal` → Abre el modal con el mood especificado

**Eventos emitidos:**
- `openMoodEntryModal` → Abre el modal de edición al hacer clic en "Edit"

**Métodos públicos:**
```php
openModal($moodId)     // Abre el modal y carga el mood
closeModal()           // Cierra el modal y limpia los datos
editMood()             // Cierra este modal y abre el de edición
```

**Estructura visual:**
1. Botón X en la esquina superior derecha
2. Icono del mood centrado (grande)
3. Nombre del mood en negrita
4. Fecha y hora
5. Bloque azul del evento de calendario (si existe)
6. Sección "Note" con la nota completa o "No notes added"
7. Warning amarillo para moods bajos (si aplica)
8. Botones de acción apilados

**Ejemplo de flujo:**
```blade
<!-- En mood-history-mobile.blade.php -->
<x-mood-card :mood="$mood" />

<!-- mood-card.blade.php tiene un botón: -->
<button wire:click="editMood('{{ $mood->id }}')">→</button>

<!-- MoodHistory.php ejecuta: -->
public function editMood($moodId) {
    $this->dispatch('openMoodDetailModal', moodId: $moodId);
}

<!-- MoodDetailModal escucha el evento y abre el modal -->
```

**Integración:**
- Se incluye en `mood-history-mobile.blade.php`
- El componente `MoodCard` dispara el evento para abrirlo
- Si el usuario hace clic en "Edit", se abre `MoodEntryForm` automáticamente

---

---

## Layout y Espaciado en Mobile App

### Regla del Menú Flotante

**IMPORTANTE:** La aplicación móvil tiene un menú de navegación flotante en la parte inferior de la pantalla. **TODOS** los contenidos y modales deben dejar espacio para este menú.

**Constantes de configuración** (en `/karma-mobile/src/config/config.js`):

```javascript
LAYOUT: {
  // Altura a reservar en la parte inferior del WebView para el tab bar
  TAB_BAR_HEIGHT: 100,

  // Margen inferior para modales/popups que aparecen desde abajo
  MODAL_BOTTOM_MARGIN: 90,
}
```

### Aplicación en diferentes componentes:

#### 1. WebView (pantallas web embebidas)

El componente `WebViewScreen` ya tiene el padding configurado:

```javascript
// src/screens/WebViewScreen.js
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: config.COLORS.BACKGROUND,
    paddingBottom: config.LAYOUT.TAB_BAR_HEIGHT,
  },
});
```

**Todas las pantallas que usan WebView automáticamente respetan este espacio:**
- Home (index.js)
- Calendar (calendar.js)
- Profile (profile.js)

#### 2. ScrollView y pantallas nativas

Para pantallas con ScrollView u otros contenedores nativos, añade padding bottom:

```javascript
// Ejemplo: SettingsScreen.js
const styles = StyleSheet.create({
  content: {
    padding: 20,
    paddingBottom: config.LAYOUT.TAB_BAR_HEIGHT + 20, // + padding original
  },
});
```

#### 3. Modales tipo bottom-sheet

Los modales que aparecen desde abajo necesitan:
- **marginBottom** en el contenedor del modal
- **Backdrop** que no cubra el área del menú
- **Bottom spacer** con el color de fondo de la app

```javascript
// Ejemplo: new.js
return (
  <View style={styles.container}>
    {/* Backdrop - no cubre el área del tab bar */}
    <TouchableOpacity
      style={styles.backdrop}
      activeOpacity={1}
      onPress={handleClose}
    />

    {/* Spacer con color de fondo de la app */}
    <View style={styles.bottomSpacer} />

    {/* Modal Content */}
    <View style={styles.modalContainer}>
      {/* ... */}
    </View>
  </View>
);

const styles = StyleSheet.create({
  backdrop: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: config.LAYOUT.MODAL_BOTTOM_MARGIN,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  bottomSpacer: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    height: config.LAYOUT.MODAL_BOTTOM_MARGIN,
    backgroundColor: config.COLORS.BACKGROUND,
  },
  modalContainer: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    paddingBottom: 40,
    marginBottom: config.LAYOUT.MODAL_BOTTOM_MARGIN,
  },
});
```

### ¿Por qué es necesario?

El menú flotante está posicionado de forma absoluta sobre el contenido. Si no dejamos espacio:
- Los botones y contenido quedan ocultos detrás del menú
- Los usuarios no pueden hacer clic en elementos tapados
- El diseño se ve mal con elementos cortados

### ¿Dónde NO es necesario?

El menú flotante **NO se muestra** en:
- Pantalla de onboarding (cuando `isOnboarding === true`)
- Cuando el usuario no está logueado (`isLoggedIn === false`)

En estos casos, el espacio reservado no afecta porque el menú no existe.

### Color del espacio reservado

El espacio reservado debe tener el **mismo color de fondo que la app** (blanco por defecto).

**❌ NO hacer:**
```javascript
// MAL: El backdrop oscuro cubre todo incluyendo el área del menú
backdrop: {
  bottom: 0, // Esto hace que el fondo oscuro tape el menú
  backgroundColor: 'rgba(0, 0, 0, 0.5)',
}
```

**✅ Hacer:**
```javascript
// BIEN: El backdrop se detiene antes del menú, y un spacer blanco ocupa el espacio
backdrop: {
  bottom: config.LAYOUT.MODAL_BOTTOM_MARGIN, // Se detiene antes del menú
  backgroundColor: 'rgba(0, 0, 0, 0.5)',
},
bottomSpacer: {
  bottom: 0,
  height: config.LAYOUT.MODAL_BOTTOM_MARGIN,
  backgroundColor: config.COLORS.BACKGROUND, // Blanco
}
```

---

**Última actualización:** 2025-10-15
