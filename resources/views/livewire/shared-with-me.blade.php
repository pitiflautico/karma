<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="Shared With Me" />

    <!-- Content -->
    <div class="px-6 py-6 space-y-6">
        @if($sharedAccesses->count() > 0)
            <!-- People Sharing With Me -->
            <div class="bg-white rounded-2xl p-6">
                <h3 class="text-lg font-bold text-[#292524] mb-4">People Sharing With Me</h3>
                <div class="space-y-3">
                    @foreach($sharedAccesses as $access)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <!-- Avatar -->
                            <div class="w-12 h-12 bg-[#8B5CF6] rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($access->owner->name, 0, 1)) }}</span>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#292524] truncate">{{ $access->owner->name }}</p>
                                <p class="text-xs text-[#57534e] truncate">{{ $access->owner->email }}</p>
                            </div>

                            <!-- Permissions Badges -->
                            <div class="flex gap-1 flex-shrink-0">
                                @if($access->can_view_moods)
                                    <div class="w-2 h-2 bg-[#10B981] rounded-full" title="Can view moods"></div>
                                @endif
                                @if($access->can_view_notes)
                                    <div class="w-2 h-2 bg-[#3B82F6] rounded-full" title="Can view notes"></div>
                                @endif
                                @if($access->can_view_selfies)
                                    <div class="w-2 h-2 bg-[#F59E0B] rounded-full" title="Can view selfies"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl p-6 space-y-4">
                <h3 class="text-base font-bold text-[#292524] mb-2">Filters</h3>

                <!-- Person Filter -->
                <div>
                    <label class="text-sm font-medium text-[#57534e] mb-1.5 block">Person</label>
                    <select
                        wire:model.live="selectedOwnerId"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-[#292524] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent"
                    >
                        <option value="">All People</option>
                        @foreach($sharedAccesses as $access)
                            <option value="{{ $access->owner_id }}">{{ $access->owner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-[#57534e] mb-1.5 block">From</label>
                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-[#292524] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent"
                        >
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#57534e] mb-1.5 block">To</label>
                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-[#292524] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            <!-- Mood Entries -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-[#292524] px-1">Mood Entries</h3>

                @if($moodEntries->count() > 0)
                    @foreach($moodEntries as $entry)
                        @php
                            $moodEmoji = match(true) {
                                $entry->mood_score >= 9 => '🤩',
                                $entry->mood_score >= 8 => '😄',
                                $entry->mood_score >= 7 => '😊',
                                $entry->mood_score >= 6 => '🙂',
                                $entry->mood_score >= 5 => '😶',
                                $entry->mood_score >= 4 => '😐',
                                $entry->mood_score >= 3 => '😕',
                                $entry->mood_score >= 2 => '☹️',
                                default => '😢',
                            };
                        @endphp

                        <div class="bg-white rounded-2xl p-5">
                            <!-- Header with User Info -->
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-[#8B5CF6] rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold">{{ strtoupper(substr($entry->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-[#292524]">{{ $entry->user->name }}</p>
                                    <p class="text-xs text-[#57534e]">{{ $entry->created_at->format('M d, Y') }} at {{ $entry->created_at->format('H:i') }}</p>
                                </div>
                            </div>

                            <!-- Mood Score -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="text-4xl">{{ $moodEmoji }}</div>
                                    <div>
                                        <p class="text-2xl font-bold text-[#292524]">{{ $entry->mood_score }}</p>
                                        <p class="text-xs text-[#57534e]">out of 10</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Calendar Event -->
                            @if($entry->calendarEvent)
                                <div class="flex items-center gap-2 mb-3 p-3 bg-gray-50 rounded-xl">
                                    <svg class="w-4 h-4 text-[#8B5CF6] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-[#292524] truncate">{{ $entry->calendarEvent->title }}</span>
                                </div>
                            @endif

                            <!-- Note -->
                            @if($this->canViewNotes($entry->user_id) && $entry->note)
                                <div class="p-3 bg-[#F7F3EF] rounded-xl">
                                    <p class="text-sm text-[#57534e] italic">"{{ $entry->note }}"</p>
                                </div>
                            @elseif(!$this->canViewNotes($entry->user_id) && $entry->note)
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-[#78716c] italic flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Note hidden (no permission)
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $moodEntries->links() }}
                    </div>
                @else
                    <!-- No Entries Found -->
                    <div class="bg-white rounded-2xl p-12 text-center">
                        <div class="text-5xl mb-4">📭</div>
                        <h3 class="text-lg font-bold text-[#292524] mb-2">No mood entries found</h3>
                        <p class="text-sm text-[#57534e]">Try adjusting your filters to see more results</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl p-12 text-center">
                <div class="text-6xl mb-4">👥</div>
                <h3 class="text-xl font-bold text-[#292524] mb-3">No one has shared with you yet</h3>
                <p class="text-[#57534e] mb-6">When someone shares their mood data with you, it will appear here.</p>

                <!-- Info Box -->
                <div class="bg-[#DBEAFE] rounded-xl p-4 text-left">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-[#3B82F6] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-[#1E40AF] mb-1">How it works</p>
                            <p class="text-xs text-[#1E3A8A]">Others can share their mood data from their Sharing Settings page. Once shared, you'll be able to see their moods here.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
