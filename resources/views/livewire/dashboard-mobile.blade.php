<div class="min-h-screen bg-gradient-to-b from-purple-50 to-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8 rounded-b-[3rem] shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-white">Welcome, {{ $user->name }}!</h1>

            <!-- Hamburger Menu -->
            <div x-data="{ open: false }" class="relative">
                <!-- Hamburger Button -->
                <button @click="open = !open" class="text-white/90 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl overflow-hidden z-50"
                    style="display: none;">

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 text-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Average Mood Card -->
        @if($averageMood)
        <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-4">
            <p class="text-white/90 text-sm mb-1">7-Day Average Mood</p>
            <p class="text-4xl font-bold text-white">{{ number_format($averageMood, 1) }}/10</p>
        </div>
        @endif
    </div>

    <!-- Content -->
    <div class="px-6 py-6">
        <!-- Pending Prompts -->
        @if($pendingPrompts->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Pending Mood Check-ins</h2>
            @foreach($pendingPrompts as $prompt)
            <div class="bg-white rounded-2xl p-4 mb-3 shadow-sm border border-gray-100">
                <p class="text-gray-900 font-medium mb-2">{{ $prompt->event_title }}</p>
                <p class="text-gray-500 text-sm mb-3">{{ $prompt->event_end_time->format('M d, g:i A') }}</p>
                <div class="flex gap-2">
                    <button
                        wire:click="openPrompt({{ $prompt->id }})"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-full text-sm font-medium transition-colors">
                        Log Mood
                    </button>
                    <button
                        wire:click="skipPrompt({{ $prompt->id }})"
                        class="px-4 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-full text-sm font-medium transition-colors">
                        Skip
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Recent Moods -->
        @if($recentMoods->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Recent Mood Entries</h2>
            @foreach($recentMoods as $mood)
            <div class="bg-white rounded-2xl p-4 mb-3 shadow-sm border border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $mood->mood_score }}/10</p>
                        <p class="text-gray-500 text-sm">{{ $mood->created_at->format('M d, g:i A') }}</p>
                    </div>
                </div>
                @if($mood->note)
                <p class="text-gray-700 text-sm mt-2">{{ $mood->note }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Next Event -->
        @if($nextEvent)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Upcoming Event</h2>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-gray-900 font-medium">{{ $nextEvent->title }}</p>
                <p class="text-gray-500 text-sm">{{ $nextEvent->start_time->format('M d, g:i A') }}</p>
            </div>
        </div>
        @endif

        <!-- Empty State -->
        @if($recentMoods->count() === 0 && $pendingPrompts->count() === 0)
        <div class="text-center py-12">
            <svg class="w-20 h-20 text-purple-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No mood entries yet</h3>
            <p class="text-gray-500">Start tracking your mood to see insights here!</p>
        </div>
        @endif
    </div>

    <!-- Mood Modal -->
    @if($showModal && $selectedPrompt)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-900 mb-4">How did you feel?</h3>
            <p class="text-gray-600 mb-4">{{ $selectedPrompt->event_title }}</p>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Mood Score (1-10)</label>
                <input
                    type="range"
                    wire:model.live="moodScore"
                    min="1"
                    max="10"
                    class="w-full h-2 bg-purple-200 rounded-lg appearance-none cursor-pointer">
                <div class="text-center mt-2">
                    <span class="text-3xl font-bold text-purple-600">{{ $moodScore }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Note (optional)</label>
                <textarea
                    wire:model="note"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                    rows="3"
                    placeholder="Add any thoughts..."></textarea>
            </div>

            <div class="flex gap-3">
                <button
                    wire:click="closeModal"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-full font-medium transition-colors">
                    Cancel
                </button>
                <button
                    wire:click="submitMood"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-full font-medium transition-colors">
                    Submit
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if(session()->has('success'))
    <div class="fixed bottom-4 left-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif
</div>
