<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @auth
        <!-- User authentication data for native app -->
        <meta name="user-id" content="{{ auth()->id() }}">
        @if(session('native_app_token'))
        <meta name="user-token" content="{{ session('native_app_token') }}">
        @endif
        @endauth

        <title>{{ config('app.name', 'Karma') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Native App Bridge Script -->
        <script src="{{ asset('js/app/nativeApp.js') }}"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-2xl font-bold text-purple-600">
                                    Karma
                                </a>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center space-x-1">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-100' : '' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('calendar') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('calendar') ? 'bg-gray-100' : '' }}">
                                    Calendar
                                </a>
                                <a href="{{ route('reports') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('reports') ? 'bg-gray-100' : '' }}">
                                    Reports
                                </a>
                                <a href="{{ route('ai.insights') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('ai.insights') ? 'bg-gray-100' : '' }}">
                                    AI Insights
                                </a>
                                <a href="{{ route('sharing.settings') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('sharing.settings') ? 'bg-gray-100' : '' }}">
                                    Sharing
                                </a>
                                <a href="{{ route('shared.with.me') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('shared.with.me') ? 'bg-gray-100' : '' }}">
                                    Shared
                                </a>
                                <a href="{{ route('selfies') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('selfies') ? 'bg-gray-100' : '' }}">
                                    Selfies
                                </a>
                                <a href="{{ route('mood.prompts') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('mood.prompts') ? 'bg-gray-100' : '' }} relative">
                                    Mood Check-ins
                                    @php
                                        $promptCount = \App\Models\MoodPrompt::where('user_id', auth()->id())->where('is_completed', false)->count();
                                    @endphp
                                    @if($promptCount > 0)
                                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-purple-600 rounded-full">{{ $promptCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('settings') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('settings') ? 'bg-gray-100' : '' }}">
                                    <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('auth.google') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Login with Google
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-center space-x-6 text-sm text-gray-500">
                        <a href="{{ route('terms') }}" class="hover:text-gray-900">
                            Terms of Service
                        </a>
                        <span>•</span>
                        <a href="{{ route('privacy') }}" class="hover:text-gray-900">
                            Privacy Policy
                        </a>
                        <span>•</span>
                        <span>© {{ date('Y') }} Feelith</span>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Native App Integration Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Try to get auth data from URL query parameter (for WebView)
                var urlParams = new URLSearchParams(window.location.search);
                var nativeAuthParam = urlParams.get('native_auth');
                var authData = null;
                var authSource = null;

                if (nativeAuthParam) {
                    try {
                        var decoded = atob(nativeAuthParam);
                        authData = JSON.parse(decoded);
                        authSource = 'URL parameter';
                        console.log('[NativeApp] 🟢 Auth data received via URL');

                        // Clean URL (remove the parameter)
                        var cleanUrl = window.location.pathname;
                        window.history.replaceState({}, document.title, cleanUrl);
                    } catch (e) {
                        console.error('[NativeApp] ❌ Error decoding auth data:', e);
                    }
                }

                // Fallback to session data (for web browsers and simulator)
                @if(session('native_app_login') && session('native_app_token'))
                if (!authData) {
                    authData = {
                        user_id: '{{ auth()->id() }}',
                        token: '{{ session('native_app_token') }}',
                    };
                    authSource = 'session';
                    console.log('[NativeApp] 🟢 Auth data received via session');
                }
                @endif

                if (authData && window.NativeAppBridge && window.NativeAppBridge.isRunningInNativeApp()) {
                    // Send login success message to native app
                    window.NativeAppBridge.notifyLoginSuccess(
                        authData.user_id.toString(),
                        authData.token,
                        '{{ config('app.url') }}/api/push/register'
                    );

                    // Clear the token from session after using it
                    if (authSource === 'session') {
                        fetch('{{ route('clear-native-token') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                    }
                }

                // Intercept logout form submission to notify native app
                var logoutForm = document.querySelector('form[action="{{ route('logout') }}"]');
                if (logoutForm && window.NativeAppBridge) {
                    logoutForm.addEventListener('submit', function(e) {
                        if (window.NativeAppBridge.isRunningInNativeApp()) {
                            window.NativeAppBridge.notifyLogout();
                        }
                    });
                }
            });
        </script>

        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>
