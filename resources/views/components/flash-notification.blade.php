@props([
    'type' => 'error', // error, success, info, warning
    'message' => '',
    'autoHide' => false,
    'autoHideDelay' => 5000, // milliseconds
])

@php
$colors = [
    'error' => [
        'bg' => 'bg-white',
        'border' => 'border-red-200',
        'icon' => 'text-red-500',
        'text' => 'text-gray-900',
    ],
    'success' => [
        'bg' => 'bg-white',
        'border' => 'border-green-200',
        'icon' => 'text-green-500',
        'text' => 'text-gray-900',
    ],
    'info' => [
        'bg' => 'bg-white',
        'border' => 'border-blue-200',
        'icon' => 'text-blue-500',
        'text' => 'text-gray-900',
    ],
    'warning' => [
        'bg' => 'bg-white',
        'border' => 'border-yellow-200',
        'icon' => 'text-yellow-500',
        'text' => 'text-gray-900',
    ],
];

$typeColors = $colors[$type] ?? $colors['error'];

$icons = [
    'error' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
    'success' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
    'info' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    'warning' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
];

$icon = $icons[$type] ?? $icons['error'];
@endphp

<div
    x-data="{
        show: true,
        init() {
            @if($autoHide)
            setTimeout(() => {
                this.show = false;
            }, {{ $autoHideDelay }});
            @endif
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-2"
    class="fixed top-4 left-4 right-4 z-50 mx-auto max-w-md"
>
    <div class="{{ $typeColors['bg'] }} {{ $typeColors['border'] }} border-2 rounded-2xl shadow-lg px-5 py-4 flex items-center gap-4">
        <!-- Icon -->
        <div class="{{ $typeColors['icon'] }} flex-shrink-0">
            {!! $icon !!}
        </div>

        <!-- Message -->
        <div class="{{ $typeColors['text'] }} flex-1 font-medium text-base">
            {{ $message }}
        </div>

        <!-- Close Button -->
        <button
            @click="show = false"
            class="text-gray-400 hover:text-gray-600 flex-shrink-0 transition-colors"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
