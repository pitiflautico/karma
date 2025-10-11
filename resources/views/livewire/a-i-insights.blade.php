<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">AI Insights</h1>
        <p class="text-gray-600">Análisis inteligente de tus patrones emocionales y recomendaciones personalizadas</p>
    </div>

    <!-- Period Selector & Refresh -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <label for="period" class="text-sm font-medium text-gray-700">Periodo:</label>
            <select
                id="period"
                wire:model.live="period"
                class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
            >
                <option value="7_days">Últimos 7 días</option>
                <option value="30_days">Últimos 30 días</option>
                <option value="90_days">Últimos 90 días</option>
            </select>
        </div>

        <button
            wire:click="refreshInsight"
            wire:loading.attr="disabled"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <svg wire:loading.remove wire:target="refreshInsight" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <svg wire:loading wire:target="refreshInsight" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="refreshInsight">Regenerar Insights</span>
            <span wire:loading wire:target="refreshInsight">Generando...</span>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading wire:target="loadInsight,updatedPeriod" class="mb-6">
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <div class="flex items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-purple-700 font-medium">Analizando tus datos emocionales con IA...</p>
            </div>
        </div>
    </div>

    <!-- Error State -->
    @if($error)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">No se pudieron generar insights</h3>
                    <p class="text-sm text-yellow-700 mt-1">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Insights Display -->
    @if($insight && !$error)
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Promedio</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $insight->summary_stats['average_score'] ?? 'N/A' }}/10</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Máximo</p>
                            <p class="text-2xl font-bold text-green-600">{{ $insight->summary_stats['highest_score'] ?? 'N/A' }}/10</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Mínimo</p>
                            <p class="text-2xl font-bold text-red-600">{{ $insight->summary_stats['lowest_score'] ?? 'N/A' }}/10</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Registros</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $insight->summary_stats['total_entries'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Analysis -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-full p-2 mr-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900">Análisis de IA</h2>
                        <p class="text-sm text-gray-500">Generado {{ $insight->generated_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="prose prose-purple max-w-none">
                    <div class="whitespace-pre-line text-gray-700 leading-relaxed">{{ $insight->insights_data['raw_response'] ?? 'No insights available' }}</div>
                </div>
            </div>

            <!-- Day of Week Patterns -->
            @if(!empty($insight->summary_stats['day_of_week_averages']))
                <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Patrones por día de la semana
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        @foreach($insight->summary_stats['day_of_week_averages'] as $day => $avg)
                            @php
                                $percentage = ($avg / 10) * 100;
                                $colorClass = $avg >= 7 ? 'bg-green-500' : ($avg >= 5 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="text-center">
                                <div class="text-xs font-medium text-gray-600 mb-2">{{ $day }}</div>
                                <div class="relative h-24 bg-gray-100 rounded-lg overflow-hidden">
                                    <div class="{{ $colorClass }} absolute bottom-0 w-full transition-all duration-300" style="height: {{ $percentage }}%"></div>
                                </div>
                                <div class="text-sm font-semibold text-gray-900 mt-2">{{ $avg }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Activity Impact -->
            @if(!empty($insight->summary_stats['activity_averages']))
                <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                        </svg>
                        Impacto de actividades en tu ánimo
                    </h3>
                    <div class="space-y-3">
                        @foreach(array_slice($insight->summary_stats['activity_averages'], 0, 10) as $activity => $avg)
                            @php
                                $percentage = ($avg / 10) * 100;
                                $colorClass = $avg >= 7 ? 'bg-green-500' : ($avg >= 5 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $activity }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $avg }}/10</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
