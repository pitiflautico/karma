<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Reports & Analytics</h2>
                <p class="mt-2 text-gray-600">Insights into your emotional wellbeing patterns</p>
            </div>
            <button
                wire:click="exportData"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
            >
                <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Data
            </button>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">
                        From Date
                    </label>
                    <input
                        type="date"
                        id="dateFrom"
                        wire:model.live="dateFrom"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                    >
                </div>

                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">
                        To Date
                    </label>
                    <input
                        type="date"
                        id="dateTo"
                        wire:model.live="dateTo"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                    >
                </div>

                <div>
                    <label for="periodType" class="block text-sm font-medium text-gray-700 mb-2">
                        Trend Period
                    </label>
                    <select
                        id="periodType"
                        wire:model.live="periodType"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                    >
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Entries</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summaryStats['total_entries'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Mood</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summaryStats['avg_mood'] ?? 0 }}/10</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $summaryStats['mood_category'] ?? 'N/A' }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Current Streak</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summaryStats['current_streak'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">days</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Longest Streak</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $summaryStats['longest_streak'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">days</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mood Trends Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mood Trends Over Time</h3>
            @if(count($trends) > 0)
                <div class="h-80">
                    <canvas id="trendsChart"></canvas>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>No trend data available for the selected period.</p>
                </div>
            @endif
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Mood Distribution -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mood Distribution</h3>
                @if(count($moodDistribution) > 0)
                    <div class="h-64">
                        <canvas id="distributionChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach($moodDistribution as $dist)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $dist['category'] }}</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $dist['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $dist['count'] }} ({{ $dist['percentage'] }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No mood distribution data available.</p>
                    </div>
                @endif
            </div>

            <!-- Time of Day Analysis -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mood by Time of Day</h3>
                @if(count($timeOfDayStats) > 0)
                    <div class="h-64">
                        <canvas id="timeOfDayChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach($timeOfDayStats as $stat)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $stat['time_of_day'] }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stat['avg_mood'] }}/10 ({{ $stat['count'] }} entries)</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No time of day data available.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Event Type Correlations -->
        @if(count($correlations) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mood by Event Type</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entries</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Mood</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Range</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($correlations as $correlation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $correlation['event_type'])) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $correlation['count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $correlation['avg_mood'] }}/10
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $correlation['min_mood'] }} - {{ $correlation['max_mood'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $categoryColors = [
                                                'Excellent' => 'bg-green-100 text-green-800',
                                                'Good' => 'bg-blue-100 text-blue-800',
                                                'Medium' => 'bg-yellow-100 text-yellow-800',
                                                'Low' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $categoryColors[$correlation['category']] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $correlation['category'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Best and Worst Moods -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Best Moods -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Best Moods
                </h3>
                @if(count($bestAndWorst['best'] ?? []) > 0)
                    <div class="space-y-3">
                        @foreach($bestAndWorst['best'] as $mood)
                            <div class="border-l-4 border-green-500 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $mood['event_title'] }}</p>
                                    <span class="text-sm font-semibold text-green-600">{{ $mood['mood_score'] }}/10</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $mood['date'] }}</p>
                                @if($mood['note'])
                                    <p class="text-xs text-gray-600 mt-1 italic">"{{ Str::limit($mood['note'], 80) }}"</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-gray-500">No data available</p>
                @endif
            </div>

            <!-- Worst Moods -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Areas for Improvement
                </h3>
                @if(count($bestAndWorst['worst'] ?? []) > 0)
                    <div class="space-y-3">
                        @foreach($bestAndWorst['worst'] as $mood)
                            <div class="border-l-4 border-red-500 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $mood['event_title'] }}</p>
                                    <span class="text-sm font-semibold text-red-600">{{ $mood['mood_score'] }}/10</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $mood['date'] }}</p>
                                @if($mood['note'])
                                    <p class="text-xs text-gray-600 mt-1 italic">"{{ Str::limit($mood['note'], 80) }}"</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-gray-500">No data available</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mood Trends Chart
            const trendsData = @json($trends);
            if (trendsData.length > 0) {
                const trendsCtx = document.getElementById('trendsChart');
                if (trendsCtx) {
                    new Chart(trendsCtx, {
                        type: 'line',
                        data: {
                            labels: trendsData.map(item => item.formatted_period),
                            datasets: [{
                                label: 'Average Mood',
                                data: trendsData.map(item => item.avg_mood),
                                borderColor: 'rgb(147, 51, 234)',
                                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 10,
                                    title: {
                                        display: true,
                                        text: 'Mood Score'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            }

            // Mood Distribution Chart
            const distributionData = @json($moodDistribution);
            if (distributionData.length > 0) {
                const distCtx = document.getElementById('distributionChart');
                if (distCtx) {
                    new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: distributionData.map(item => item.category),
                            datasets: [{
                                data: distributionData.map(item => item.count),
                                backgroundColor: [
                                    'rgb(239, 68, 68)',
                                    'rgb(251, 191, 36)',
                                    'rgb(59, 130, 246)',
                                    'rgb(34, 197, 94)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            }

            // Time of Day Chart
            const timeOfDayData = @json($timeOfDayStats);
            if (timeOfDayData.length > 0) {
                const timeCtx = document.getElementById('timeOfDayChart');
                if (timeCtx) {
                    new Chart(timeCtx, {
                        type: 'bar',
                        data: {
                            labels: timeOfDayData.map(item => item.time_of_day),
                            datasets: [{
                                label: 'Average Mood',
                                data: timeOfDayData.map(item => item.avg_mood),
                                backgroundColor: 'rgba(147, 51, 234, 0.8)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 10,
                                    title: {
                                        display: true,
                                        text: 'Mood Score'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</div>
