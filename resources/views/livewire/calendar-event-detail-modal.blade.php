<div>
@if($showModal && $event)
<div class="fixed inset-0 z-50 overflow-y-auto" style="z-index: 9999;">
    <!-- Backdrop -->
    <div
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        wire:click="closeModal"
    ></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
        <div
            class="relative w-full max-w-2xl bg-white rounded-t-3xl sm:rounded-3xl shadow-xl transform transition-all"
            style="margin-bottom: 0; padding-bottom: env(safe-area-inset-bottom, 0px);"
        >
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-3xl flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Event Details</h3>
                <button
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-4 space-y-6 max-h-[70vh] overflow-y-auto">
                <!-- Event Title -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                </div>

                <!-- Date & Time -->
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $event->start_time->format('l, F j, Y') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($event->is_all_day)
                                All day
                            @else
                                {{ $event->start_time->format('g:i A') }} - {{ $event->end_time->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Location -->
                @if($event->location)
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-600">{{ $event->location }}</p>
                    </div>
                </div>
                @endif

                <!-- Conference Link (Google Meet, Zoom, etc) -->
                @if($event->conference_link)
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <a href="{{ $event->conference_link }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                            Join video meeting
                        </a>
                    </div>
                </div>
                @endif

                <!-- Organizer -->
                @if($event->organizer_email)
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Organizer</p>
                        <p class="text-sm text-gray-600">
                            {{ $event->organizer_name ?: $event->organizer_email }}
                        </p>
                    </div>
                </div>
                @endif

                <!-- Attendees -->
                @if($event->attendees && count($event->attendees) > 0)
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900">
                            Attendees ({{ count($event->attendees) }})
                        </h4>
                    </div>
                    <div class="space-y-2">
                        @foreach($event->attendees as $attendee)
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-semibold text-gray-700">
                                    {{ strtoupper(substr($attendee['email'] ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $attendee['name'] ?? $attendee['email'] }}
                                        @if($attendee['optional'] ?? false)
                                            <span class="text-xs text-gray-500">(Optional)</span>
                                        @endif
                                    </p>
                                    @if($attendee['email'] && $attendee['name'])
                                        <p class="text-xs text-gray-500">{{ $attendee['email'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @php
                                    $status = $attendee['response_status'] ?? 'needsAction';
                                    $statusConfig = match($status) {
                                        'accepted' => ['text' => 'Going', 'class' => 'bg-green-100 text-green-800'],
                                        'declined' => ['text' => 'Declined', 'class' => 'bg-red-100 text-red-800'],
                                        'tentative' => ['text' => 'Maybe', 'class' => 'bg-yellow-100 text-yellow-800'],
                                        default => ['text' => 'Pending', 'class' => 'bg-gray-100 text-gray-600'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['text'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Description -->
                @if($event->description)
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
                    <div class="text-sm text-gray-600 prose prose-sm max-w-none">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
                @endif

                <!-- Event Link -->
                @if($event->event_link)
                <div class="border-t border-gray-200 pt-4">
                    <a
                        href="{{ $event->event_link }}"
                        target="_blank"
                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        View in Google Calendar
                    </a>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 px-6 py-4">
                <button
                    wire:click="closeModal"
                    class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-900 font-medium rounded-2xl transition"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif
</div>
