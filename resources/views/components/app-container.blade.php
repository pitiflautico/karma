@props(['title' => '', 'subtitle' => '', 'showBackButton' => false, 'backUrl' => null])

<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <div class="px-6 pt-8 pb-6" style="padding-top: max(2rem, env(safe-area-inset-top, 0px) + 2rem);">
        @if($showBackButton)
            <button onclick="if (window.NativeAppBridge) { window.NativeAppBridge.goBack(); } else { window.history.back(); }" class="mb-4 text-gray-700 inline-block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        @endif

        @if($title)
            <h1 class="text-[30px] leading-tight font-bold text-[#533630] mb-2">{{ $title }}</h1>
        @endif

        @if($subtitle)
            <p class="text-[#57534e] text-base font-normal">{{ $subtitle }}</p>
        @endif

        {{ $header ?? '' }}
    </div>

    <!-- Content -->
    <div class="px-6 pb-24" style="padding-bottom: max(6rem, env(safe-area-inset-bottom, 0px) + 6rem);">
        {{ $slot }}
    </div>
</div>
