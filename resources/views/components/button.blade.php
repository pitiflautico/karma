@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, google
    'icon' => null,
    'iconPosition' => 'left', // left or right
    'fullWidth' => true,
])

@php
$baseClasses = 'flex items-center justify-center font-medium py-4 px-6 rounded-full transition-all duration-200 shadow-lg';

$variantClasses = [
    'primary' => 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-900 border border-gray-200',
    'google' => 'bg-black hover:bg-gray-900 text-white',
];

$widthClass = $fullWidth ? 'w-full' : '';
$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $widthClass;
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($icon && $iconPosition === 'left')
        <span class="mr-3">
            {!! $icon !!}
        </span>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right')
        <span class="ml-3">
            {!! $icon !!}
        </span>
    @endif
</button>
