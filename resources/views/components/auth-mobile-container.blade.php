@props([
    'showBackButton' => false,
    'backUrl' => null,
])

<div class="relative min-h-screen w-full overflow-hidden">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-purple-400 to-blue-600"></div>

    <!-- Radial gradient overlay for the glow effect -->
    <div class="absolute inset-0" style="background: radial-gradient(circle at center top, rgba(255, 192, 203, 0.6) 0%, rgba(138, 196, 255, 0.4) 30%, transparent 60%);"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col min-h-screen">
        @if($showBackButton)
        <!-- Back Button -->
        <div class="pt-8 pl-6">
            <a href="{{ $backUrl ?? route('home') }}" class="text-white text-2xl">
                ←
            </a>
        </div>
        @endif

        <!-- Logo/Title - Centered vertically in remaining space -->
        <div class="flex-1 flex items-center justify-center {{ $showBackButton ? '-mt-16' : '' }}">
            @isset($logo)
                {{ $logo }}
            @else
                <h1 class="text-white text-5xl font-serif">Feelith</h1>
            @endisset
        </div>

        <!-- Bottom Rounded White Section -->
        <div class="relative overflow-hidden">
            <!-- Large rounded white section with wide circular curve -->
            <div class="relative w-[200%] -left-[50%]">
                <div class="bg-white rounded-t-[50%] pt-16 pb-12 px-6">
                    <div class="w-[50%] mx-auto">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
