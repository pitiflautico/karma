# Design System Analysis - Figma vs Implementation

## 📊 Estado: Dashboard Mobile

Comparación entre diseño de Figma (node-id=6-22407) e implementación actual.

---

## ✅ Variables de Diseño Globales (BIEN IMPLEMENTADAS)

### Colores Base
```css
/* Ya correctos en el proyecto */
--background: #f7f3ef;  /* Beige/crema claro */
--text-primary: #292524;  /* Casi negro */
--text-secondary: #57534e;  /* Gris oscuro */
--accent: #9bb167;  /* Verde oliva */
--brown-primary: #926247;  /* Marrón */
```

### Tipografía
- **Fuente**: Urbanist ✅
- Ya está configurada globalmente en `layouts/app.blade.php` y `layouts/app-mobile.blade.php`

### Espaciados
- **Padding contenedor**: 16px (px-4) ✅
- **Gap entre secciones**: 32px (space-y-8 = gap-8) ✅
- **Rounded corners**: 24px (rounded-3xl) ✅

---

## ❌ DISCREPANCIAS CRÍTICAS A CORREGIR

### 1. **Hero Section (Top Mood Display)**

#### Figma Specs:
```
Background: Blanco puro (#ffffff) con zona de mood
Mood Name: 36px, weight 700, color #f7f3ef (TEXTO CLARO)
Logged time: 20px, weight 500, color #57534e
Context message: 16px, weight 400, color #57534e
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:95-123 -->
<div class="relative bg-white pb-16">
    ...
    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $moodData['name'] }}</h2>
    <p class="text-sm text-gray-500 mb-1">Logged today at {{ $moodData['logged_time'] }}</p>
    <p class="text-sm text-gray-600">{{ $moodData['message'] }}</p>
</div>
```

#### Correcciones Necesarias:
- ❌ `text-2xl` → `text-[36px]` (título mood)
- ❌ `text-gray-900` → `text-[#f7f3ef]` o `text-[#533630]` (depende del background)
- ❌ `text-sm` (14px) → `text-[20px]` (logged time)
- ❌ `text-sm` (14px) → `text-base` (16px) (context message)

### 2. **Top Navigation Header**

#### Figma Specs:
```
Height: 48px
Title "My Mood": 16px, weight 600, color #292524
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:21 -->
<h1 class="text-lg font-semibold text-gray-900">My Mood</h1>
```

#### Correcciones:
- ❌ `text-lg` (18px) → `text-base` (16px)
- ❌ `text-gray-900` → `text-[#292524]`

### 3. **Floating Add Button**

#### Figma Specs:
```
Size: 56x56px
Background: #926247 (marrón específico)
Icon: Plus white
Shadow: lg
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:133-139 -->
<button class="w-14 h-14 bg-gradient-to-br from-amber-700 to-amber-800 ...">
```

#### Correcciones:
- ✅ Tamaño correcto (w-14 h-14 = 56px)
- ⚠️ Color: `from-amber-700 to-amber-800` → `bg-[#926247]` (usar color exacto)

### 4. **Mood Insight Card**

#### Figma Specs:
```
Background: #f5f5f4 (gris muy claro, NO blanco)
Title "7 days": 24px, weight 700, color #292524
Subtitle "Mood Streak": 16px, weight 500, color #57534e
Description: 14px, weight 400, color #57534e
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:151-160 -->
<div class="bg-white rounded-3xl p-4 flex gap-4 overflow-hidden">
    <h2 class="text-2xl font-bold text-gray-900">{{ $moodStreak }} days</h2>
    <p class="text-base font-medium text-gray-600">Mood Streak</p>
    <p class="text-sm font-normal text-gray-600">You've checked in...</p>
</div>
```

#### Correcciones:
- ❌ `bg-white` → `bg-[#f5f5f4]`
- ✅ `text-2xl` (24px) está correcto
- ⚠️ `text-gray-900` → `text-[#292524]`
- ⚠️ `text-gray-600` → `text-[#57534e]`

### 5. **Section Headers**

#### Figma Specs:
```
Icon: 22x22px, color #a8a29e
Title: 16px, weight 700, color #292524
"See All": 14px, weight 500, color #926247
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:147-149 -->
<h3 class="text-base font-bold text-gray-900">Mood Insight</h3>
<a href="..." class="text-sm font-medium text-gray-500">See All</a>
```

#### Correcciones:
- ⚠️ `text-gray-900` → `text-[#292524]`
- ❌ `text-gray-500` → `text-[#926247]` ("See All" en marrón)

### 6. **Mood History Cards**

#### Figma Specs:
```
Background: white
Mood name: 16px, weight 600, color #292524
Note/Event: 14px, weight 400, color #57534e
Time: 14px, weight 400, color #57534e
Icon size: 48x48px (emoji mood)
```

#### Implementación Actual:
```blade
<!-- dashboard-mobile.blade.php:173-203 -->
<div class="p-4 bg-white rounded-3xl flex items-center gap-3">
    <img src="..." class="w-6 h-6">  <!-- ❌ 24x24 debería ser ~40x40 -->
    <h4 class="text-base font-bold text-gray-900">{{ $mood->mood_name }}</h4>
    <p class="text-sm font-medium text-gray-600">{{ ... }}</p>
    <span class="text-sm font-normal text-gray-600">{{ $mood->created_at->format('g:i A') }}</span>
</div>
```

