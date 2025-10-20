<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <div class="bg-white">
        <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
            <x-back-button />
            <h1 class="text-base font-semibold text-[#292524] truncate flex-1 mx-4">{{ $group['name'] ?? 'Group' }}</h1>
            <!-- Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-2">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden z-50"
                     style="display: none;">
                    <button wire:click="$dispatch('openLeaveModal')"
                            class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="font-medium">Leave Group</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-6 space-y-6">
        <!-- Group Info Card -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center gap-4 mb-4">
                <!-- Group Avatar -->
                <div class="w-16 h-16 rounded-full flex-shrink-0 flex items-center justify-center text-white text-2xl font-bold"
                     style="background-color: {{ $group['color'] ?? '#8B5CF6' }}">
                    @if(isset($group['avatar']) && $group['avatar'])
                        <img src="{{ $group['avatar'] }}" alt="{{ $group['name'] }}" class="w-full h-full rounded-full object-cover">
                    @else
                        {{ substr($group['name'] ?? 'G', 0, 1) }}
                    @endif
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-bold text-[#292524]">{{ $group['name'] ?? 'Group Name' }}</h2>
                    @if(isset($group['description']) && $group['description'])
                        <p class="text-sm text-[#57534e] mt-1">{{ $group['description'] }}</p>
                    @endif
                </div>
            </div>

            <!-- Member Count -->
            <div class="flex items-center gap-2 text-[#57534e]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ $group['member_count'] ?? 0 }} members</span>
            </div>

            <!-- Invite Code -->
            @if(isset($group['invite_code']))
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-[#57534e] mb-2">Invite Code</p>
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                        <span class="text-lg font-bold tracking-wider text-[#292524]">{{ $group['invite_code'] }}</span>
                        <button onclick="navigator.clipboard.writeText('{{ $group['invite_code'] }}')"
                                class="text-[#8B5CF6] hover:text-[#7C3AED] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-2xl p-1 flex gap-1" x-data="{ tab: 'stats' }">
            <button @click="tab = 'stats'"
                    :class="tab === 'stats' ? 'bg-[#8B5CF6] text-white' : 'text-[#57534e]'"
                    class="flex-1 py-2.5 rounded-xl font-medium transition-all">
                Stats
            </button>
            <button @click="tab = 'events'"
                    :class="tab === 'events' ? 'bg-[#8B5CF6] text-white' : 'text-[#57534e]'"
                    class="flex-1 py-2.5 rounded-xl font-medium transition-all">
                Events
            </button>
        </div>

        <!-- Stats Tab -->
        <div x-show="tab === 'stats'" x-transition>
            <!-- Period Selector -->
            <div class="flex gap-2 mb-4">
                @foreach(['24h' => 'Today', '7d' => '7 Days', '30d' => '30 Days'] as $value => $label)
                    <button wire:click="setPeriod('{{ $value }}')"
                            class="flex-1 py-2 rounded-xl text-sm font-medium transition-all
                                {{ $period === $value ? 'bg-[#8B5CF6] text-white' : 'bg-white text-[#57534e]' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Average Mood Card -->
            <div class="bg-white rounded-2xl p-6 text-center">
                <p class="text-sm text-[#57534e] mb-2">Average Group Mood</p>
                @if(isset($stats['average_mood']) && $stats['average_mood'] > 0)
                    <div class="text-5xl mb-2">{{ $stats['mood_emoji'] ?? '😊' }}</div>
                    <p class="text-3xl font-bold text-[#292524]">{{ number_format($stats['average_mood'], 1) }}</p>
                @else
                    <div class="text-5xl mb-2">😶</div>
                    <p class="text-lg text-[#57534e]">No data yet</p>
                @endif
            </div>

            <!-- Activity Today -->
            @if(isset($stats['activity_today']))
                <div class="bg-white rounded-2xl p-6">
                    <h3 class="text-base font-bold text-[#292524] mb-4">Activity Today</h3>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl font-bold text-[#292524]">{{ $stats['activity_today']['members_logged'] }}/{{ $stats['activity_today']['total_members'] }}</span>
                        <span class="text-sm text-[#57534e]">{{ $stats['activity_today']['percentage'] }}% active</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-[#8B5CF6] h-2 rounded-full transition-all"
                             style="width: {{ $stats['activity_today']['percentage'] }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Mood Distribution -->
            @if(isset($stats['mood_distribution']) && count($stats['mood_distribution']) > 0)
                <div class="bg-white rounded-2xl p-6">
                    <h3 class="text-base font-bold text-[#292524] mb-4">Mood Distribution</h3>
                    <div class="space-y-3">
                        @foreach($stats['mood_distribution'] as $dist)
                            @if($dist['count'] > 0)
                                <div class="flex items-center gap-3">
                                    <span class="text-xl w-8">{{ $dist['emoji'] }}</span>
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-[#8B5CF6] h-2 rounded-full transition-all"
                                                 style="width: {{ ($dist['count'] / $stats['total_count']) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-[#57534e] w-8 text-right">{{ $dist['count'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Events Tab -->
        <div x-show="tab === 'events'" x-transition>
            <a href="{{ route('groups.events', $groupId) }}"
               class="block bg-white rounded-2xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-[#8B5CF6]/10 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#292524]">Group Events</h3>
                            <p class="text-sm text-[#57534e]">View and rate events</p>
                        </div>
                    </div>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</div>
