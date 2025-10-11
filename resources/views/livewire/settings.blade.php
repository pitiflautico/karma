<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuración</h1>
        <p class="text-gray-600">Gestiona tus preferencias y configuraciones del sistema</p>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Settings Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- AI Insights Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    AI Insights
                </h2>

                <div class="space-y-4">
                    <!-- Enable AI Insights -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Habilitar AI Insights</label>
                            <p class="text-xs text-gray-500">Genera análisis automáticos de tus estados de ánimo</p>
                        </div>
                        <input type="checkbox" wire:model="aiInsightsEnabled" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    </div>

                    <!-- AI Insights Frequency -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia de generación</label>
                        <select wire:model="aiInsightsFrequency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="manual">Manual - Solo cuando lo solicites</option>
                            <option value="daily">Diario - Cada 24 horas</option>
                            <option value="weekly">Semanal - Cada 7 días</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Define con qué frecuencia se generarán nuevos insights automáticamente</p>
                    </div>
                </div>
            </div>

            <!-- Calendar Sync Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Sincronización de Calendario
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia de sincronización (minutos)</label>
                        <select wire:model="calendarSyncFrequency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="15">Cada 15 minutos</option>
                            <option value="30">Cada 30 minutos</option>
                            <option value="60">Cada hora</option>
                            <option value="120">Cada 2 horas</option>
                            <option value="360">Cada 6 horas</option>
                            <option value="720">Cada 12 horas</option>
                            <option value="1440">Una vez al día</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Con qué frecuencia se sincronizarán los eventos de tu calendario de Google</p>
                    </div>
                </div>
            </div>

            <!-- Mood Reminders Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Recordatorios de Ánimo
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Habilitar recordatorios</label>
                            <p class="text-xs text-gray-500">Recibe notificaciones para registrar tu estado de ánimo</p>
                        </div>
                        <input type="checkbox" wire:model="moodRemindersEnabled" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora del recordatorio</label>
                        <input type="time" wire:model="moodReminderTime" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">A qué hora quieres recibir el recordatorio diario</p>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button wire:click="saveSettings" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Guardar Configuración
                </button>
            </div>
        </div>

        <!-- Right Column - Usage Stats -->
        <div class="space-y-6">
            <!-- AI Usage Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Uso de IA (últimos 30 días)</h3>

                <div class="space-y-4">
                    <!-- Total Requests -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total solicitudes</span>
                        <span class="text-lg font-bold text-gray-900">{{ $aiUsageStats['total_requests'] ?? 0 }}</span>
                    </div>

                    <!-- Total Tokens -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total tokens</span>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($aiUsageStats['total_tokens'] ?? 0) }}</span>
                    </div>

                    <!-- Total Cost -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Costo total</span>
                        <span class="text-xl font-bold text-purple-600">${{ number_format($totalCost, 4) }}</span>
                    </div>
                </div>
            </div>

            <!-- Usage by Service -->
            @if(isset($aiUsageStats['by_service']) && count($aiUsageStats['by_service']) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Por servicio</h3>

                    <div class="space-y-3">
                        @foreach($aiUsageStats['by_service'] as $service => $stats)
                            <div class="border-b border-gray-100 pb-3 last:border-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ $service }}</span>
                                    <span class="text-xs text-gray-500">{{ $stats['count'] }} usos</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>{{ number_format($stats['tokens']) }} tokens</span>
                                    <span class="font-semibold text-purple-600">${{ number_format($stats['cost'], 4) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent AI Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Solicitudes recientes</h3>

                @if($recentAiLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentAiLogs as $log)
                            <div class="text-xs border-b border-gray-100 pb-2 last:border-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium text-gray-700 capitalize">{{ $log->service }}</span>
                                    <span class="text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-gray-600">
                                    <span>{{ number_format($log->total_tokens) }} tokens</span>
                                    <span class="font-semibold text-purple-600">${{ number_format($log->estimated_cost, 4) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hay solicitudes recientes</p>
                @endif
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-xs text-blue-800">
                        <p class="font-medium mb-1">Sobre los costos</p>
                        <p>Los costos son estimaciones basadas en el uso de tokens. El precio actual del modelo Llama-3.3-70B es de $0.88 por millón de tokens.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
