<div class="min-h-screen bg-[#F7F3EF]">
    <!-- Top Section with Mood Info -->
    <div class="relative bg-white pb-16">
        <!-- Safe Area Background Extension -->
        <div class="absolute top-0 left-0 right-0 bg-white" style="height: env(safe-area-inset-top, 0px);"></div>

        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between" style="padding-top: max(1rem, env(safe-area-inset-top, 0px) + 1rem);">
            <!-- Profile Picture -->
            <div class="w-10 h-10 rounded-full bg-gray-800 overflow-hidden flex-shrink-0">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-sm font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Title -->
            <h1 class="text-lg font-semibold text-gray-900">My Mood</h1>

            <!-- Hamburger Menu -->
            <div x-data="{ open: false }" class="relative">
                <!-- Hamburger Button -->
                <button @click="open = !open" class="text-gray-700 p-2">
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
                    class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl overflow-hidden z-50"
                    style="display: none;">

                    <!-- Mood History Link -->
                    <a
                        href="{{ route('mood.history') }}"
                        class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-3 text-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="font-medium">Mood History</span>
                    </a>

                    <!-- Calendar Link -->
                    <a
                        href="{{ route('calendar') }}"
                        class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-3 text-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Calendar</span>
                    </a>

                    <!-- Settings Link -->
                    <a
                        href="{{ route('settings') }}"
                        class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-3 text-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">Settings</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-100"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 text-red-600 transition-colors">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Average Mood Section -->
        @if($moodData)
            <div class="flex flex-col items-center px-6 py-4">
                <!-- Mood Icon -->
                <img src="{{ asset('images/moods/' . $moodData['icon']) }}"
                     alt="{{ $moodData['name'] }}"
                     class="w-24 h-24 mb-3">

                <!-- Mood Name -->
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $moodData['name'] }}</h2>

                <!-- Logged Time -->
                @if($moodData['logged_time'])
                    <p class="text-sm text-gray-500 mb-1">Logged today at {{ $moodData['logged_time'] }}</p>
                @else
                    <p class="text-sm text-gray-500 mb-1">Based on last 7 days</p>
                @endif

                <!-- Context Message -->
                <p class="text-sm text-gray-600">{{ $moodData['message'] }}</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center px-6 py-8">
                <div class="text-6xl mb-3">😊</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Track Your Mood</h2>
                <p class="text-sm text-gray-500">Start logging your moods to see insights</p>
            </div>
        @endif

        <!-- Curved Bottom with White Background -->
        <div class="absolute bottom-0 left-0 right-0 overflow-hidden h-12">
            <div class="absolute w-[300vw] left-1/2 -translate-x-1/2 bg-[#F7F3EF] rounded-full h-[300vw] allow-overflow"></div>
        </div>
    </div>

    <!-- Floating Add Button (positioned over the curve) -->
    <div class="relative -mt-7 flex justify-center z-10">
        <button
            wire:click="openMoodEntryModal"
            class="w-14 h-14 p-4 bg-gradient-to-br from-amber-700 to-amber-800 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-8 space-y-8" style="padding-bottom: max(2rem, env(safe-area-inset-bottom, 0px) + 2rem);">

        <!-- Mood Insight (Streak) -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between h-5">
                <h3 class="text-base font-bold text-gray-900">Mood Insight</h3>
                <a href="{{ route('mood.history') }}" class="text-sm font-medium text-gray-500">See All</a>
            </div>
            <div class="bg-white rounded-3xl p-4 flex gap-4 overflow-hidden">
                <div class="flex-1 flex flex-col gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $moodStreak }} days</h2>
                    <p class="text-base font-medium text-gray-600">Mood Streak</p>
                    <p class="text-sm font-normal text-gray-600">You've checked in your mood for {{ $moodStreak }} day{{ $moodStreak !== 1 ? 's' : '' }} straight!</p>
                </div>
                <div class="w-24 relative">
                    <img src="{{ asset('images/mood_home_art.png') }}" alt="Mood tracking" class="w-full h-auto">
                </div>
            </div>
        </div>

        <!-- Mood History -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between h-5">
                <h3 class="text-base font-bold text-gray-900">Mood History</h3>
                <a href="{{ route('mood.history') }}" class="text-sm font-medium text-gray-500">See All</a>
            </div>

            @if($recentMoods->count() > 0)
                <div class="flex flex-col gap-2">
                    @foreach($recentMoods as $mood)
                        <div class="p-4 bg-white rounded-3xl flex items-center gap-3 overflow-hidden">
                            <div class="flex-1 flex items-center gap-3">
                                <img src="{{ asset('images/moods/' . $mood->mood_icon) }}"
                                     alt="{{ $mood->mood_name }}"
                                     class="w-6 h-6">

                                <div class="flex-1 flex flex-col gap-2">
                                    <div class="flex flex-col gap-1">
                                        <h4 class="text-base font-bold text-gray-900">{{ $mood->mood_name }}</h4>
                                        @if($mood->note)
                                            <p class="text-sm font-medium text-gray-600 truncate">{{ Str::words($mood->note, 7, '...') }}</p>
                                        @elseif($mood->calendarEvent)
                                            <p class="text-sm font-medium text-gray-600 truncate">{{ $mood->calendarEvent->title }}</p>
                                        @else
                                            <p class="text-sm font-medium text-gray-400 italic">No notes</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-normal text-gray-600">{{ $mood->created_at->format('g:i A') }}</span>
                                <button
                                    type="button"
                                    @click.prevent.stop="Livewire.dispatch('view-mood', { id: '{{ $mood->id }}' })"
                                    class="w-6 h-6 text-gray-600">
                                    <svg class="w-2 h-5" fill="currentColor" viewBox="0 0 8 20">
                                        <path d="M1 2l6 8-6 8" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl p-6 text-center text-gray-500">
                    <p class="text-sm">No mood entries yet</p>
                </div>
            @endif
        </div>

        <!-- Mood Reminder -->
        @if($nextReminder)
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between h-5">
                    <h3 class="text-base font-bold text-gray-900">Mood Reminder</h3>
                    <a href="{{ route('calendar') }}" class="text-sm font-medium text-gray-500">See All</a>
                </div>

                <div class="min-h-14 p-4 bg-white rounded-3xl flex items-center gap-4">
                    <div class="flex-1 flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center gap-2.5">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div class="flex-1 flex flex-col justify-center gap-1">
                            <h4 class="text-base font-semibold text-gray-900">{{ $nextReminder->title }}</h4>
                            <p class="text-sm font-normal text-gray-600">Daily at {{ $nextReminder->start_time->format('g:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9.34 5.66l7 7-7 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mood Goal -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between h-5">
                <h3 class="text-base font-bold text-gray-900">Mood Goal</h3>
                <a href="{{ route('mood.history') }}" class="text-sm font-medium text-gray-500">See All</a>
            </div>

            <div class="p-4 bg-white rounded-3xl flex flex-col gap-4 overflow-hidden">
                <div class="flex flex-col gap-2">
                    @if($moodGoalData['happyStreak'] > 0)
                        <h4 class="text-2xl font-bold text-gray-900">Happy</h4>
                        <p class="text-base font-normal text-gray-600">for {{ $moodGoalData['happyStreak'] }} day{{ $moodGoalData['happyStreak'] !== 1 ? 's' : '' }} straight</p>
                    @else
                        <h4 class="text-2xl font-bold text-gray-900">Track Your Moods</h4>
                        <p class="text-base font-normal text-gray-600">Start building a happy streak!</p>
                    @endif
                </div>

                <!-- Divider -->
                <div class="h-0 border-t border-gray-200"></div>

                <!-- 7 Days Emoji Tracker -->
                <div class="flex justify-between items-start">
                    @foreach($moodGoalData['days'] as $day)
                        @if($day['has_mood'])
                            @php
                                $dayIcon = match(true) {
                                    $day['avg_mood'] <= 2 => 'depressed_icon.svg',
                                    $day['avg_mood'] <= 4 => 'Sad_icon.svg',
                                    $day['avg_mood'] <= 6 => 'Normal_icon.svg',
                                    $day['avg_mood'] <= 8 => 'Happy_icon.svg',
                                    default => 'Great_icon.svg',
                                };
                            @endphp
                            <img src="{{ asset('images/moods/' . $dayIcon) }}" alt="Mood" class="w-8 h-8 relative rounded-full">
                        @else
                            <div class="w-8 h-8 relative bg-gray-200 rounded-full"></div>
                        @endif
                    @endforeach
                </div>

                <!-- Divider -->
                <div class="h-0 border-t border-gray-200"></div>

                <!-- Progress Message -->
                <p class="text-sm font-normal text-gray-600">
                    You have achieved a {{ $moodGoalData['happyStreak'] }} day happy streak so far
                </p>
            </div>
        </div>
    </div>

    <!-- Include Mood Entry Form -->
    @livewire('mood-entry-form')

    <!-- Include Mood Detail Modal -->
    @livewire('mood-detail-modal')
</div>

@push('scripts')
<script>
    // Auto-open mood entry modal if URL parameter is present
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('openMoodEntry') === '1') {
            // Dispatch Livewire event to open mood entry modal
            Livewire.dispatch('openMoodEntryModal');

            // Remove the parameter from URL to clean it up
            const cleanUrl = window.location.pathname + window.location.search.replace(/[?&]openMoodEntry=1/, '').replace(/^&/, '?');
            window.history.replaceState({}, document.title, cleanUrl || window.location.pathname);
        }
    });
</script>
@endpush
