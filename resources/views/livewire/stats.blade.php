<div class="min-h-screen bg-[#F7F3EF] pb-24">
    <!-- Header -->
    <div class="bg-[#F7F3EF] pt-8 pb-4 px-6 text-center">
        <div class="inline-block bg-green-50 rounded-full p-2 mb-3">
            <span class="text-2xl">😊</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-1">Mood Insight</h1>
        <p class="text-xs text-gray-600 max-w-xs mx-auto leading-relaxed">Track and analyze your key health indicators to optimize your wellness journey.</p>
    </div>

    <!-- Month Selector -->
    <div class="px-4 py-3">
        <div class="flex items-center justify-center bg-white rounded-full py-2 px-4 shadow-sm">
            <button wire:click="previousMonth" class="text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="flex items-center gap-1.5 mx-4">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">{{ $monthName }}</span>
            </div>
            <button wire:click="nextMonth" class="text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    @if($totalEntries === 0)
        <!-- No Entries Message -->
        <div class="px-4 py-6 text-center">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="text-4xl mb-3">📊</div>
                <h3 class="text-base font-semibold text-gray-900 mb-1">No Entries for This Month</h3>
                <p class="text-xs text-gray-600">Start tracking your mood to see insights here.</p>
            </div>
        </div>
    @else
        <!-- Most Logged Mood -->
        <div class="px-4 py-2">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-1.5 mb-3">
                    <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99935 0.833496C3.94926 0.833496 0.666016 4.11674 0.666016 8.16683C0.666016 10.8825 2.1422 13.2518 4.33268 14.5189V16.4168H11.666V14.5189C13.8565 13.2518 15.3327 10.8825 15.3327 8.16683C15.3327 4.11674 12.0494 0.833496 7.99935 0.833496ZM2.49935 8.16683C2.49935 5.12926 4.96178 2.66683 7.99935 2.66683C11.0369 2.66683 13.4993 5.12926 13.4993 8.16683C13.4993 10.3595 12.2163 12.2543 10.356 13.138L9.83268 13.3866V14.5835H6.16602V13.3866L5.64268 13.138C3.78238 12.2543 2.49935 10.3595 2.49935 8.16683Z" fill="#A8A29E"/>
                        <path d="M4.33268 17.3335V19.1668H11.666V17.3335H4.33268Z" fill="#A8A29E"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-900">Most Logged Mood</h2>
                </div>

                <!-- Mood Bars -->
                <div class="space-y-2">
                    @foreach($mostLoggedMoods as $mood)
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center w-8 h-8 flex-shrink-0">
                                @if($mood['icon'] === 'depressed')
                                    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="40" cy="40" r="40" fill="#C084FC"/>
                                        <path d="M22.3052 29.4024C23.7401 28.574 25.5748 29.0656 26.4033 30.5005C26.6666 30.9566 27.0453 31.3353 27.5013 31.5986C27.9574 31.8619 28.4747 32.0005 29.0013 32.0005C29.528 32.0005 30.0453 31.8619 30.5013 31.5986C30.9574 31.3353 31.3361 30.9566 31.5994 30.5005C32.4278 29.0656 34.2626 28.574 35.6975 29.4024C37.1324 30.2309 37.624 32.0656 36.7956 33.5005C36.0057 34.8687 34.8695 36.0048 33.5013 36.7947C32.1332 37.5846 30.5812 38.0005 29.0013 38.0005C27.4215 38.0005 25.8695 37.5846 24.5013 36.7947C23.1332 36.0048 21.997 34.8687 21.2071 33.5005C20.3787 32.0656 20.8703 30.2309 22.3052 29.4024Z" fill="#6B21A8"/>
                                        <path d="M44.3052 29.4024C45.7401 28.574 47.5748 29.0656 48.4033 30.5005C48.6666 30.9566 49.0453 31.3353 49.5013 31.5986C49.9574 31.8619 50.4747 32.0005 51.0013 32.0005C51.528 32.0005 52.0453 31.8619 52.5013 31.5986C52.9574 31.3353 53.3361 30.9566 53.5994 30.5005C54.4278 29.0656 56.2626 28.574 57.6975 29.4024C59.1324 30.2309 59.624 32.0656 58.7956 33.5005C58.0057 34.8687 56.8695 36.0048 55.5013 36.7947C54.1332 37.5846 52.5812 38.0005 51.0013 38.0005C49.4215 38.0005 47.8695 37.5846 46.5013 36.7947C45.1332 36.0048 43.997 34.8687 43.2071 33.5005C42.3787 32.0656 42.8703 30.2309 44.3052 29.4024Z" fill="#6B21A8"/>
                                        <path d="M40.0016 42.0005C37.3751 42.0005 34.7744 42.5178 32.3479 43.5229C29.9214 44.528 27.7166 46.0012 25.8594 47.8584C24.7154 49.0024 24.3732 50.7228 24.9923 52.2175C25.6115 53.7122 27.07 54.6868 28.6879 54.6868L51.3153 54.6868C52.9331 54.6868 54.3917 53.7122 55.0108 52.2175C55.6299 50.7228 55.2877 49.0024 54.1437 47.8584C52.2865 46.0012 50.0818 44.528 47.6552 43.5229C45.2287 42.5178 42.628 42.0005 40.0016 42.0005Z" fill="#6B21A8"/>
                                    </svg>
                                @elseif($mood['icon'] === 'sad')
                                    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="40" cy="40" r="40" fill="#FB923C"/>
                                        <circle cx="29" cy="33" r="5" fill="#9A3412"/>
                                        <circle cx="51" cy="33" r="5" fill="#9A3412"/>
                                        <path d="M32.3463 43.5224C34.7728 42.5173 37.3736 42 40 42C42.6264 42 45.2272 42.5173 47.6537 43.5224C50.0802 44.5275 52.285 46.0007 54.1421 47.8579C55.7042 49.42 55.7042 51.9526 54.1421 53.5147C52.58 55.0768 50.0474 55.0768 48.4853 53.5147C47.371 52.4004 46.0481 51.5165 44.5922 50.9134C43.1363 50.3104 41.5759 50 40 50C38.4241 50 36.8637 50.3104 35.4078 50.9135C33.9519 51.5165 32.629 52.4004 31.5147 53.5147C29.9526 55.0768 27.42 55.0768 25.8579 53.5147C24.2958 51.9526 24.2958 49.42 25.8579 47.8579C27.715 46.0007 29.9198 44.5275 32.3463 43.5224Z" fill="#9A3412"/>
                                    </svg>
                                @elseif($mood['icon'] === 'neutral')
                                    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="40" cy="40" r="40" fill="#B1865E"/>
                                        <circle cx="29" cy="33" r="5" fill="#533630"/>
                                        <circle cx="51" cy="33" r="5" fill="#533630"/>
                                        <path d="M40 56C37.3736 56 34.7728 55.4827 32.3463 54.4776C29.9198 53.4725 27.715 51.9993 25.8579 50.1421C24.2958 48.58 24.2958 46.0474 25.8579 44.4853C27.42 42.9232 29.9526 42.9232 31.5147 44.4853C32.629 45.5996 33.9519 46.4835 35.4078 47.0866C36.8637 47.6896 38.4241 48 40 48C41.5759 48 43.1363 47.6896 44.5922 47.0866C46.0481 46.4835 47.371 45.5996 48.4853 44.4853C50.0474 42.9232 52.58 42.9232 54.1421 44.4853C55.7042 46.0474 55.7042 48.58 54.1421 50.1421C52.285 51.9993 50.0802 53.4725 47.6537 54.4776C45.2272 55.4827 42.6264 56 40 56Z" fill="#533630"/>
                                    </svg>
                                @elseif($mood['icon'] === 'happy')
                                    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="40" cy="40" r="40" fill="#FBBF24"/>
                                        <circle cx="29" cy="33" r="5" fill="#92400E"/>
                                        <circle cx="51" cy="33" r="5" fill="#92400E"/>
                                        <path d="M40 56C37.3736 56 34.7728 55.4827 32.3463 54.4776C29.9198 53.4725 27.715 51.9993 25.8579 50.1421C24.2958 48.58 24.2958 46.0474 25.8579 44.4853C27.42 42.9232 29.9526 42.9232 31.5147 44.4853C32.629 45.5996 33.9519 46.4835 35.4078 47.0866C36.8637 47.6896 38.4241 48 40 48C41.5759 48 43.1363 47.6896 44.5922 47.0866C46.0481 46.4835 47.371 45.5996 48.4853 44.4853C50.0474 42.9232 52.58 42.9232 54.1421 44.4853C55.7042 46.0474 55.7042 48.58 54.1421 50.1421C52.285 51.9993 50.0802 53.4725 47.6537 54.4776C45.2272 55.4827 42.6264 56 40 56Z" fill="#92400E"/>
                                    </svg>
                                @else
                                    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="40" cy="40" r="40" fill="#9BB167"/>
                                        <path d="M22.3052 37.5981C23.7401 38.4265 25.5748 37.9349 26.4033 36.5C26.6666 36.0439 27.0453 35.6652 27.5013 35.4019C27.9574 35.1386 28.4747 35 29.0013 35C29.528 35 30.0453 35.1386 30.5013 35.4019C30.9574 35.6652 31.3361 36.0439 31.5994 36.5C32.4278 37.9349 34.2626 38.4265 35.6975 37.5981C37.1324 36.7696 37.624 34.9349 36.7956 33.5C36.0057 32.1318 34.8695 30.9957 33.5013 30.2058C32.1332 29.4159 30.5812 29 29.0013 29C27.4215 29 25.8695 29.4159 24.5013 30.2058C23.1332 30.9957 21.997 32.1318 21.2071 33.5C20.3787 34.9349 20.8703 36.7696 22.3052 37.5981Z" fill="#3F4B29"/>
                                        <path d="M44.3052 37.5981C45.7401 38.4265 47.5748 37.9349 48.4033 36.5C48.6666 36.0439 49.0453 35.6652 49.5013 35.4019C49.9574 35.1386 50.4747 35 51.0013 35C51.528 35 52.0453 35.1386 52.5013 35.4019C52.9574 35.6652 53.3361 36.0439 53.5994 36.5C54.4278 37.9349 56.2626 38.4265 57.6975 37.5981C59.1324 36.7696 59.624 34.9349 58.7956 33.5C58.0057 32.1318 56.8695 30.9957 55.5013 30.2058C54.1332 29.4159 52.5812 29 51.0013 29C49.4215 29 47.8695 29.4159 46.5013 30.2058C45.1332 30.9957 43.997 32.1318 43.2071 33.5C42.3787 34.9349 42.8703 36.7696 44.3052 37.5981Z" fill="#3F4B29"/>
                                        <path d="M40.0016 56C37.3751 56 34.7744 55.4827 32.3479 54.4776C29.9214 53.4725 27.7166 51.9993 25.8594 50.1421C24.7154 48.9981 24.3732 47.2777 24.9923 45.783C25.6115 44.2883 27.07 43.3137 28.6879 43.3137L51.3153 43.3137C52.9331 43.3137 54.3917 44.2883 55.0108 45.783C55.6299 47.2777 55.2877 48.9981 54.1437 50.1421C52.2865 51.9993 50.0818 53.4725 47.6552 54.4776C45.2287 55.4827 42.628 56 40.0016 56Z" fill="#3F4B29"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-xs text-gray-700 font-medium">{{ $mood['score'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $mood['count'] }}</span>
                                </div>
                                <div class="h-6 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $topMood ? ($mood['count'] / $topMood['count']) * 100 : 0 }}%;
                                                background: {{ $mood['color'] }};">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($topMood)
                    <p class="text-xs text-gray-600 mt-3 leading-relaxed">
                        Your most logged mood is <span class="font-semibold">overjoyed</span> with {{ $topMood['count'] }} entries this month.
                    </p>
                @endif
            </div>
        </div>

        <!-- Mood Over Time -->
        <div class="px-4 py-2">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-1.5 mb-3">
                    <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.25065 2.64007V0.75H5.41732V3.11639C2.72981 4.20427 0.833984 6.83907 0.833984 9.91667C0.833984 13.9668 4.11723 17.25 8.16732 17.25H11.834C15.8841 17.25 19.1673 13.9668 19.1673 9.91667C19.1673 6.83907 17.2715 4.20427 14.584 3.11639V0.75H12.7507V2.64007C12.4504 2.60262 12.1444 2.58333 11.834 2.58333H8.16732C7.85688 2.58333 7.55095 2.60262 7.25065 2.64007ZM15.9335 6.25C14.9264 5.12479 13.4629 4.41667 11.834 4.41667H8.16732C6.5384 4.41667 5.07488 5.12479 4.06779 6.25H15.9335ZM2.98028 8.08333H17.021C17.2237 8.65676 17.334 9.27384 17.334 9.91667C17.334 12.9542 14.8715 15.4167 11.834 15.4167H8.16732C5.12975 15.4167 2.66732 12.9542 2.66732 9.91667C2.66732 9.27384 2.7776 8.65676 2.98028 8.08333Z" fill="#A8A29E"/>
                    </svg>
                    <h2 class="text-sm font-bold text-gray-900">Mood Over Time</h2>
                </div>

                <!-- Chart -->
                <div class="relative h-32">
                    <canvas id="moodOverTimeChart"></canvas>
                </div>

                <!-- Day Labels -->
                <div class="flex justify-between mt-1 text-xs text-gray-500">
                    @php
                        $daysToShow = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    @endphp
                    @foreach($daysToShow as $day)
                        <span>{{ $day }}</span>
                    @endforeach
                </div>

                <p class="text-xs text-gray-600 mt-3 leading-relaxed">
                    Your mood is improving over time, congratulations!
                </p>
            </div>
        </div>

        <!-- Most Logged Trigger -->
        @if($mostLoggedTags->count() > 0)
            <div class="px-4 py-2">
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-1.5 mb-3">
                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.08398 6.3335C9.08398 3.29593 11.5464 0.833496 14.584 0.833496C17.6216 0.833496 20.084 3.29593 20.084 6.3335C20.084 9.37106 17.6216 11.8335 14.584 11.8335C11.5464 11.8335 9.08398 9.37106 9.08398 6.3335ZM14.584 2.66683C12.5589 2.66683 10.9173 4.30845 10.9173 6.3335C10.9173 8.35854 12.5589 10.0002 14.584 10.0002C16.609 10.0002 18.2507 8.35854 18.2507 6.3335C18.2507 4.30845 16.609 2.66683 14.584 2.66683Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.833984 11.8335C0.833984 9.30219 2.88601 7.25016 5.41732 7.25016C7.94862 7.25016 10.0007 9.30219 10.0007 11.8335C10.0007 14.3648 7.94862 16.4168 5.41732 16.4168C2.88601 16.4168 0.833984 14.3648 0.833984 11.8335ZM5.41732 9.0835C3.89853 9.0835 2.66732 10.3147 2.66732 11.8335C2.66732 13.3523 3.89853 14.5835 5.41732 14.5835C6.9361 14.5835 8.16732 13.3523 8.16732 11.8335C8.16732 10.3147 6.9361 9.0835 5.41732 9.0835Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.209 12.7502C11.4371 12.7502 10.0007 14.1866 10.0007 15.9585C10.0007 17.7304 11.4371 19.1668 13.209 19.1668C14.9809 19.1668 16.4173 17.7304 16.4173 15.9585C16.4173 14.1866 14.9809 12.7502 13.209 12.7502ZM11.834 15.9585C11.834 15.1991 12.4496 14.5835 13.209 14.5835C13.9684 14.5835 14.584 15.1991 14.584 15.9585C14.584 16.7179 13.9684 17.3335 13.209 17.3335C12.4496 17.3335 11.834 16.7179 11.834 15.9585Z" fill="#A8A29E"/>
                        </svg>
                        <h2 class="text-sm font-bold text-gray-900">Most logged trigger</h2>
                    </div>

                    <!-- Tags as Pills -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($mostLoggedTags as $index => $tag)
                            @php
                                $colors = ['bg-purple-100 text-purple-600', 'bg-blue-100 text-blue-600', 'bg-green-100 text-green-600', 'bg-yellow-100 text-yellow-600', 'bg-pink-100 text-pink-600'];
                                $color = $colors[$index % count($colors)];
                                $sizes = ['text-2xl', 'text-xl', 'text-lg', 'text-base', 'text-sm'];
                                $textSize = $sizes[$index % count($sizes)];
                                $paddingSize = ['px-6 py-3', 'px-5 py-2.5', 'px-4 py-2', 'px-3 py-1.5', 'px-3 py-1.5'];
                                $padding = $paddingSize[$index % count($paddingSize)];
                            @endphp
                            <span class="{{ $color }} {{ $textSize }} {{ $padding }} rounded-full font-medium">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>

                    @if($topTag)
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Your most logged tag is "{{ $topTag->name }}" with around {{ $topTag->count }} logs this month
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Most Happiest Days -->
        @if($happiestDay)
            <div class="px-4 py-2">
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-1.5 mb-3">
                        <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.75 3.87508C0.75 5.647 2.18642 7.08341 3.95833 7.08341H14.0417C15.8136 7.08341 17.25 5.647 17.25 3.87508C17.25 2.10317 15.8136 0.666748 14.0417 0.666748H3.95833C2.18642 0.666748 0.75 2.10317 0.75 3.87508ZM3.95833 5.25008C3.19894 5.25008 2.58333 4.63447 2.58333 3.87508C2.58333 3.11569 3.19894 2.50008 3.95833 2.50008L14.0417 2.50008C14.8011 2.50008 15.4167 3.11569 15.4167 3.87508C15.4167 4.63447 14.8011 5.25008 14.0417 5.25008H3.95833Z" fill="#A8A29E"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.75 12.1251C0.75 13.897 2.18642 15.3334 3.95833 15.3334H14.0417C15.8136 15.3334 17.25 13.897 17.25 12.1251C17.25 10.3532 15.8136 8.91675 14.0417 8.91675H3.95833C2.18642 8.91675 0.75 10.3532 0.75 12.1251ZM3.95833 13.5001C3.19894 13.5001 2.58333 12.8845 2.58333 12.1251C2.58333 11.3657 3.19894 10.7501 3.95833 10.7501H14.0417C14.8011 10.7501 15.4167 11.3657 15.4167 12.1251C15.4167 12.8845 14.8011 13.5001 14.0417 13.5001H3.95833Z" fill="#A8A29E"/>
                        </svg>
                        <h2 class="text-sm font-bold text-gray-900">Most Happiest Days</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <img src="/images/happy-person.svg" alt="Happy" class="w-16 h-16" onerror="this.style.display='none'">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-0.5">{{ $happiestDay['dayName'] }}</h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                {{ $happiestDay['dayName'] }} is your most happiest days based on our data.
                            </p>
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
