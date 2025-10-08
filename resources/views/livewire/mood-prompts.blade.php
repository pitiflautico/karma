<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Mood Check-ins</h2>
            <p class="text-gray-600">Events waiting for your mood feedback</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            @if(count($prompts) > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($prompts as $prompt)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $prompt->event_title }}</h3>
                                    </div>

                                    <p class="text-gray-700 mb-2">{{ $prompt->prompt_text }}</p>

                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($prompt->event_start_time)->format('D, M j, Y • g:i A') }}</span>
                                        <span class="mx-2">•</span>
                                        <span class="text-purple-600">{{ \Carbon\Carbon::parse($prompt->event_end_time)->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="ml-4 flex flex-col gap-2">
                                    <button wire:click="openPrompt('{{ $prompt->id }}')" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Log Mood
                                    </button>
                                    <button wire:click="dismissPrompt('{{ $prompt->id }}')" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md shadow-sm transition">
                                        Dismiss
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending mood check-ins</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        You're all caught up! New prompts will appear after calendar events.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Mood Entry Modal -->
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
