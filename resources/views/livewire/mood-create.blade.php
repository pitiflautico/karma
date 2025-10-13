<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Log Your Mood</h2>
                    <p class="mt-1 text-sm text-gray-600">Take a moment to record how you're feeling</p>
                </div>

                @if($calendarEvent)
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="h-6 w-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <form wire:submit.prevent="save">
                    <!-- Mood Score Slider -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            How are you feeling?
                        </label>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-3xl">😢</span>
                            <span class="text-3xl">😐</span>
                            <span class="text-3xl">🙂</span>
                            <span class="text-3xl">😊</span>
                        </div>
                        <input
                            type="range"
                            min="1"
                            max="10"
                            wire:model.live="moodScore"
                            class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                        >
                        <div class="text-center mt-3">
                            <span class="text-4xl font-bold text-purple-600">{{ $moodScore }}</span>
                            <span class="text-xl text-gray-600">/10</span>
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

                        <div class="text-center mt-3">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-medium {{ $badgeColors[$category['color']] }}">
                                {{ $category['label'] }} Mood
                            </span>
                        </div>

                        @error('moodScore')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description Textarea -->
                    <div class="mb-6">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                            Describe your mood <span class="text-gray-500 text-xs font-normal">(optional)</span>
                        </label>
                        <p class="text-sm text-gray-600 mb-3">
                            Share details about what influenced your mood. This helps create more personalized insights and professional reports.
                        </p>

                        <!-- Suggestion prompts -->
                        <div class="mb-3 bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <p class="text-sm text-purple-700 font-medium mb-2">💡 Try describing:</p>
                            <ul class="text-sm text-purple-600 space-y-1">
                                <li>• What happened today that affected your mood?</li>
                                <li>• How are you feeling physically and mentally?</li>
                                <li>• Any challenges or accomplishments?</li>
                                <li>• Interactions with others or personal reflections</li>
                            </ul>
                        </div>

                        <textarea
                            id="note"
                            wire:model="note"
                            rows="6"
                            class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            placeholder="Example: Had a productive meeting with my team where we solved a complex problem. Felt accomplished after completing a difficult task ahead of schedule. However, I'm a bit stressed about upcoming deadlines next week..."
                        ></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">Maximum 500 characters</span>
                            <span class="text-xs {{ strlen($note ?? '') > 500 ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                {{ strlen($note ?? '') }}/500
                            </span>
                        </div>
                        @error('note')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            wire:click="cancel"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2.5 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        >
                            Save Mood Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