#### Correcciones:
- ❌ Icono mood: `w-6 h-6` (24px) → `w-10 h-10` (40px) o `w-12 h-12` (48px)
- ⚠️ `text-gray-900` → `text-[#292524]`
- ⚠️ `text-gray-600` → `text-[#57534e]`
- ❌ `font-bold` → `font-semibold` (600)

---

## 🎨 PLAN DE ACCIÓN: Crear Sistema de Variables Compartidas

### Paso 1: Crear archivo de configuración de diseño

**Archivo**: `config/design.php`

```php
<?php

return [
    'colors' => [
        'background' => '#f7f3ef',
        'text' => [
            'primary' => '#292524',
            'secondary' => '#57534e',
            'tertiary' => '#a8a29e',
        ],
        'accent' => '#9bb167',
        'brown' => '#926247',
        'card-bg' => '#f5f5f4',
    ],

    'typography' => [
        'hero-title' => '36px',
        'section-title' => '24px',
        'card-title' => '16px',
        'body' => '16px',
        'small' => '14px',
        'tiny' => '12px',
    ],

    'spacing' => [
        'section-gap' => '32px',
        'card-padding' => '16px',
        'radius-card' => '24px',
    ],
];
```

### Paso 2: Crear componentes Blade reutilizables

#### **Section Header Component**
`resources/views/components/section-header.blade.php`

```blade
@props(['title', 'href' => null])

<div class="flex items-center justify-between h-5">
    <div class="flex items-center gap-2">
        @if(isset($icon))
            <div class="w-[22px] h-[22px] text-[#a8a29e]">
                {{ $icon }}
            </div>
        @endif
        <h3 class="text-base font-bold text-[#292524]">{{ $title }}</h3>
    </div>
    @if($href)
        <a href="{{ $href }}" class="text-sm font-medium text-[#926247]">See All</a>
    @endif
</div>
```

#### **Mood Card Component**
`resources/views/components/mood-card.blade.php`

```blade
@props(['mood'])

<div class="p-4 bg-white rounded-3xl flex items-center gap-3">
    <img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
         alt="{{ $mood->mood_name }}"
         class="w-12 h-12">

    <div class="flex-1 flex flex-col gap-1">
        <h4 class="text-base font-semibold text-[#292524]">{{ $mood->mood_name }}</h4>
        @if($mood->note)
            <p class="text-sm font-normal text-[#57534e] truncate">{{ Str::words($mood->note, 7, '...') }}</p>
        @endif
    </div>

    <div class="flex items-center gap-2">
        <span class="text-sm font-normal text-[#57534e]">{{ $mood->created_at->format('g:i A') }}</span>
        {{ $slot }}
    </div>
</div>
```

### Paso 3: Actualizar Tailwind Config

**Archivo**: `tailwind.config.js` (o inline en layouts)

```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'karma-bg': '#f7f3ef',
                'karma-text': '#292524',
                'karma-text-secondary': '#57534e',
                'karma-text-tertiary': '#a8a29e',
                'karma-accent': '#9bb167',
                'karma-brown': '#926247',
                'karma-card-bg': '#f5f5f4',
            },
            fontFamily: {
                sans: ['Urbanist', 'system-ui', '-apple-system', 'sans-serif'],
            },
        }
    }
}
```

---

## 📝 CHECKLIST DE CORRECCIONES

### Dashboard Mobile (`dashboard-mobile.blade.php`)

- [ ] **Línea 21**: Header title → `text-base text-[#292524]`
- [ ] **Línea 104**: Mood name → `text-[36px] text-[#f7f3ef]` o revisar background
- [ ] **Línea 108**: Logged time → `text-[20px] text-[#57534e]`
- [ ] **Línea 114**: Context message → `text-base text-[#57534e]`
- [ ] **Línea 134**: Botón + → `bg-[#926247]` (quitar gradient)
- [ ] **Línea 148**: Section header → `text-[#292524]`
- [ ] **Línea 149**: "See All" → `text-[#926247]`
- [ ] **Línea 151**: Card insight → `bg-[#f5f5f4]`
- [ ] **Línea 153**: Título "7 days" → `text-[#292524]`
- [ ] **Línea 154-155**: Subtítulos → `text-[#57534e]`
- [ ] **Línea 176**: Icono mood → `w-12 h-12` (48px)
- [ ] **Línea 181**: Mood name → `text-[#292524] font-semibold`
- [ ] **Línea 183, 185, 193**: Textos → `text-[#57534e]`

### Sharing Settings (`sharing-settings.blade.php`)

Ya implementado correctamente ✅ - usar como referencia

---

## 🚀 PRIORIDAD DE IMPLEMENTACIÓN

1. **Alta**: Hero section (tamaños de fuente incorrectos)
2. **Alta**: Colores de texto (usar valores exactos en vez de gray-X)
3. **Media**: Botón flotante (color exacto)
4. **Media**: Card backgrounds (gris claro vs blanco)
5. **Baja**: Tamaños de iconos mood

---

**Última actualización**: 2025-10-20
**Diseño Figma**: node-id=6-22407
