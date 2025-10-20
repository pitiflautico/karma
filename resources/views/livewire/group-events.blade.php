<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="Group Events" />

    <!-- Content -->
    <div class="px-6 py-6" style="padding-bottom: max(8rem, env(safe-area-inset-bottom, 0px) + 8rem);">
        <!-- Filters -->
        <div class="bg-[#e7e5e4] rounded-full p-1 flex gap-1 overflow-x-auto mb-6">
            <button wire:click="setFilter('all')"
                    class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold whitespace-nowrap transition-all
                        {{ $filter === 'all' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                All
            </button>
            <button wire:click="setFilter('upcoming')"
                    class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold whitespace-nowrap transition-all
                        {{ $filter === 'upcoming' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                Upcoming
            </button>
            <button wire:click="setFilter('past')"
                    class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold whitespace-nowrap transition-all
                        {{ $filter === 'past' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                Past
            </button>
            <button wire:click="setFilter('rated')"
                    class="flex-1 py-2.5 px-4 rounded-full text-sm font-semibold whitespace-nowrap transition-all
                        {{ $filter === 'rated' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}">
                Rated
            </button>
        </div>
        @if($events && count($events) > 0)
            <!-- Events List -->
            <div class="space-y-4 mb-6">
                @foreach($events as $event)
                    <a href="{{ route('groups.events.rate', $event['id']) }}"
                       class="block bg-white rounded-3xl p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <!-- Event Icon -->
                            <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center
                                {{ $event['is_custom'] ? 'bg-[#8B5CF6]/10' : 'bg-blue-50' }}">
                                @if($event['is_custom'])
                                    <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Event Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-bold text-[#292524] text-base">{{ $event['title'] }}</h3>
                                    <!-- Source Badge -->
                                    <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                                        {{ $event['is_custom'] ? 'bg-[#8B5CF6]/10 text-[#8B5CF6]' : 'bg-blue-50 text-blue-600' }}">
                                        {{ $event['is_custom'] ? 'Custom' : 'Calendar' }}
                                    </span>
                                </div>

                                <!-- Date & Time -->
                                <p class="text-sm text-[#57534e] mt-1">
                                    {{ $event['event_date_formatted'] }}
                                </p>

                                @if($event['description'])
                                    <p class="text-sm text-[#57534e] mt-1 line-clamp-1">{{ $event['description'] }}</p>
                                @endif

                                <!-- Rating Stats -->
                                <div class="flex items-center gap-4 mt-3">
                                    @if($event['rating_count'] > 0)
                                        <!-- Average Mood -->
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-lg">{{ $event['mood_emoji'] }}</span>
                                            <span class="text-sm font-semibold text-[#292524]">{{ number_format($event['average_mood'], 1) }}</span>
                                        </div>

                                        <!-- Rating Count -->
                                        <div class="text-sm text-[#57534e]">
                                            {{ $event['rating_count'] }}/{{ $event['total_members'] }} rated
                                        </div>
                                    @else
                                        <span class="text-sm text-[#a8a29e] italic">No ratings yet</span>
                                    @endif
                                </div>

                                <!-- User Rating Status -->
                                @if(!$event['user_rated'])
                                    <div class="mt-3">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-full text-sm font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                            <span>Rate this event!</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-green-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-medium">You rated this</span>
                                        </span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#292524] mb-2">No events yet</h3>
                <p class="text-[#57534e] text-center mb-8">Create an event for your group to rate</p>
            </div>
        @endif

        <!-- Create Event Button -->
        <a href="{{ route('groups.events.create', $groupId) }}"
           class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold rounded-full shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create Event</span>
        </a>
    </div>
</div>
