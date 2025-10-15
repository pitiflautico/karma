<x-app-container
    title="Mood History"
    subtitle="Browse your mood history here"
    :showBackButton="true">

    <x-slot:header>
        <!-- Tabs -->
        <div class="mt-4">
            <div class="bg-gray-200 rounded-full p-1 flex">
                <button
                    wire:click="switchView('list')"
                    class="flex-1 py-3 px-4 rounded-full text-sm font-medium transition-all {{ $activeView === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}"
                >
                    List View
                </button>
                <button
                    wire:click="switchView('calendar')"
                    class="flex-1 py-3 px-4 rounded-full text-sm font-medium transition-all {{ $activeView === 'calendar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}"
                >
                    Calendar View
                </button>
            </div>
        </div>
    </x-slot:header>

    <!-- Content -->
        @if($activeView === 'list')
            <!-- List View -->
            <div class="space-y-6">
                @forelse($moodsByDate as $date => $moods)
                    <!-- Date Header -->
                    <div class="pt-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ $date }}</h3>

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
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">😊</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No mood entries yet</h3>
                        <p class="text-sm text-gray-500">Start tracking your moods to see them here!</p>
                    </div>
                @endforelse
            </div>

        @else
            <!-- Calendar View -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <!-- Month Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="goToToday" class="text-sm text-gray-600 hover:text-gray-900">
                        Today
                    </button>

                    <div class="flex items-center space-x-4">
                        <button wire:click="previousMonth" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <h3 class="text-lg font-semibold text-gray-900 min-w-[140px] text-center">{{ $currentMonthName }}</h3>

                        <button wire:click="nextMonth" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div class="w-12"></div> <!-- Spacer for alignment -->
                </div>

                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-2 mb-3">
                    @foreach(['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $day)
                        <div class="text-center text-xs font-medium text-gray-500">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($calendarData as $day)
                        @if($day === null)
                            <!-- Empty cell -->
                            <div class="aspect-square"></div>
                        @else
                            <div class="aspect-square flex items-center justify-center">
                                @if($day['moods']->count() > 0)
                                    @php
                                        // Get the representative mood for the day (first one)
                                        $representativeMood = $day['moods']->first();
                                    @endphp

                                    <div class="w-10 h-10 flex items-center justify-center {{ $day['isToday'] ? 'ring-2 ring-purple-600 ring-offset-2 rounded-full' : '' }}">
                                        <img src="{{ asset('images/moods/' . $representativeMood->mood_icon) }}" alt="{{ $representativeMood->mood_name }}" class="w-10 h-10">
                                    </div>
                                @else
                                    <!-- No mood for this day -->
                                    <div class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-xs text-gray-400 {{ $day['isToday'] ? 'ring-2 ring-purple-600 ring-offset-2' : '' }}">
                                        {{ $day['day'] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="grid grid-cols-3 gap-3 text-xs text-gray-600">
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-200"></div>
                            <span>Skipped</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full" style="background-color: #C084FC"></div>
                            <span>Depressed</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full" style="background-color: #FB923C"></div>
                            <span>Sad</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full" style="background-color: #B1865E"></div>
                            <span>Neutral</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full" style="background-color: #FBBF24"></div>
                            <span>Happy</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <div class="w-4 h-4 rounded-full" style="background-color: #9BB167"></div>
                            <span>Overjoyed</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- Delete Confirmation Modal -->
    <x-delete-confirmation-modal
        :show="$showDeleteConfirm"
        title="Delete Mood Entry?"
        message="This action cannot be undone."
        confirmText="Yes ✓"
        cancelText="Cancel"
        onConfirm="deleteMood"
        onCancel="cancelDelete"
    />

    <!-- Include MoodDetailModal for viewing -->
    @livewire('mood-detail-modal')

    <!-- Include MoodEntryForm for editing -->
    @livewire('mood-entry-form')
</x-app-container>
