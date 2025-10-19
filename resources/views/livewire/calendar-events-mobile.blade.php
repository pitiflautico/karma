<x-app-container
    title="Calendar Events"
    subtitle="Your upcoming Google Calendar events"
    :showBackButton="true">

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if(!auth()->user()->calendar_sync_enabled)
        <!-- Not Connected State -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📅</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Calendar not connected</h3>
            <p class="text-sm text-gray-500 mb-6">
                Connect your Google Calendar to see your events and receive mood reminders after events.
            </p>
            <a
                href="{{ route('auth.google.sync') }}"
                class="inline-block py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-2xl transition"
            >
                Connect Google Calendar
            </a>
        </div>
    @else
        <!-- Events List -->
        <div class="space-y-6">
            @forelse($eventsByDate as $date => $events)
            <!-- Date Header -->
            <div class="pt-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">{{ $date }}</h3>

                <!-- Event Cards -->
                <div class="space-y-3">
                    @foreach($events as $event)
                        <div class="bg-white rounded-2xl p-4 shadow-sm">
                            <!-- Event Title -->
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $event->title }}</h4>

                            <!-- Event Time -->
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>
                                    @if($event->is_all_day)
                                        All day
                                    @else
                                        {{ $event->start_time->format('g:i A') }}
                                        @if($event->end_time)
                                            - {{ $event->end_time->format('g:i A') }}
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <!-- Event Location -->
                            @if($event->location)
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif

                            <!-- Event Description -->
                            @if($event->description)
                                <p class="text-sm text-gray-600 mt-2">{{ Str::limit($event->description, 100) }}</p>
                            @endif

                            <!-- Event Type Badge -->
                            @if($event->event_type)
                                <div class="mt-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($event->event_type) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Mood Status -->
                            @if($event->hasEnded())
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    @if($event->hasMoodLogged())
                                        <div class="flex items-center text-sm text-green-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Mood logged
                                        </div>
                                    @else
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            No mood logged yet
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No events found</h3>
                    <p class="text-sm text-gray-500">
                        Click "Sync Events" below to fetch your calendar events.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Sync Button at Bottom -->
        <div class="mt-6 pb-6">
            @if(auth()->user()->last_calendar_sync_at)
                <p class="text-sm text-gray-500 text-center mb-3">Last synced: {{ auth()->user()->last_calendar_sync_at->diffForHumans() }}</p>
            @endif
            <button
                wire:click="syncEvents"
                wire:loading.attr="disabled"
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-2xl transition flex items-center justify-center {{ $syncing ? 'opacity-50' : '' }}"
            >
                <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>Sync Events</span>
                <span wire:loading>Syncing...</span>
            </button>
        </div>
    @endif
</x-app-container>
