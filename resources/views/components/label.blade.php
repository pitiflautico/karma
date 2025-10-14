@props([
    'for' => null,
])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'block text-gray-900 text-base font-normal mb-3']) }}
>
    {{ $slot }}
</label>
