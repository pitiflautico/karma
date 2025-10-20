@props(['color' => '#292524', 'size' => 'w-7 h-7'])

<button onclick="
    if (window.NativeAppBridge && typeof window.NativeAppBridge.goBack === 'function') {
        window.NativeAppBridge.goBack();
    } else if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ url('/dashboard') }}';
    }
" class="inline-flex items-center justify-center" style="color: {{ $color }};">
    <svg class="{{ $size }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
    </svg>
</button>
