<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Header -->
    <div class="bg-white">
        <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
            <x-back-button />
            <h1 class="text-base font-semibold text-[#292524]">Create Event</h1>
            <div class="w-7"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-6">
        <!-- Form -->
        <form wire:submit.prevent="createEvent" class="space-y-6">
            <!-- Event Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-[#292524] mb-2">
                    Event Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    placeholder="e.g., Family Dinner, Team Meeting"
                    class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-[#292524] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all"
                >
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-[#292524] mb-2">
                    Description <span class="text-gray-400 text-sm">(optional)</span>
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    placeholder="Add more details about the event..."
                    class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-[#292524] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all resize-none"
                ></textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Date -->
            <div>
                <label for="event_date" class="block text-sm font-medium text-[#292524] mb-2">
                    Date <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    id="event_date"
                    wire:model="eventDate"
                    min="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-[#292524] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all"
                >
                @error('eventDate')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Time -->
            <div>
                <label for="event_time" class="block text-sm font-medium text-[#292524] mb-2">
                    Time <span class="text-red-500">*</span>
                </label>
                <input
                    type="time"
                    id="event_time"
                    wire:model="eventTime"
                    class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-[#292524] focus:outline-none focus:ring-2 focus:ring-[#8B5CF6] focus:border-transparent transition-all"
                >
                @error('eventTime')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="p-4 bg-blue-50 rounded-xl">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Custom Event</p>
                        <p class="text-sm text-blue-700 mt-1">All group members will be able to rate this event with their mood.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-[#8B5CF6] hover:bg-[#7C3AED] text-white font-semibold py-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Create Event</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
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
