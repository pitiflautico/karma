<div class="min-h-screen bg-[#F7F3EF] pb-24">
    <!-- Header -->
    <div class="bg-[#F7F3EF] pt-12 pb-6 px-6 text-center" style="padding-top: max(3rem, env(safe-area-inset-top, 0px) + 3rem);">
        <div class="flex justify-start px-2 mb-6">
            <button onclick="if (window.NativeAppBridge) { window.NativeAppBridge.goBack(); } else { window.history.back(); }" class="text-[#292524]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full mb-3">
            <span class="text-3xl">😊</span>
        </div>
        <h1 class="text-[24px] font-bold text-[#292524] mb-2">Mood Insight</h1>
        <p class="text-sm font-normal text-[#57534e] max-w-sm mx-auto leading-relaxed">Track and analyze your key health indicators to optimize your wellness journey.</p>
    </div>

    <!-- Month Selector -->
    <div class="px-6 py-3" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between bg-white rounded-full py-3 px-6 shadow-sm">
            <svg class="w-5 h-5 text-[#292524]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-base font-semibold text-[#292524]">{{ $monthName }}</span>
            <svg class="w-5 h-5 text-[#292524] transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Month Dropdown -->
        <div x-show="open"
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-6 right-6 mt-2 bg-white rounded-2xl shadow-lg z-50 max-h-80 overflow-y-auto">
            <div class="p-2">
                @php
                    $currentYear = date('Y');
                    $months = [];
                    // Last 12 months
                    for ($i = 0; $i < 12; $i++) {
                        $date = \Carbon\Carbon::now()->subMonths($i);
                        $months[] = [
                            'label' => $date->format('F Y'),
                            'month' => $date->month,
                            'year' => $date->year,
                        ];
                    }
                @endphp
                @foreach($months as $month)
                    <button wire:click="$set('selectedMonth', {{ $month['month'] }}); $set('selectedYear', {{ $month['year'] }})"
                            @click="open = false"
                            class="w-full text-left px-4 py-3 rounded-xl hover:bg-[#f7f3ef] transition-colors {{ $selectedMonth == $month['month'] && $selectedYear == $month['year'] ? 'bg-[#f7f3ef] font-semibold' : '' }}">
                        <span class="text-base text-[#292524]">{{ $month['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    @if($totalEntries === 0)
        <!-- No Entries Message -->
        <div class="px-6 py-6 text-center">
            <div class="bg-white rounded-3xl p-8 shadow-sm">
                <div class="text-5xl mb-4">📊</div>
                <h3 class="text-lg font-bold text-[#292524] mb-2">No Entries for This Month</h3>
                <p class="text-sm font-normal text-[#57534e]">Start tracking your mood to see insights here.</p>
            </div>
        </div>
    @else
        <!-- Most Logged Mood -->
        <div class="px-6 py-2">
            <div class="bg-white rounded-3xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99935 0.833496C3.94926 0.833496 0.666016 4.11674 0.666016 8.16683C0.666016 10.8825 2.1422 13.2518 4.33268 14.5189V16.4168H11.666V14.5189C13.8565 13.2518 15.3327 10.8825 15.3327 8.16683C15.3327 4.11674 12.0494 0.833496 7.99935 0.833496ZM2.49935 8.16683C2.49935 5.12926 4.96178 2.66683 7.99935 2.66683C11.0369 2.66683 13.4993 5.12926 13.4993 8.16683C13.4993 10.3595 12.2163 12.2543 10.356 13.138L9.83268 13.3866V14.5835H6.16602V13.3866L5.64268 13.138C3.78238 12.2543 2.49935 10.3595 2.49935 8.16683Z" fill="#A8A29E"/>
                        <path d="M4.33268 17.3335V19.1668H11.666V17.3335H4.33268Z" fill="#A8A29E"/>
                    </svg>
                    <h2 class="text-base font-bold text-[#292524]">Most Logged Mood</h2>
                </div>

                <!-- Mood Bars -->
                <div class="space-y-3">
                    @foreach($mostLoggedMoods as $mood)
                        <div class="flex items-center gap-3">
                            <!-- Mood Icon SVG -->
                            <div class="flex items-center justify-center w-10 h-10 flex-shrink-0">
                                <img src="{{ asset('images/moods/' . $mood['icon']) }}"
                                     alt="{{ $mood['name'] }}"
                                     class="w-10 h-10">
                            </div>

                            <!-- Bar with count -->
                            <div class="flex-1 flex items-center gap-2">
                                <div class="flex-1 h-10 bg-white rounded-full border-2 relative"
                                     style="border-color: {{ $mood['color'] }};">
                                    <!-- Fill bar (only fills proportional part) -->
                                    <div class="absolute inset-0 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500"
                                             style="width: {{ $topMood ? ($mood['count'] / $topMood['count']) * 100 : 0 }}%;
                                                    background: {{ $mood['color'] }}; opacity: 0.15;">
                                        </div>
                                    </div>
                                </div>
                                <!-- Count number -->
                                <span class="text-base font-semibold text-[#292524] w-8 text-right">{{ $mood['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($topMood)
                    <p class="text-sm font-normal text-[#57534e] mt-4 leading-relaxed">
                        Your most logged mood is <span class="font-semibold text-[#292524]">overjoyed</span> with {{ $topMood['count'] }} entries this month.
                    </p>
                @endif
            </div>
        </div>

        <!-- Mood Over Time -->
        <div class="px-6 py-2">
            <div class="bg-white rounded-3xl p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.25065 2.64007V0.75H5.41732V3.11639C2.72981 4.20427 0.833984 6.83907 0.833984 9.91667C0.833984 13.9668 4.11723 17.25 8.16732 17.25H11.834C15.8841 17.25 19.1673 13.9668 19.1673 9.91667C19.1673 6.83907 17.2715 4.20427 14.584 3.11639V0.75H12.7507V2.64007C12.4504 2.60262 12.1444 2.58333 11.834 2.58333H8.16732C7.85688 2.58333 7.55095 2.60262 7.25065 2.64007ZM15.9335 6.25C14.9264 5.12479 13.4629 4.41667 11.834 4.41667H8.16732C6.5384 4.41667 5.07488 5.12479 4.06779 6.25H15.9335ZM2.98028 8.08333H17.021C17.2237 8.65676 17.334 9.27384 17.334 9.91667C17.334 12.9542 14.8715 15.4167 11.834 15.4167H8.16732C5.12975 15.4167 2.66732 12.9542 2.66732 9.91667C2.66732 9.27384 2.7776 8.65676 2.98028 8.08333Z" fill="#A8A29E"/>
                    </svg>
                    <h2 class="text-base font-bold text-[#292524]">Mood Over Time</h2>
                </div>

                <!-- Chart with mood legend -->
                <div class="flex gap-3">
                    <!-- Mood Legend (left side) -->
                    <div class="flex flex-col justify-around py-2">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <img src="{{ asset('images/moods/Great_icon.svg') }}" alt="Great" class="w-7 h-7">
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <img src="{{ asset('images/moods/Happy_icon.svg') }}" alt="Happy" class="w-7 h-7">
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <img src="{{ asset('images/moods/Normal_icon.svg') }}" alt="Normal" class="w-7 h-7">
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <img src="{{ asset('images/moods/Sad_icon.svg') }}" alt="Sad" class="w-7 h-7">
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <img src="{{ asset('images/moods/depressed_icon.svg') }}" alt="Depressed" class="w-7 h-7">
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="flex-1 relative h-32">
                        <canvas id="moodOverTimeChart"></canvas>
                    </div>
                </div>

                <!-- Day Labels -->
                <div class="flex justify-between mt-1 text-xs font-normal text-[#a8a29e]" style="margin-left: 44px;">
                    @php
                        $daysToShow = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    @endphp
                    @foreach($daysToShow as $day)
                        <span>{{ $day }}</span>
                    @endforeach
                </div>

                <p class="text-sm font-normal text-[#57534e] mt-4 leading-relaxed">
                    Your mood is improving over time, congratulations!
                </p>
            </div>
        </div>

        <!-- Most Logged Trigger -->
        @if($mostLoggedTags->count() > 0)
            <div class="px-6 py-2">
                <div class="bg-white rounded-3xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.08398 6.3335C9.08398 3.29593 11.5464 0.833496 14.584 0.833496C17.6216 0.833496 20.084 3.29593 20.084 6.3335C20.084 9.37106 17.6216 11.8335 14.584 11.8335C11.5464 11.8335 9.08398 9.37106 9.08398 6.3335ZM14.584 2.66683C12.5589 2.66683 10.9173 4.30845 10.9173 6.3335C10.9173 8.35854 12.5589 10.0002 14.584 10.0002C16.609 10.0002 18.2507 8.35854 18.2507 6.3335C18.2507 4.30845 16.609 2.66683 14.584 2.66683Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.833984 11.8335C0.833984 9.30219 2.88601 7.25016 5.41732 7.25016C7.94862 7.25016 10.0007 9.30219 10.0007 11.8335C10.0007 14.3648 7.94862 16.4168 5.41732 16.4168C2.88601 16.4168 0.833984 14.3648 0.833984 11.8335ZM5.41732 9.0835C3.89853 9.0835 2.66732 10.3147 2.66732 11.8335C2.66732 13.3523 3.89853 14.5835 5.41732 14.5835C6.9361 14.5835 8.16732 13.3523 8.16732 11.8335C8.16732 10.3147 6.9361 9.0835 5.41732 9.0835Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.209 12.7502C11.4371 12.7502 10.0007 14.1866 10.0007 15.9585C10.0007 17.7304 11.4371 19.1668 13.209 19.1668C14.9809 19.1668 16.4173 17.7304 16.4173 15.9585C16.4173 14.1866 14.9809 12.7502 13.209 12.7502ZM11.834 15.9585C11.834 15.1991 12.4496 14.5835 13.209 14.5835C13.9684 14.5835 14.584 15.1991 14.584 15.9585C14.584 16.7179 13.9684 17.3335 13.209 17.3335C12.4496 17.3335 11.834 16.7179 11.834 15.9585Z" fill="#A8A29E"/>
                        </svg>
                        <h2 class="text-base font-bold text-[#292524]">Most logged trigger</h2>
                    </div>

                    <!-- Tags as Bubbles (circular layout) -->
                    <div class="relative h-64 mb-4">
                        @foreach($mostLoggedTags as $index => $tag)
                            @php
                                // Bubble configurations based on Figma design
                                $bubbles = [
                                    // Joyful (center, largest) - blue
                                    ['size' => 'w-32 h-32', 'bg' => '#C7E9FF', 'text' => '#0284C7', 'fontSize' => 'text-xl', 'pos' => 'left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2'],
                                    // Tired (top left) - gray
                                    ['size' => 'w-20 h-20', 'bg' => '#E7E5E4', 'text' => '#78716C', 'fontSize' => 'text-sm', 'pos' => 'left-8 top-4'],
                                    // Stress (top right) - green
                                    ['size' => 'w-24 h-24', 'bg' => '#D1F4E0', 'text' => '#059669', 'fontSize' => 'text-base', 'pos' => 'right-8 top-6'],
                                    // Normal (bottom left) - pink
                                    ['size' => 'w-20 h-20', 'bg' => '#FCE7F3', 'text' => '#DB2777', 'fontSize' => 'text-sm', 'pos' => 'left-4 bottom-8'],
                                    // Lonely (bottom right) - yellow
                                    ['size' => 'w-24 h-24', 'bg' => '#FEF3C7', 'text' => '#D97706', 'fontSize' => 'text-base', 'pos' => 'right-4 bottom-4'],
                                    // Fini (top center) - purple
                                    ['size' => 'w-16 h-16', 'bg' => '#E0D4F7', 'text' => '#7C3AED', 'fontSize' => 'text-xs', 'pos' => 'left-1/2 top-0 -translate-x-1/2'],
                                ];
                                $bubble = $bubbles[$index % count($bubbles)];
                            @endphp
                            <div class="absolute {{ $bubble['pos'] }} {{ $bubble['size'] }} rounded-full flex items-center justify-center font-semibold {{ $bubble['fontSize'] }} transition-all hover:scale-105"
                                 style="background-color: {{ $bubble['bg'] }}; color: {{ $bubble['text'] }};">
                                {{ $tag->name }}
                            </div>
                        @endforeach
                    </div>

                    @if($topTag)
                        <p class="text-sm font-normal text-[#57534e] leading-relaxed">
                            Your most logged tag is "<span class="font-semibold text-[#292524]">{{ $topTag->name }}</span>" with around {{ $topTag->count }} logs this month
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Common Key Triggers -->
        @if($topTag)
            <div class="px-6 py-2">
                <div class="bg-white rounded-3xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0007 1.6665C5.39795 1.6665 1.66732 5.39713 1.66732 9.99984C1.66732 14.6025 5.39795 18.3332 10.0007 18.3332C14.6034 18.3332 18.334 14.6025 18.334 9.99984C18.334 5.39713 14.6034 1.6665 10.0007 1.6665ZM10.0007 5.83317C10.4609 5.83317 10.834 6.20627 10.834 6.6665V9.99984C10.834 10.4601 10.4609 10.8332 10.0007 10.8332C9.54041 10.8332 9.16732 10.4601 9.16732 9.99984V6.6665C9.16732 6.20627 9.54041 5.83317 10.0007 5.83317ZM10.0007 14.1665C10.4609 14.1665 10.834 13.7934 10.834 13.3332C10.834 12.8729 10.4609 12.4998 10.0007 12.4998C9.54041 12.4998 9.16732 12.8729 9.16732 13.3332C9.16732 13.7934 9.54041 14.1665 10.0007 14.1665Z" fill="#A8A29E"/>
                        </svg>
                        <h2 class="text-base font-bold text-[#292524]">Common Key Triggers</h2>
                    </div>

                    <!-- Main trigger -->
                    <h3 class="text-3xl font-bold text-[#292524] mb-2">{{ $topTag->name }}</h3>
                    <p class="text-sm font-normal text-[#57534e] mb-4">
                        {{ $topTag->name }} is your most common key trigger for your mood
                    </p>

                    <!-- Related triggers as small tags -->
                    <div class="flex flex-wrap gap-2">
                        @foreach($mostLoggedTags->take(4) as $index => $tag)
                            <div class="flex items-center gap-1.5 px-3 py-2 bg-[#f5f5f4] rounded-full">
                                @if($index === 0)
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 0L8.5 5.5L14 7L8.5 8.5L7 14L5.5 8.5L0 7L5.5 5.5L7 0Z" fill="#78716C"/>
                                    </svg>
                                @elseif($index === 1)
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 1L7.5 6.5L13 7L7.5 7.5L7 13L6.5 7.5L1 7L6.5 6.5L7 1Z" fill="#78716C"/>
                                    </svg>
                                @elseif($index === 2)
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="7" cy="7" r="6" stroke="#78716C" stroke-width="2" fill="none"/>
                                    </svg>
                                @else
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 0L8.5 5.5L14 7L8.5 8.5L7 14L5.5 8.5L0 7L5.5 5.5L7 0Z" fill="#78716C"/>
                                    </svg>
                                @endif
                                <span class="text-sm font-medium text-[#57534e]">{{ $tag->name }}</span>
                                <span class="text-xs font-normal text-[#a8a29e]">({{ $tag->count }}x)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Most Happiest Days -->
        @if($happiestDay)
            <div class="px-6 py-2 mb-6">
                <div class="bg-white rounded-3xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.75 3.87508C0.75 5.647 2.18642 7.08341 3.95833 7.08341H14.0417C15.8136 7.08341 17.25 5.647 17.25 3.87508C17.25 2.10317 15.8136 0.666748 14.0417 0.666748H3.95833C2.18642 0.666748 0.75 2.10317 0.75 3.87508ZM3.95833 5.25008C3.19894 5.25008 2.58333 4.63447 2.58333 3.87508C2.58333 3.11569 3.19894 2.50008 3.95833 2.50008L14.0417 2.50008C14.8011 2.50008 15.4167 3.11569 15.4167 3.87508C15.4167 4.63447 14.8011 5.25008 14.0417 5.25008H3.95833Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.75 12.1251C0.75 13.897 2.18642 15.3334 3.95833 15.3334H14.0417C15.8136 15.3334 17.25 13.897 17.25 12.1251C17.25 10.3532 15.8136 8.91675 14.0417 8.91675H3.95833C2.18642 8.91675 0.75 10.3532 0.75 12.1251ZM3.95833 13.5001C3.19894 13.5001 2.58333 12.8845 2.58333 12.1251C2.58333 11.3657 3.19894 10.7501 3.95833 10.7501H14.0417C14.8011 10.7501 15.4167 11.3657 15.4167 12.1251C15.4167 12.8845 14.8011 13.5001 14.0417 13.5001H3.95833Z" fill="#A8A29E"/>
                        </svg>
                        <h2 class="text-base font-bold text-[#292524]">Most Happiest Days</h2>
                    </div>

                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-[#292524] mb-2">{{ $happiestDay['dayName'] }}</h3>
                            <p class="text-sm font-normal text-[#57534e] leading-relaxed">
                                {{ $happiestDay['dayName'] }} is your most happiest days based on our data.
                            </p>
                        </div>
                        <!-- Illustration placeholder - dancing person -->
                        <div class="flex-shrink-0 w-32 h-32 relative">
                            <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <!-- Happy dancing person illustration -->
                                <!-- Head -->
                                <circle cx="70" cy="25" r="15" fill="#FFD4B2"/>
                                <!-- Hair -->
                                <path d="M55 20 Q60 10, 70 10 Q80 10, 85 20" fill="#3F2A1D"/>
                                <!-- Body (purple shirt) -->
                                <ellipse cx="70" cy="55" rx="18" ry="25" fill="#9B7EDE"/>
                                <!-- Left arm (raised) -->
                                <path d="M55 45 Q45 35, 50 25" stroke="#FFD4B2" stroke-width="8" stroke-linecap="round" fill="none"/>
                                <!-- Right arm -->
                                <path d="M85 45 Q95 50, 90 60" stroke="#FFD4B2" stroke-width="8" stroke-linecap="round" fill="none"/>
                                <!-- Pants (brown) -->
                                <path d="M60 75 L60 95 M80 75 L80 95" stroke="#6B4423" stroke-width="16" stroke-linecap="round"/>
                                <!-- Legs -->
                                <path d="M60 95 L55 110 M80 95 L90 108" stroke="#FFD4B2" stroke-width="7" stroke-linecap="round"/>
                                <!-- Face details -->
                                <circle cx="65" cy="23" r="2" fill="#000"/>
                                <circle cx="75" cy="23" r="2" fill="#000"/>
                                <path d="M63 30 Q70 33, 77 30" stroke="#000" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            initChart();
        });

        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });

        function initChart() {
            const ctx = document.getElementById('moodOverTimeChart');
            if (!ctx) return;

            // Destroy existing chart if it exists
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            const moodData = @json($moodOverTime);

            // Group by day of week and calculate average
            const dayAverages = {
                'Mon': [], 'Tue': [], 'Wed': [], 'Thu': [], 'Fri': [], 'Sat': [], 'Sun': []
            };

            moodData.forEach(entry => {
                if (entry.score !== null) {
                    dayAverages[entry.dayName].push(entry.score);
                }
            });

            const chartData = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map(day => {
                const scores = dayAverages[day];
                if (scores.length === 0) return null;
                return scores.reduce((a, b) => a + b, 0) / scores.length;
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        data: chartData,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Mood: ' + (context.parsed.y ? context.parsed.y.toFixed(1) : 'No data');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 10,
                            ticks: {
                                stepSize: 2,
                                display: true,
                                color: '#9CA3AF',
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                display: false
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
    </script>
</div>
