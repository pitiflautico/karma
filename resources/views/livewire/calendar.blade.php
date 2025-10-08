<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Calendar & Mood Tracker</h2>
                    <p class="text-gray-600">View your mood entries and calendar events</p>
                </div>
                @if(!auth()->user()->calendar_sync_enabled)
                    <a href="{{ route('auth.google.sync') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Sync Google Calendar
                    </a>
                @endif
            </div>
        </div>

        @if(!auth()->user()->calendar_sync_enabled)
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-900">Google Calendar not connected</h3>
                        <p class="mt-1 text-xs text-yellow-700">Connect your Google Calendar to see your events alongside your mood entries.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
                        </h3>
                        <div class="flex gap-2">
                            <button wire:click="previousMonth" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                ← Previous
                            </button>
                            <button wire:click="nextMonth" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                Next →
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1">
                        <!-- Day headers -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="text-center text-xs font-semibold text-gray-600 py-2">
                                {{ $day }}
                            </div>
                        @endforeach

                        <!-- Calendar days -->
                        @foreach($daysInMonth as $dayData)
                            @if($dayData === null)
                                <div class="aspect-square border border-gray-100"></div>
                            @else
                                <button
                                    wire:click="selectDate('{{ $dayData['date'] }}')"
                                    class="aspect-square border p-1 transition-colors relative
                                        {{ $dayData['isToday'] ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}
                                        {{ $selectedDate === $dayData['date'] ? 'ring-2 ring-purple-500' : '' }}"
                                >
                                    <div class="text-sm {{ $dayData['isToday'] ? 'font-bold text-purple-600' : 'text-gray-700' }}">
                                        {{ $dayData['day'] }}
                                    </div>

                                    @if($dayData['moodCount'] > 0)
                                        <div class="mt-1">
                                            <div class="w-full h-1.5 rounded-full
                                                @if($dayData['avgMood'] <= 3) bg-red-400
                                                @elseif($dayData['avgMood'] <= 5) bg-yellow-400
                                                @elseif($dayData['avgMood'] <= 7) bg-green-400
                                                @else bg-blue-400
                                                @endif">
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ number_format($dayData['avgMood'], 1) }}
                                            </div>
                                        </div>
                                    @endif
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-600 mb-2">Mood Legend:</p>
                        <div class="flex gap-4 text-xs">
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 rounded bg-red-400"></div>
                                <span>Low (1-3)</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 rounded bg-yellow-400"></div>
                                <span>Fair (4-5)</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 rounded bg-green-400"></div>
                                <span>Good (6-7)</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 rounded bg-blue-400"></div>
                                <span>Great (8-10)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Date Details -->
            <div class="lg:col-span-1">
                @if($selectedDate)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                        </h3>

                        <!-- Mood Entries -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center justify-between">
                                <span>Mood Entries ({{ count($selectedDateMoods) }})</span>
                                <button wire:click="$dispatch('openMoodEntryModal')" class="text-purple-600 hover:text-purple-700 text-xs">
                                    + Add
                                </button>
                            </h4>

                            @if(count($selectedDateMoods) > 0)
                                <div class="space-y-3">
                                    @foreach($selectedDateMoods as $mood)
                                        <div class="p-3 bg-gray-50 rounded border-l-2
                                            @if($mood->mood_score <= 3) border-red-400
                                            @elseif($mood->mood_score <= 5) border-yellow-400
                                            @elseif($mood->mood_score <= 7) border-green-400
                                            @else border-blue-400
                                            @endif">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-semibold text-sm">{{ $mood->mood_score }}/10</span>
                                                <span class="text-xs text-gray-500">{{ $mood->created_at->format('H:i') }}</span>
                                            </div>
                                            @if($mood->note)
                                                <p class="text-xs text-gray-600 italic">{{ Str::limit($mood->note, 80) }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No mood entries for this day</p>
                            @endif
                        </div>

                        <!-- Calendar Events -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">
                                Calendar Events ({{ count($selectedDateEvents) }})
                            </h4>

                            @if(count($selectedDateEvents) > 0)
                                <div class="space-y-2">
                                    @foreach($selectedDateEvents as $event)
                                        <div class="p-2 bg-blue-50 rounded text-xs">
                                            <div class="font-medium text-blue-900">{{ $event->title }}</div>
                                            <div class="text-blue-600">
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No events for this day</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-sm text-gray-500 text-center">Select a date to view details</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mood Entry Form Modal -->
    <livewire:mood-entry-form />
</div>
