@props(['title' => '', 'subtitle' => '', 'showBackButton' => false, 'backUrl' => null])

<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <div class="px-6 pt-8 pb-6">
        @if($showBackButton)
            <a href="{{ $backUrl ?? 'javascript:history.back()' }}" class="mb-4 text-gray-700 inline-block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        @if($title)
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $title }}</h1>
        @endif

        @if($subtitle)
            <p class="text-gray-600 text-sm">{{ $subtitle }}</p>
        @endif

        {{ $header ?? '' }}
    </div>

    <!-- Content -->
    <div class="px-6 pb-24">
        {{ $slot }}
    </div>
</div>
