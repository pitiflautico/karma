<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <x-interior-header title="Calendar Events" />

    <!-- Content -->
    <div class="px-6 py-6 space-y-6">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-[#10B981]/10 border border-[#10B981]/20 rounded-2xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#10B981] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-[#047857]">{{ session('success') }}</p>
            </div>
        @endif

        @if(!auth()->user()->calendar_sync_enabled)
            <!-- Not Connected State -->
            <div class="bg-white rounded-2xl p-8 text-center">
                <!-- Calendar Icon SVG -->
                <div class="w-20 h-20 mx-auto mb-4 bg-[#8B5CF6]/10 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#292524] mb-2">Calendar not connected</h3>
                <p class="text-sm text-[#57534e] mb-6">
                    Connect your Google Calendar to see your events and receive mood reminders after events.
                </p>

                <a
                    href="{{ route('auth.google.sync') }}"
                    class="inline-flex items-center justify-center w-full py-3.5 px-6 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold rounded-xl transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Connect Google Calendar
                </a>
            </div>
        @else
            <!-- Events List -->
            @forelse($eventsByDate as $date => $events)
                <!-- Date Header -->
                <div>
                    <h3 class="text-base font-bold text-[#292524] mb-3 px-1">{{ $date }}</h3>

                    <!-- Event Cards -->
                    <div class="space-y-3">
                        @foreach($events as $event)
                            <div class="bg-white rounded-2xl p-5 cursor-pointer hover:shadow-md transition-shadow"
                                 wire:click="$dispatch('viewEventDetails', { eventId: '{{ $event->id }}' })">
                                <!-- Event Title -->
                                <h4 class="font-bold text-[#292524] mb-3">{{ $event->title }}</h4>

                                <!-- Event Time -->
                                <div class="flex items-center gap-2 text-sm text-[#57534e] mb-2">
                                    <svg class="w-4 h-4 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <div class="flex items-center gap-2 text-sm text-[#57534e] mb-2">
                                        <svg class="w-4 h-4 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $event->location }}</span>
                                    </div>
                                @endif

                                <!-- Event Description -->
                                @if($event->description)
                                    <p class="text-sm text-[#57534e] mt-3 line-clamp-2">{{ $event->description }}</p>
                                @endif

                                <!-- Event Type Badge & Mood Status -->
                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                    @if($event->event_type)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#8B5CF6]/10 text-[#8B5CF6]">
                                            {{ ucfirst($event->event_type) }}
                                        </span>
                                    @else
                                        <div></div>
                                    @endif

                                    <!-- Mood Status -->
                                    @if($event->hasEnded())
                                        @if($event->hasMoodLogged())
                                            <div class="flex items-center text-xs font-medium text-[#10B981]">
                                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Mood logged
                                            </div>
                                        @else
                                            <div class="flex items-center text-xs font-medium text-[#78716c]">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Not logged
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-2xl p-12 text-center">
                    <!-- Calendar Icon SVG -->
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-[#292524] mb-2">No events found</h3>
                    <p class="text-sm text-[#57534e]">
                        Click "Sync Events" below to fetch your calendar events.
                    </p>
                </div>
            @endforelse

            <!-- Sync Button -->
            <div class="sticky bottom-0 pb-6" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 0px) + 1.5rem);">
                @if(auth()->user()->last_calendar_sync_at)
                    <p class="text-xs text-[#78716c] text-center mb-3">
                        Last synced: {{ auth()->user()->last_calendar_sync_at->diffForHumans() }}
                    </p>
                @endif

                <button
                    wire:click="syncEvents"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 px-4 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold rounded-xl transition-colors flex items-center justify-center disabled:opacity-50"
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
    </div>

    <!-- Event Detail Modal -->
    @livewire('calendar-event-detail-modal')
</div>
