<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-mobile-header
        :title="$group->name ?? 'Group'"
        :show-menu="true"
        :menu-items="[
            [
                'label' => 'Leave Group',
                'action' => '$dispatch(\'openLeaveModal\')',
                'color' => 'text-red-600',
                'icon' => '<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1\'/></svg>'
            ]
        ]"
    />

    <!-- Content -->
    <div class="px-6 py-6 space-y-6" x-data="{ tab: 'stats' }">
        <!-- Group Info Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <!-- Group Avatar -->
                <div class="w-16 h-16 rounded-full flex-shrink-0 flex items-center justify-center text-white text-2xl font-bold"
                     style="background-color: {{ $group->color ?? '#8B5CF6' }}">
                    @if($group->avatar)
                        <img src="{{ $group->avatar }}" alt="{{ $group->name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        {{ substr($group->name ?? 'G', 0, 1) }}
                    @endif
                </div>

                <div class="flex-1">
                    <h2 class="text-xl font-bold text-[#292524]">{{ $group->name ?? 'Group Name' }}</h2>
                    @if($group->description)
                        <p class="text-sm text-[#57534e] mt-1">{{ $group->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Member Count -->
            <div class="flex items-center gap-2 text-[#57534e]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ $group->users->count() ?? 0 }} members</span>
            </div>

            <!-- Invite Code -->
            @if($group->invite_code)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-[#57534e] mb-2">Invite Code</p>
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                        <span class="text-lg font-bold tracking-wider text-[#292524]">{{ $group->invite_code }}</span>
                        <button onclick="navigator.clipboard.writeText('{{ $group->invite_code }}')"
                                class="text-[#8B5CF6] hover:text-[#7C3AED] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Create Event Button -->
        <a href="{{ route('groups.events.create', $groupId) }}"
           class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold rounded-full shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create Group Event</span>
        </a>

        <!-- Tabs -->
        <div class="bg-[#e7e5e4] rounded-full p-1 flex gap-1">
            <button @click="tab = 'stats'"
                    :class="tab === 'stats' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]'"
                    class="flex-1 py-3 px-6 rounded-full text-base font-semibold transition-all">
                Stats
            </button>
            <button @click="tab = 'events'"
                    :class="tab === 'events' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]'"
                    class="flex-1 py-3 px-6 rounded-full text-base font-semibold transition-all">
                Events
            </button>
        </div>

        <!-- Stats Tab -->
        <div x-show="tab === 'stats'" x-transition class="space-y-4">
            <!-- Stats Type Selector -->
            <div class="bg-[#e7e5e4] rounded-full p-1 flex gap-1">
                <button wire:click="setStatsTab('events')"
                        class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold transition-all {{ $statsTab === 'events' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                    Event Stats
                </button>
                <button wire:click="setStatsTab('members')"
                        class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold transition-all {{ $statsTab === 'members' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                    Member Stats
                </button>
            </div>

            <!-- Period Selector -->
            <div class="bg-[#e7e5e4] rounded-full p-1 flex gap-1">
                <button wire:click="setPeriod('today')"
                        class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold transition-all {{ $period === 'today' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                    Today
                </button>
                <button wire:click="setPeriod('7d')"
                        class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold transition-all {{ $period === '7d' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                    7 Days
                </button>
                <button wire:click="setPeriod('30d')"
                        class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold transition-all {{ $period === '30d' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                    30 Days
                </button>
            </div>

            @php
                $currentStats = $statsTab === 'events' ? $eventStats : $memberStats;
                $statsTitle = $statsTab === 'events' ? 'Event Ratings' : 'Member Moods';
            @endphp

            <!-- Info Box -->
            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-200">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        @if($statsTab === 'events')
                            <p class="text-sm font-semibold text-[#292524]">Event Statistics</p>
                            <p class="text-sm text-[#57534e] mt-1">Shows how members rated shared group events</p>
                        @else
                            <p class="text-sm font-semibold text-[#292524]">Aggregated Member Stats</p>
                            <p class="text-sm text-[#57534e] mt-1">General mood trends of all members (anonymous)</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Average Mood Card -->
            <div class="bg-white rounded-3xl p-6 text-center shadow-sm">
                <p class="text-sm text-[#57534e] mb-2">Average {{ $statsTitle }}</p>
                @if(isset($currentStats['average_mood']) && $currentStats['average_mood'] > 0)
                    @php
                        $moodScore = round($currentStats['average_mood']);
                        $moodIcon = match(true) {
                            $moodScore <= 2 => 'depressed_icon.svg',
                            $moodScore <= 4 => 'Sad_icon.svg',
                            $moodScore <= 6 => 'Normal_icon.svg',
                            $moodScore <= 8 => 'Happy_icon.svg',
                            default => 'Great_icon.svg',
                        };
                    @endphp
                    <div class="w-20 h-20 mx-auto mb-2">
                        <img src="{{ asset('images/moods/' . $moodIcon) }}" alt="Mood" class="w-full h-full object-contain">
                    </div>
                    <p class="text-3xl font-bold text-[#292524]">{{ number_format($currentStats['average_mood'], 1) }}</p>
                @else
                    <div class="w-20 h-20 mx-auto mb-2">
                        <img src="{{ asset('images/moods/Normal_icon.svg') }}" alt="No mood" class="w-full h-full object-contain opacity-50">
                    </div>
                    <p class="text-lg text-[#57534e]">No data yet</p>
                @endif
            </div>

            <!-- Activity Today -->
            @if(isset($currentStats['activity_today']))
                <div class="bg-white rounded-3xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#292524] mb-4">Activity Today</h3>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl font-bold text-[#292524]">{{ $currentStats['activity_today']['members_logged'] }}/{{ $currentStats['activity_today']['total_members'] }}</span>
                        <span class="text-sm text-[#57534e]">{{ $currentStats['activity_today']['percentage'] }}% active</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-[#8B5CF6] h-2 rounded-full transition-all"
                             style="width: {{ $currentStats['activity_today']['percentage'] }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Mood Distribution -->
            @if(isset($currentStats['mood_distribution']) && $currentStats['total_count'] > 0)
                <div class="bg-white rounded-3xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#292524] mb-4">Mood Distribution</h3>
                    <div class="space-y-4">
                        @foreach($currentStats['mood_distribution'] as $dist)
                            @if($dist['count'] > 0)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex-shrink-0">
                                        <img src="{{ asset('images/moods/' . $dist['icon']) }}"
                                             alt="Mood {{ $dist['range'] }}"
                                             class="w-full h-full object-contain">
                                    </div>
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-[#8B5CF6] h-2.5 rounded-full transition-all"
                                                 style="width: {{ $currentStats['total_count'] > 0 ? ($dist['count'] / $currentStats['total_count']) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-base font-semibold text-[#292524] w-8 text-right">{{ $dist['count'] }}</span>
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
               class="block bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition-shadow">
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
