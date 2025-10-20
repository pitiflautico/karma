<div class="min-h-screen bg-[#F7F3EF]" x-data="{ selectedMood: @entangle('selectedMood') }">
    <!-- Header -->
    <div class="bg-white">
        <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
            <x-back-button />
            <h1 class="text-base font-semibold text-[#292524]">Rate Event</h1>
            <div class="w-7"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-6 space-y-6">
        <!-- Event Info Card -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-start gap-3 mb-4">
                <!-- Event Icon -->
                <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center
                    {{ $event['is_custom'] ?? true ? 'bg-[#8B5CF6]/10' : 'bg-blue-50' }}">
                    @if($event['is_custom'] ?? true)
                        <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>

                <div class="flex-1">
                    <h2 class="text-lg font-bold text-[#292524]">{{ $event['title'] ?? 'Event' }}</h2>
                    <p class="text-sm text-[#57534e] mt-1">{{ $event['event_date_formatted'] ?? '' }}</p>
                    @if(isset($event['description']) && $event['description'])
                        <p class="text-sm text-[#57534e] mt-2">{{ $event['description'] }}</p>
                    @endif
                </div>

                <!-- Source Badge -->
                <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                    {{ $event['is_custom'] ?? true ? 'bg-[#8B5CF6]/10 text-[#8B5CF6]' : 'bg-blue-50 text-blue-600' }}">
                    {{ $event['is_custom'] ?? true ? 'Custom' : 'Calendar' }}
                </span>
            </div>
        </div>

        <!-- My Rating Section -->
        <div class="bg-white rounded-2xl p-6">
            <h3 class="text-base font-bold text-[#292524] mb-4">My Rating</h3>

            <!-- Mood Selector -->
            <div class="mb-6">
                <p class="text-sm text-[#57534e] mb-4">How did you feel?</p>
                <div class="grid grid-cols-5 gap-3">
                    @foreach($moods as $mood)
                        <button
                            type="button"
                            wire:click="selectMood({{ $mood['score'] }})"
                            @click="selectedMood = {{ $mood['score'] }}"
                            class="flex flex-col items-center gap-2 p-3 rounded-xl transition-all
                                {{ $selectedMood == $mood['score'] ? 'bg-[#8B5CF6]/10 ring-2 ring-[#8B5CF6]' : 'bg-gray-50 hover:bg-gray-100' }}">
                            <span class="text-3xl">{{ $mood['icon'] }}</span>
                            <span class="text-xs font-medium text-[#292524]">{{ $mood['score'] }}</span>
                        </button>
                    @endforeach
                </div>
                @error('selectedMood')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selected Mood Name -->
            @if($selectedMood)
                <div class="mb-6 text-center">
                    <p class="text-lg font-semibold text-[#292524]">{{ $moods[$selectedMood - 1]['name'] ?? '' }}</p>
                </div>
            @endif

            <!-- Note Input -->
            <div>
                <label for="note" class="block text-sm font-medium text-[#292524] mb-2">
                    Add a note <span class="text-gray-400">(optional)</span>
                </label>
                <textarea
                    id="note"
                    wire:model="note"
                    rows="3"
                    placeholder="How was it? What made you feel this way?"
                    class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-[#292524] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all resize-none"
                ></textarea>
                @error('note')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button
                    wire:click="submitRating"
                    wire:loading.attr="disabled"
                    :disabled="!selectedMood"
                    class="w-full bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold py-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>
                        {{ $userRating ? 'Update Rating' : 'Submit Rating' }}
                    </span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>

        <!-- Group Mood Section -->
        <div class="bg-white rounded-2xl p-6">
            <h3 class="text-base font-bold text-[#292524] mb-6">Group Mood</h3>

            @if(isset($groupStats['rating_count']) && $groupStats['rating_count'] > 0)
                <!-- Average Mood -->
                <div class="text-center mb-6">
                    <p class="text-sm text-[#57534e] mb-2">Average Rating</p>
                    <div class="text-5xl mb-2">{{ $groupStats['mood_emoji'] ?? '😊' }}</div>
                    <p class="text-3xl font-bold text-[#292524] mb-1">{{ number_format($groupStats['average_mood'], 1) }}</p>
                    <p class="text-sm text-[#57534e]">
                        {{ $groupStats['rating_count'] }} out of {{ $groupStats['total_members'] }} members rated
                    </p>
                </div>

                <!-- Mood Distribution -->
                @if(isset($groupStats['distribution']) && count($groupStats['distribution']) > 0)
                    <div class="pt-6 border-t border-gray-100">
                        <p class="text-sm font-medium text-[#292524] mb-4">Distribution</p>
                        <div class="space-y-3">
                            @foreach($groupStats['distribution'] as $dist)
                                @if($dist['count'] > 0)
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl w-8">{{ $dist['emoji'] }}</span>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-[#8B5CF6] h-2 rounded-full transition-all"
                                                     style="width: {{ ($dist['count'] / $groupStats['rating_count']) * 100 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm text-[#57534e] w-8 text-right">{{ $dist['count'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Privacy Notice -->
                <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                    <div class="flex gap-2">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-xs text-gray-600">Individual ratings are private. Only aggregated data is shown.</p>
                    </div>
                </div>
            @else
                <!-- No Ratings Yet -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <p class="text-[#57534e]">No ratings yet</p>
                    <p class="text-sm text-gray-400 mt-1">Be the first to rate this event!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="fixed top-20 left-4 right-4 z-50">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
