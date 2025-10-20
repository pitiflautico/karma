# 📦 Catálogo de Componentes Blade - Karma

**Fecha:** 20 de Octubre, 2025
**Proyecto:** Karma - Mood Tracking App

Este documento lista todos los componentes Blade reutilizables disponibles en el proyecto.

---

## 🎨 Componentes de Layout

### 1. **app-container**
**Ubicación:** `resources/views/components/app-container.blade.php`

**Propósito:** Contenedor principal para páginas de la aplicación con márgenes y padding responsivos.

**Uso:**
```blade
<x-app-container>
    <!-- Contenido de la página -->
</x-app-container>
```

**Props:**
- Slot por defecto para el contenido

---

### 2. **auth-card**
**Ubicación:** `resources/views/components/auth-card.blade.php`

**Propósito:** Card para pantallas de autenticación (login, register, etc.)

**Uso:**
```blade
<x-auth-card>
    <!-- Formulario de autenticación -->
</x-auth-card>
```

---

### 3. **auth-mobile-container**
**Ubicación:** `resources/views/components/auth-mobile-container.blade.php`

**Propósito:** Contenedor mobile-first para pantallas de autenticación con safe areas.

**Uso:**
```blade
<x-auth-mobile-container>
    <!-- Contenido de auth -->
</x-auth-mobile-container>
```

---

## 🧭 Componentes de Navegación

### 4. **back-button**
**Ubicación:** `resources/views/components/back-button.blade.php`

**Propósito:** Botón para volver a la página anterior con soporte para apps nativas.

**Uso:**
```blade
<x-back-button />

<!-- Con URL personalizada -->
<x-back-button :url="route('dashboard')" />
```

**Props:**
- `url` (opcional): URL a la que volver. Por defecto usa `back()`

**Features:**
- Detecta si está en app nativa (iOS/Android)
- Usa navegación nativa si está disponible
- Fallback a navegación web

---

## 📝 Componentes de Formulario

### 5. **button**
**Ubicación:** `resources/views/components/button.blade.php`

**Propósito:** Botón estilizado con variantes y estados.

**Uso:**
```blade
<x-button>
    Enviar
</x-button>

<x-button variant="secondary">
    Cancelar
</x-button>

<x-button :loading="$loading">
    Guardar
</x-button>
```

**Props:**
- `variant`: `primary` (default), `secondary`, `danger`
- `loading`: Boolean para mostrar spinner
- `disabled`: Boolean para deshabilitar
- Todos los atributos HTML estándar

---

### 6. **input**
**Ubicación:** `resources/views/components/input.blade.php`

**Propósito:** Input de texto estilizado con estados de error.

**Uso:**
```blade
<x-input
    type="text"
    name="email"
    placeholder="tu@email.com"
    :value="old('email')"
/>
```

**Props:**
- `type`: Tipo de input (text, email, password, etc.)
- `name`: Nombre del campo
- `placeholder`: Texto placeholder
- `value`: Valor inicial
- `disabled`: Boolean
- `required`: Boolean

---

### 7. **label**
**Ubicación:** `resources/views/components/label.blade.php`

**Propósito:** Label para inputs con estilo consistente.

**Uso:**
```blade
<x-label for="email">
    Email
</x-label>
```

**Props:**
- `for`: ID del input asociado
- Slot para el texto del label

---

### 8. **checkbox**
**Ubicación:** `resources/views/components/checkbox.blade.php`

**Propósito:** Checkbox estilizado.

**Uso:**
```blade
<x-checkbox
    name="remember"
    id="remember"
    :checked="old('remember')"
/>
```

**Props:**
- `name`: Nombre del campo
- `id`: ID del checkbox
- `checked`: Boolean o valor
- `disabled`: Boolean

---

### 9. **password-strength**
**Ubicación:** `resources/views/components/password-strength.blade.php`

**Propósito:** Indicador visual de fortaleza de contraseña.

**Uso:**
```blade
<x-password-strength :password="$password" />
```

**Props:**
- `password`: String de la contraseña a evaluar

**Features:**
- Barra de progreso con colores (rojo -> amarillo -> verde)
- Texto descriptivo (Débil, Media, Fuerte)
- Validación en tiempo real

---

## 🎴 Componentes de Contenido

### 10. **mood-card**
**Ubicación:** `resources/views/components/mood-card.blade.php`

**Propósito:** Card para mostrar información de un mood entry.

**Uso:**
```blade
<x-mood-card
    :mood="$moodEntry"
    :show-date="true"
/>
```

**Props:**
- `mood`: Objeto MoodEntry
- `show-date`: Boolean para mostrar fecha
- `compact`: Boolean para versión compacta

**Features:**
- Muestra emoji del mood
- Muestra score numérico
- Muestra trigger si existe
- Muestra nota si existe
- Formato de fecha relativo

---

### 11. **swipeable-card**
**Ubicación:** `resources/views/components/swipeable-card.blade.php`

**Propósito:** Card que se puede deslizar para revelar acciones (ej: eliminar).

**Uso:**
```blade
<x-swipeable-card
    delete-action="deleteItem({{ $item->id }})"
    delete-text="¿Eliminar este elemento?"
>
    <!-- Contenido de la card -->
</x-swipeable-card>
```

**Props:**
- `delete-action`: Método Livewire a ejecutar al confirmar
- `delete-text`: Texto de confirmación
- Slot para el contenido principal

