<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Welcome back, {{ $user->name }}!</h2>
            <p class="text-gray-600">Here's your emotional wellness dashboard 11</p>
        </div>

        <!-- Google Calendar Sync Status -->
        @if(!$user->calendar_sync_enabled)
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-900">Sync your Google Calendar</h3>
                        <p class="mt-1 text-sm text-blue-700">Connect your Google Calendar to correlate your mood entries with your daily events and activities.</p>
                        <div class="mt-3">
                            <a href="{{ route('auth.google.sync') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Connect Google Calendar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-900">Google Calendar Connected</h3>
                            <p class="mt-1 text-sm text-green-700">Your calendar is synced and events will be correlated with your mood entries.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('auth.google.disconnect') }}" class="ml-3">
                        @csrf
                        <button type="submit" class="text-sm text-green-700 hover:text-green-900 underline">
                            Disconnect
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">7-Day Average</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $averageMood ? number_format($averageMood, 1) : 'N/A' }}
                                </div>
                                @if($averageMood)
                                    <div class="ml-2 text-sm text-gray-600">/10</div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Entries</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $user->moodEntries()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Groups</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $user->groups()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-4">
                    <button wire:click="$dispatch('openMoodEntryModal')" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Log Mood
                    </button>

                    <a href="{{ route('calendar') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        View Calendar
                    </a>

                    @if($user->calendar_sync_enabled)
                        <a href="{{ route('calendar.events') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Calendar Events
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Mood Prompts -->
        @if(count($pendingPrompts) > 0)
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Events Waiting for Your Feedback</h3>
                    <a href="{{ route('mood.prompts') }}" class="text-sm text-purple-600 hover:text-purple-700">View all</a>
                </div>
                <div class="space-y-3">
                    @foreach($pendingPrompts as $prompt)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <svg class="h-5 w-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h4 class="font-semibold text-gray-900">{{ $prompt->event_title }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $prompt->prompt_text }}</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <span>{{ \Carbon\Carbon::parse($prompt->event_end_time)->format('D, M j • g:i A') }}</span>
                                        <span class="mx-2">•</span>
                                        <span class="text-amber-600">{{ \Carbon\Carbon::parse($prompt->event_end_time)->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="ml-4 flex gap-2">
                                    <button wire:click="skipPrompt('{{ $prompt->id }}')" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md transition">
                                        Skip
                                    </button>
                                    <button wire:click="openPrompt('{{ $prompt->id }}')" class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition">
                                        Rate
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Next Event Widget -->
        @if($user->calendar_sync_enabled && $nextEvent)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Event</h3>
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-gray-900">{{ $nextEvent->title }}</h4>
                            </div>

                            @if($nextEvent->description)
                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($nextEvent->description, 100) }}</p>
                            @endif

                            <div class="flex items-center text-sm text-gray-700 mb-2">
                                <svg class="h-4 w-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($nextEvent->start_time)->format('D, M j, Y • g:i A') }}</span>
                            </div>

                            @if($nextEvent->location)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $nextEvent->location }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="ml-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ \Carbon\Carbon::parse($nextEvent->start_time)->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p class="text-xs text-yellow-800">
                                <strong>You'll be asked to log your mood</strong> after this event to help track how your activities affect your emotional wellness.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Mood Entries -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Mood Entries</h3>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($recentMoods->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($recentMoods as $mood)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-3">
                                                @if($mood->mood_score <= 3)
                                                    😢
                                                @elseif($mood->mood_score <= 5)
                                                    😐
                                                @elseif($mood->mood_score <= 7)
                                                    🙂
                                                @else
                                                    😊
                                                @endif
                                            </span>
                                            <div>
                                                <div class="flex items-center">
                                                    <span class="font-semibold text-gray-900">{{ $mood->mood_score }}/10</span>
                                                    <span class="ml-2 px-2 py-1 text-xs rounded-full
                                                        @if($mood->mood_score <= 3) bg-red-100 text-red-800
                                                        @elseif($mood->mood_score <= 5) bg-yellow-100 text-yellow-800
                                                        @elseif($mood->mood_score <= 7) bg-green-100 text-green-800
                                                        @else bg-blue-100 text-blue-800
                                                        @endif">
                                                        {{ $mood->mood_category }}
                                                    </span>
                                                </div>
                                                @if($mood->note)
                                                    <div class="mt-2 p-2 bg-gray-50 rounded border-l-2 border-purple-300">
                                                        <p class="text-sm text-gray-700 italic">{{ Str::limit($mood->note, 150) }}</p>
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-400 mt-1">No description provided</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $mood->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2">No mood entries yet. Start tracking your emotions!</p>
                        <button wire:click="$dispatch('openMoodEntryModal')" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                            Log Your First Mood
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mood Entry Form Modal -->
    <livewire:mood-entry-form />

    <!-- Mood Rating Modal for Past Events -->
    @if($showModal && $selectedPrompt)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button wire:click="closeModal" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="sm:flex sm:items-start mb-4">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                                How did you feel?
                            </h3>
                            <div class="bg-purple-50 border border-purple-200 rounded p-3 mb-4">
                                <p class="text-sm font-medium text-purple-900">{{ $selectedPrompt->event_title }}</p>
                                <p class="text-xs text-purple-700 mt-1">{{ \Carbon\Carbon::parse($selectedPrompt->event_start_time)->format('D, M j, Y • g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mood Score (1-10)
                        </label>
                        <input type="range" wire:model.live="moodScore" min="1" max="10" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-gray-600 mt-1">
                            <span>😢 Very Low</span>
                            <span class="font-bold text-lg text-purple-600">{{ $moodScore }}</span>
                            <span>😊 Very High</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tell us more (optional)
                        </label>
                        <textarea wire:model="note" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="How did this event affect your mood? What were your thoughts and feelings?"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-400">Maximum 1000 characters</span>
                            <span class="text-xs text-gray-400">{{ strlen($note ?? '') }}/1000</span>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button wire:click="submitMood" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition">
                            Save Mood
                        </button>
                        <button wire:click="closeModal" class="inline-flex justify-center items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg shadow-md transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
