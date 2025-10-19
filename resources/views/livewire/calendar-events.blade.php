<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Google Calendar Events</h2>
                <p class="text-gray-600">Your upcoming events</p>
                @if(auth()->user()->last_calendar_sync_at)
                    <p class="text-sm text-gray-500 mt-1">Last synced: {{ auth()->user()->last_calendar_sync_at->diffForHumans() }}</p>
                @endif
            </div>

            @if(auth()->user()->calendar_sync_enabled)
                <button wire:click="syncEvents" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition {{ $syncing ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove>Sync Events</span>
                    <span wire:loading>Syncing...</span>
                </button>
            @else
                <a href="{{ route('auth.google.sync') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                    Connect Google Calendar First
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            @if(count($events) > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($events as $event)
                        <div class="p-6 hover:bg-gray-50 transition cursor-pointer" wire:click="$dispatch('viewEventDetails', { eventId: '{{ $event->id }}' })">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $event->title }}</h3>

                                    @if($event->description)
                                        <p class="mt-1 text-sm text-gray-600">{{ $event->description }}</p>
                                    @endif

                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($event->start_time)->format('D, M j, Y • g:i A') }}</span>
                                    </div>

                                    @if($event->location)
                                        <div class="mt-1 flex items-center text-sm text-gray-500">
                                            <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ \Carbon\Carbon::parse($event->start_time)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(auth()->user()->calendar_sync_enabled)
                            Click "Sync Events" to fetch your calendar events.
                        @else
                            Connect your Google Calendar to see your events here.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Event Detail Modal -->
    @livewire('calendar-event-detail-modal')
</div>
