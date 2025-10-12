<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center py-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">
                        Welcome to Karma
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Track your emotions, understand your patterns, and improve your well-being. 1
                    </p>

                    @guest
                        <a href="{{ route('auth.google') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Get Started with Google
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                            Go to Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Track Your Mood</h3>
                    <p class="text-gray-600">Log your emotional state throughout the day and see patterns emerge</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center">
                    <div class="text-4xl mb-4">📅</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Calendar Integration</h3>
                    <p class="text-gray-600">Connect with Google Calendar to correlate events with your emotions</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center">
                    <div class="text-4xl mb-4">👥</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Share with Groups</h3>
                    <p class="text-gray-600">Join groups and optionally share your emotional journey with others</p>
                </div>
            </div>
        </div>

        <!-- Debug Tools (only in development) -->
        @if(config('app.debug'))
        <div class="mt-12 bg-yellow-50 border-2 border-yellow-400 rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">🔧 Herramientas de Desarrollo</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/debug/test-native-message" class="block p-4 bg-white hover:bg-gray-50 rounded-lg border border-gray-300 transition">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">📱</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Test Native App Messages</h4>
                            <p class="text-sm text-gray-600">Probar envío de mensajes a React Native</p>
                        </div>
                    </div>
                </a>
                <a href="/debug/simulate-webview" class="block p-4 bg-white hover:bg-gray-50 rounded-lg border border-gray-300 transition">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">🔍</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">WebView Simulator</h4>
                            <p class="text-sm text-gray-600">Simular WebView de React Native</p>
                        </div>
                    </div>
                </a>
                <a href="/debug/session-info?key=debug123" class="block p-4 bg-white hover:bg-gray-50 rounded-lg border border-gray-300 transition">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">⚙️</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Session Info</h4>
                            <p class="text-sm text-gray-600">Ver configuración de sesión</p>
                        </div>
                    </div>
                </a>
                <a href="/debug/test-session?key=debug123" class="block p-4 bg-white hover:bg-gray-50 rounded-lg border border-gray-300 transition">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">🧪</span>
                        <div>
                            <h4 class="font-semibold text-gray-900">Test Session</h4>
                            <p class="text-sm text-gray-600">Probar persistencia de sesión</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
