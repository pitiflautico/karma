@props([
    'strength' => 0, // 0-4: 0=None, 1=Weak, 2=Fair, 3=Good, 4=Strong
])

@php
$strengthLabels = [
    0 => '',
    1 => 'Password strength: Weak! Add strength! 💪',
    2 => 'Password strength: Fair! Keep going!',
    3 => 'Password strength: Good! Almost there!',
    4 => 'Password strength: Strong! 💪',
];

$strengthColors = [
    0 => '',
    1 => 'bg-red-400',
    2 => 'bg-yellow-400',
    3 => 'bg-blue-400',
    4 => 'bg-green-500',
];

$label = $strengthLabels[$strength] ?? '';
$color = $strengthColors[$strength] ?? 'bg-gray-200';
@endphp

@if($strength > 0)
<div class="mt-3">
    <!-- Strength bars -->
    <div class="flex gap-2 mb-2">
        @for($i = 1; $i <= 4; $i++)
            <div class="flex-1 h-1 rounded-full {{ $i <= $strength ? $color : 'bg-gray-200' }}"></div>
        @endfor
    </div>

    <!-- Strength label -->
    <p class="text-gray-700 text-sm">
        {{ $label }}
    </p>
</div>
@endif
