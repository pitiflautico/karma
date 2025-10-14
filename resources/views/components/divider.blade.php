@props([
    'text' => 'OR',
])

<div class="flex items-center my-6">
    <div class="flex-1 border-t border-gray-200"></div>
    <span class="px-4 text-gray-500 text-sm font-medium">{{ $text }}</span>
    <div class="flex-1 border-t border-gray-200"></div>
</div>
