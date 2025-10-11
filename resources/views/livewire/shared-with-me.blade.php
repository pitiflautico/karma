<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Shared With Me</h2>
            <p class="mt-2 text-gray-600">View mood data that others have shared with you</p>
        </div>

        @if($sharedAccesses->count() > 0)
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="selectedOwnerId" class="block text-sm font-medium text-gray-700 mb-2">
                            Filter by Person
                        </label>
                        <select
                            id="selectedOwnerId"
                            wire:model.live="selectedOwnerId"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        >
                            <option value="">All People</option>
                            @foreach($sharedAccesses as $access)
                                <option value="{{ $access->owner_id }}">{{ $access->owner->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">
                            From Date
                        </label>
                        <input
                            type="date"
                            id="dateFrom"
                            wire:model.live="dateFrom"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        >
                    </div>

                    <div>
                        <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">
                            To Date
                        </label>
                        <input
                            type="date"
                            id="dateTo"
                            wire:model.live="dateTo"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        >
                    </div>
                </div>
            </div>

            <!-- People Sharing With Me -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">People Sharing With Me</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($sharedAccesses as $access)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-purple-600 font-semibold text-lg">{{ substr($access->owner->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $access->owner->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $access->owner->email }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($access->can_view_moods)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Moods</span>
                                @endif
                                @if($access->can_view_notes)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Notes</span>
                                @endif
                                @if($access->can_view_selfies)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Selfies</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mood Entries -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mood Entries</h3>

                @if($moodEntries->count() > 0)
                    <div class="space-y-4">
                        @foreach($moodEntries as $entry)
                            @php
                                $moodCategory = match(true) {
                                    $entry->mood_score >= 8 => ['bg-green-100', 'text-green-800', 'Excellent'],
                                    $entry->mood_score >= 6 => ['bg-blue-100', 'text-blue-800', 'Good'],
                                    $entry->mood_score >= 4 => ['bg-yellow-100', 'text-yellow-800', 'Medium'],
                                    default => ['bg-red-100', 'text-red-800', 'Low'],
                                };
                            @endphp

                            <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-purple-600 font-semibold text-sm">{{ substr($entry->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $entry->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $entry->created_at->format('M d, Y \a\t H:i') }}</p>
                                            </div>
                                        </div>

                                        @if($entry->calendarEvent)
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $entry->calendarEvent->title }}
                                            </div>
                                        @endif

                                        @if($this->canViewNotes($entry->user_id) && $entry->note)
                                            <p class="text-sm text-gray-700 mt-2 italic">"{{ $entry->note }}"</p>
                                        @elseif(!$this->canViewNotes($entry->user_id) && $entry->note)
                                            <p class="text-xs text-gray-400 italic mt-2">Note hidden (no permission)</p>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900 mb-1">{{ $entry->mood_score }}<span class="text-sm text-gray-500">/10</span></div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $moodCategory[0] }} {{ $moodCategory[1] }}">
                                            {{ $moodCategory[2] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $moodEntries->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600">No mood entries found for the selected filters.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No one has shared data with you yet</h3>
                <p class="text-gray-600 mb-4">When someone shares their mood data with you, it will appear here.</p>
                <p class="text-sm text-gray-500">You can invite others to share their data from their Sharing Settings page.</p>
            </div>
        @endif
    </div>
</div>