**Features:**
- Swipe gesture para revelar botón eliminar
- Modal de confirmación integrado
- Animaciones suaves
- Touch-friendly

---

## 🔔 Componentes de UI/Feedback

### 12. **flash-notification**
**Ubicación:** `resources/views/components/flash-notification.blade.php`

**Propósito:** Notificación toast para mensajes de éxito/error.

**Uso:**
```blade
<x-flash-notification />

<!-- En controller/Livewire: -->
session()->flash('success', '¡Guardado correctamente!');
session()->flash('error', 'Algo salió mal');
```

**Features:**
- Auto-desaparece después de 3 segundos
- Variantes: success (verde), error (rojo), info (azul)
- Animación de entrada/salida
- Posición fixed superior

---

### 13. **delete-confirmation-modal**
**Ubicación:** `resources/views/components/delete-confirmation-modal.blade.php`

**Propósito:** Modal de confirmación para acciones destructivas.

**Uso:**
```blade
<x-delete-confirmation-modal
    :show="$showModal"
    title="¿Eliminar entrada?"
    message="Esta acción no se puede deshacer"
    confirm-action="deleteEntry"
    cancel-action="cancelDelete"
/>
```

**Props:**
- `show`: Boolean (Livewire property)
- `title`: Título del modal
- `message`: Mensaje descriptivo
- `confirm-action`: Método Livewire para confirmar
- `cancel-action`: Método Livewire para cancelar
- `confirm-text`: Texto del botón confirmar (default: "Eliminar")
- `cancel-text`: Texto del botón cancelar (default: "Cancelar")

**Features:**
- Overlay oscuro
- Botones con colores apropiados (rojo para confirmar)
- Animación de entrada/salida
- Responsive

---

### 14. **divider**
**Ubicación:** `resources/views/components/divider.blade.php`

**Propósito:** Línea divisoria horizontal con texto opcional.

**Uso:**
```blade
<x-divider />

<!-- Con texto -->
<x-divider text="O" />

<x-divider text="Continuar con" />
```

**Props:**
- `text` (opcional): Texto a mostrar en el centro
- Slot para contenido personalizado

**Features:**
- Línea gris clara
- Texto centrado con fondo
- Márgenes verticales automáticos

---

## 📐 Patrones de Diseño

### Colores Principales
```css
- Background: #F7F3EF (beige claro)
- Primario: #8B5CF6 (morado)
- Éxito: #10B981 (verde)
- Error: #EF4444 (rojo)
- Advertencia: #F59E0B (naranja/amarillo)
```

### Safe Areas (iOS/Android)
Todos los componentes mobile respetan las safe areas usando:
```css
padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);
padding-bottom: max(1rem, env(safe-area-inset-bottom, 0px) + 1rem);
```

### Animaciones
- Transiciones suaves: `transition-all duration-200`
- Entrada de modales: `fade-in` + `scale-up`
- Hover states: Ligeramente más oscuro/claro

---

## 🎯 Cómo Usar Componentes

### Sintaxis Básica
```blade
<!-- Sin props -->
<x-component-name />

<!-- Con props -->
<x-component-name :prop="$variable" />

<!-- Con slot -->
<x-component-name>
    Contenido aquí
</x-component-name>

<!-- Props dinámicos vs estáticos -->
<x-button variant="primary">         <!-- Estático (string) -->
<x-button :variant="$type">          <!-- Dinámico (variable) -->
<x-button :disabled="$isDisabled">   <!-- Dinámico (boolean) -->
```

### Crear Nuevo Componente
```bash
php artisan make:component NombreComponente
```

Esto crea:
- Clase PHP: `app/View/Components/NombreComponente.php`
- Vista: `resources/views/components/nombre-componente.blade.php`

---

## 📚 Componentes Pendientes (Sugerencias)

Componentes que podrían ser útiles crear:

- [ ] **stat-card**: Card para mostrar estadísticas (usado en dashboard)
- [ ] **empty-state**: Ilustración + texto para estados vacíos
- [ ] **loading-spinner**: Spinner de carga reutilizable
- [ ] **avatar**: Avatar de usuario con iniciales/imagen
- [ ] **badge**: Badge para etiquetas/estados
- [ ] **tabs**: Sistema de pestañas reutilizable
- [ ] **dropdown**: Menú desplegable
- [ ] **tooltip**: Tooltips informativos
- [ ] **progress-bar**: Barra de progreso
- [ ] **skeleton**: Placeholders mientras carga

---

## 🔄 Componentes Livewire vs Blade

### Componentes Blade (Presentacionales)
- **Ubicación:** `resources/views/components/`
- **Propósito:** Solo presentación, sin lógica
- **Uso:** `<x-component-name />`
- **Ejemplos:** button, input, label, divider

### Componentes Livewire (Con Estado)
- **Ubicación:** `app/Livewire/` + `resources/views/livewire/`
- **Propósito:** Componentes interactivos con lógica
- **Uso:** `<livewire:component-name />` o `@livewire('component-name')`
- **Ejemplos:** Calendar, MoodLogger, GroupsList

---

**💡 Tip:** Siempre preferir componentes Blade para UI estática y Livewire solo cuando necesites interactividad o estado del servidor.

---

**Última actualización:** 20 de Octubre, 2025
