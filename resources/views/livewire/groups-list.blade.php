<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="My Groups" />

    <!-- Content -->
    <div class="px-6 py-6" style="padding-bottom: max(8rem, env(safe-area-inset-bottom, 0px) + 8rem);">
        @if($groups && count($groups) > 0)
            <!-- Groups List -->
            <div class="space-y-4 mb-6">
                @foreach($groups as $group)
                    <a href="{{ route('groups.dashboard', $group['id']) }}"
                       class="block bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <!-- Group Avatar -->
                            <div class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xl font-bold"
                                 style="background-color: {{ $group['color'] ?? '#8B5CF6' }}">
                                @if($group['avatar'])
                                    <img src="{{ $group['avatar'] }}" alt="{{ $group['name'] }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ substr($group['name'], 0, 1) }}
                                @endif
                            </div>

                            <!-- Group Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-[#292524] text-lg">{{ $group['name'] }}</h3>

                                @if($group['description'])
                                    <p class="text-sm text-[#57534e] mt-1 line-clamp-1">{{ $group['description'] }}</p>
                                @endif

                                <!-- Stats Row -->
                                <div class="flex items-center gap-4 mt-3">
                                    <!-- Member Count -->
                                    <div class="flex items-center gap-1 text-sm text-[#57534e]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>{{ $group['member_count'] }} members</span>
                                    </div>

                                    @if(isset($group['mood_today']) && $group['mood_today'] > 0)
                                        <!-- Today's Mood -->
                                        <div class="flex items-center gap-1 text-sm font-medium text-[#292524]">
                                            <span>{{ $group['mood_emoji'] }}</span>
                                            <span>{{ number_format($group['mood_today'], 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if(isset($group['activity_rate']))
                                    <!-- Activity Bar -->
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-xs text-[#57534e] mb-1">
                                            <span>{{ $group['activity_rate'] }}% active today</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-[#8B5CF6] h-1.5 rounded-full transition-all"
                                                 style="width: {{ $group['activity_rate'] }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 px-6 mb-6">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#292524] mb-2">No groups yet</h3>
                <p class="text-[#57534e] text-center mb-8">Join a group to share moods with family, friends, or teams</p>
            </div>
        @endif

        <!-- Bottom Action Buttons -->
        <div class="flex gap-3 pt-4">
            <!-- Join Group Button -->
            <a href="{{ route('groups.join') }}"
               class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-white hover:bg-gray-50 text-[#292524] font-semibold rounded-full border-2 border-[#e7e5e4] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Join Group</span>
            </a>

            <!-- Create Group Button -->
            <a href="{{ route('groups.create') }}"
               class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold rounded-full shadow-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Create Group</span>
            </a>
        </div>
    </div>
</div>
