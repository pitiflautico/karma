@props([
    'type' => 'text',
    'name',
    'placeholder' => '',
    'icon' => null,
    'wireModel' => null,
    'required' => false,
])

<div class="mb-4">
    <div class="relative">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            @if($wireModel)
                wire:model="{{ $wireModel }}"
            @endif
            class="w-full pl-14 pr-4 py-4 border border-gray-200 rounded-full focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-900 placeholder-gray-400 transition-all"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
        >

        @if($icon)
            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @error($name)
        <span class="text-red-500 text-sm ml-4 mt-1 block">{{ $message }}</span>
    @enderror
</div>
