@props([
    'title' => '',
    'description' => '',
])

<!-- Title -->
@if($title)
<h2 class="text-2xl font-semibold text-gray-900 text-center mb-4">
    {{ $title }}
</h2>
@endif

<!-- Description -->
@if($description)
<p class="text-gray-600 text-center mb-8 text-sm leading-relaxed">
    {{ $description }}
</p>
@endif

<!-- Content -->
{{ $slot }}

<!-- Footer (optional) -->
@isset($footer)
<div class="mt-8">
    {{ $footer }}
</div>
@endisset
