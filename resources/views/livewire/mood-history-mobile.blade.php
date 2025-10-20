<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="Mood History" />

    <!-- Subtitle -->
    <div class="px-6 pt-4 pb-2">
        <p class="text-[#78716c] text-base">Browse your mood history here</p>
    </div>

    <!-- Tabs -->
    <div class="px-6 py-4">
        <div class="bg-[#e7e5e4] rounded-full p-1 flex gap-1">
            <button
                wire:click="switchView('list')"
                class="flex-1 py-3 px-6 rounded-full text-base font-semibold transition-all {{ $activeView === 'list' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}"
            >
                List View
            </button>
            <button
                wire:click="switchView('calendar')"
                class="flex-1 py-3 px-6 rounded-full text-base font-semibold transition-all {{ $activeView === 'calendar' ? 'bg-white text-[#292524] shadow-md' : 'text-[#78716c]' }}"
            >
                Calendar View
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 pb-6">
        @if($activeView === 'list')
            <!-- List View -->
            <div class="space-y-6">
                @forelse($moodsByDate as $date => $moods)
                    <!-- Date Header -->
                    <div class="pt-4">
                        <h3 class="text-[18px] font-bold text-[#292524] mb-4">{{ $date }}</h3>

                        <!-- Mood Cards -->
                        <div class="space-y-3">
                            @foreach($moods as $mood)
                                <x-swipeable-card :deleteId="$mood->id">
                                    <x-mood-card :mood="$mood" />
                                </x-swipeable-card>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#292524] mb-2">No mood entries yet</h3>
                        <p class="text-sm text-[#57534e]">Start tracking your moods to see them here!</p>
                    </div>
                @endforelse

                <!-- See More Button -->
                @if($hasMoreDays)
                    <div class="pt-6 pb-2">
                        <button
                            wire:click="loadMore"
                            class="w-full py-3.5 bg-white text-[#926247] font-semibold text-base rounded-xl shadow-sm border border-[#e7e5e4] hover:bg-[#f7f3ef] transition-colors">
                            See More
                        </button>
                    </div>
                @endif
            </div>

        @else
            <!-- Calendar View -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <!-- Month Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <button
                        wire:click="goToToday"
                        class="text-sm font-medium text-[#78716c] hover:text-[#292524] transition-colors"
                    >
                        Today
                    </button>

                    <div class="flex items-center gap-4">
                        <button wire:click="previousMonth" class="text-[#78716c] hover:text-[#292524] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <h3 class="text-xl font-bold text-[#292524] min-w-[160px] text-center">{{ $currentMonthName }}</h3>

                        <button wire:click="nextMonth" class="text-[#78716c] hover:text-[#292524] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div class="w-12"></div> <!-- Spacer for alignment -->
                </div>

                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-2 mb-4">
                    @foreach(['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $day)
                        <div class="text-center text-sm font-bold text-[#292524]">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($calendarData as $day)
                        @if($day === null)
                            <!-- Empty cell -->
                            <div class="flex flex-col items-center gap-1"></div>
                        @else
                            <div class="flex flex-col items-center gap-1">
                                @if($day['moods']->count() > 0)
                                    @php
                                        // Get the representative mood for the day (first one)
                                        $representativeMood = $day['moods']->first();
                                    @endphp

                                    <!-- Mood SVG icon -->
                                    <div class="relative w-8 h-8 flex items-center justify-center">
                                        <img
                                            src="{{ asset('images/moods/' . $representativeMood->mood_icon) }}"
                                            alt="{{ $representativeMood->mood_name }}"
                                            class="w-8 h-8 object-contain"
                                        >

                                        @if($day['isToday'])
                                            <!-- Green ring for today -->
                                            <div class="absolute inset-0 rounded-full border-2 border-[#9BB167]"></div>
                                        @endif
                                    </div>
                                @else
                                    <!-- No mood for this day (gray circle SVG) -->
                                    <div class="relative w-8 h-8">
                                        <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="20" cy="20" r="18" stroke="#d6d3d1" stroke-width="2" fill="none"/>
                                        </svg>

                                        @if($day['isToday'])
                                            <!-- Green ring for today -->
                                            <div class="absolute inset-0 rounded-full border-2 border-[#9BB167]"></div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Day number below circle -->
                                <span class="text-xs font-medium text-[#78716c]">{{ $day['day'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="mt-6 pt-6 border-t border-[#e7e5e4]">
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Skipped -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full border-2 border-[#d6d3d1] flex-shrink-0"></div>
                            <span class="text-sm font-medium text-[#57534e]">Skipped</span>
                        </div>
                        <!-- Neutral -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex-shrink-0" style="background-color: #78716C"></div>
                            <span class="text-sm font-medium text-[#57534e]">Neutral</span>
                        </div>
                        <!-- Positive -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex-shrink-0" style="background-color: #9BB167"></div>
                            <span class="text-sm font-medium text-[#57534e]">Positive</span>
                        </div>
                        <!-- Negative -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex-shrink-0" style="background-color: #FB7185"></div>
                            <span class="text-sm font-medium text-[#57534e]">Negative</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-6" wire:click="cancelDelete">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full" wire:click.stop>
                <h3 class="text-xl font-bold text-[#292524] mb-2">Delete Mood Entry?</h3>
                <p class="text-[#57534e] mb-6">This action cannot be undone.</p>

                <div class="flex gap-3">
                    <button
                        wire:click="cancelDelete"
                        class="flex-1 py-3 px-4 bg-gray-100 text-[#292524] font-semibold rounded-xl hover:bg-gray-200 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="deleteMood"
                        class="flex-1 py-3 px-4 bg-[#EF4444] text-white font-semibold rounded-xl hover:bg-[#DC2626] transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mood Detail Modal -->
    @livewire('mood-detail-modal')
</div>
