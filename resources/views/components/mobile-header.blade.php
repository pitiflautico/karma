@props(['title' => '', 'showBack' => true, 'showMenu' => false, 'menuItems' => []])

<div class="bg-[#F7F3EF]">
    <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
        <!-- Back Button -->
        @if($showBack)
            <x-back-button />
        @else
            <div class="w-10"></div> <!-- Spacer -->
        @endif

        <!-- Title -->
        <h1 class="text-base font-semibold text-[#292524] truncate flex-1 mx-4 text-center">
            {{ $title }}
        </h1>

        <!-- Menu or Spacer -->
        @if($showMenu && count($menuItems) > 0)
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-2">
                    <svg class="w-6 h-6 text-[#292524]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden z-50"
                     style="display: none;">
                    @foreach($menuItems as $item)
                        <button wire:click="{{ $item['action'] }}"
                                class="w-full text-left px-4 py-3 {{ $item['color'] ?? 'text-gray-700' }} hover:bg-gray-50 flex items-center gap-2">
                            @if(isset($item['icon']))
                                {!! $item['icon'] !!}
                            @endif
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <div class="w-10"></div> <!-- Spacer -->
        @endif
    </div>
</div>
