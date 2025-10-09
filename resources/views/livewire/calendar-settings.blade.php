<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Calendar Settings</h2>
            <p class="mt-2 text-gray-600">Manage your Google Calendar integration and notification preferences</p>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Google Calendar Connection -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Google Calendar Connection</h3>

                @if($isConnected)
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-green-900">Connected to Google Calendar</p>
                                @if($lastSyncAt)
                                    <p class="text-sm text-green-700">Last synced: {{ $lastSyncAt->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                        <button
                            wire:click="disconnectCalendar"
                            wire:confirm="Are you sure you want to disconnect Google Calendar? Your existing data will be preserved."
                            class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition"
                        >
                            Disconnect
                        </button>
                    </div>

                    <!-- Sync Controls -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-900">Automatic Calendar Sync</label>
                                <p class="text-xs text-gray-500">Sync your calendar events automatically every 15 minutes</p>
                            </div>
                            <button
                                wire:click="toggleCalendarSync"
                                type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 {{ $calendarSyncEnabled ? 'bg-purple-600' : 'bg-gray-200' }}"
                            >
                                <span class="sr-only">Enable calendar sync</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $calendarSyncEnabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <button
                                wire:click="syncNow"
                                wire:loading.attr="disabled"
                                wire:target="syncNow"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="syncNow" class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <svg wire:loading wire:target="syncNow" class="animate-spin h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="syncNow">Sync Now</span>
                                <span wire:loading wire:target="syncNow">Syncing...</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Connect Your Google Calendar</h3>
                        <p class="text-gray-600 mb-6">Link your Google Calendar to automatically track mood after events</p>
                        <a
                            href="{{ route('auth.google.sync') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        >
                            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Connect Google Calendar
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quiet Hours Settings -->
        @if($isConnected && $calendarSyncEnabled)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quiet Hours</h3>
                    <p class="text-sm text-gray-600 mb-6">Set times when you don't want to receive mood reminders</p>

                    <form wire:submit.prevent="saveQuietHours">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="quietHoursStart" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quiet Hours Start
                                </label>
                                <input
                                    type="time"
                                    id="quietHoursStart"
                                    wire:model="quietHoursStart"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                >
                                @error('quietHoursStart')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="quietHoursEnd" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quiet Hours End
                                </label>
                                <input
                                    type="time"
                                    id="quietHoursEnd"
                                    wire:model="quietHoursEnd"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                >
                                @error('quietHoursEnd')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">
                                Mood reminders will not be sent during these hours. For example: 22:00 - 08:00
                            </p>
                        </div>

                        <div class="mt-6">
                            <button
                                type="submit"
                                class="inline-flex justify-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                Save Quiet Hours
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
