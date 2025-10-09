<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ $editMode ? 'Edit Mood Entry' : 'Log Your Mood' }}
                                    </h3>

                                    @if($calendarEvent)
                                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="flex items-start">
                                                <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm font-medium text-blue-900">
                                                        {{ $calendarEvent->title }}
                                                    </p>
                                                    <p class="text-xs text-blue-700 mt-1">
                                                        {{ $calendarEvent->start_time->format('M d, Y • g:i A') }}
                                                        @if($calendarEvent->location)
                                                            • {{ $calendarEvent->location }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        <!-- Mood Score Slider -->
                                        <div class="mb-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                How are you feeling?
                                            </label>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-2xl">😢</span>
                                                <span class="text-2xl">😐</span>
                                                <span class="text-2xl">🙂</span>
                                                <span class="text-2xl">😊</span>
                                            </div>
                                            <input
                                                type="range"
                                                min="1"
                                                max="10"
                                                wire:model.live="moodScore"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                            >
                                            <div class="text-center mt-2">
                                                <span class="text-3xl font-bold text-purple-600">{{ $moodScore }}</span>
                                                <span class="text-gray-600">/10</span>
                                            </div>

                                            @php
                                                $category = $this->getMoodCategory();
                                                $badgeColors = [
                                                    'red' => 'bg-red-100 text-red-800',
                                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                                    'green' => 'bg-green-100 text-green-800',
                                                    'blue' => 'bg-blue-100 text-blue-800',
                                                ];
                                            @endphp

                                            <div class="text-center mt-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeColors[$category['color']] }}">
                                                    {{ $category['label'] }} Mood
                                                </span>
                                            </div>

                                            @error('moodScore')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Description Textarea -->
                                        <div class="mb-4">
                                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                                Describe your mood <span class="text-gray-500 text-xs font-normal">(optional)</span>
                                            </label>
                                            <p class="text-xs text-gray-500 mb-2">
                                                Share details about what influenced your mood. This helps create more personalized insights and professional reports.
                                            </p>

                                            <!-- Suggestion prompts -->
                                            <div class="mb-2 bg-purple-50 border border-purple-200 rounded p-2">
                                                <p class="text-xs text-purple-700 font-medium mb-1">💡 Try describing:</p>
                                                <ul class="text-xs text-purple-600 space-y-1">
                                                    <li>• What happened today that affected your mood?</li>
                                                    <li>• How are you feeling physically and mentally?</li>
                                                    <li>• Any challenges or accomplishments?</li>
                                                    <li>• Interactions with others or personal reflections</li>
                                                </ul>
                                            </div>

                                            <textarea
                                                id="note"
                                                wire:model="note"
                                                rows="5"
                                                class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Example: Had a productive meeting with my team where we solved a complex problem. Felt accomplished after completing a difficult task ahead of schedule. However, I'm a bit stressed about upcoming deadlines next week..."
                                            ></textarea>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs text-gray-400">Maximum 500 characters</span>
                                                <span class="text-xs text-gray-400 {{ strlen($note ?? '') > 500 ? 'text-red-500 font-medium' : '' }}">
                                                    {{ strlen($note ?? '') }}/500
                                                </span>
                                            </div>
                                            @error('note')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                {{ $editMode ? 'Update Mood' : 'Save Mood Entry' }}
                            </button>
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
