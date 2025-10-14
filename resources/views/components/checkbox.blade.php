@props([
    'name',
    'label',
    'wireModel' => null,
])

<div class="flex items-center">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        class="w-5 h-5 text-purple-600 bg-white border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
    >
    <label for="{{ $name }}" class="ml-3 text-gray-900 text-base font-normal">
        {{ $label }}
    </label>
</div>
