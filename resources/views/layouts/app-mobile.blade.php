<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
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

        <style>
            /* Hide scrollbar but keep functionality */
            body::-webkit-scrollbar {
                display: none;
            }
            body {
                -ms-overflow-style: none;
                scrollbar-width: none;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            /* iOS Safe Area Support - Extend background to edges */
            @supports (padding: env(safe-area-inset-top)) {
                body {
                    /* Remove default padding, let individual components handle safe areas */
                    padding: 0;
                }
            }

            /* Full screen support for iOS */
            html, body {
                width: 100%;
                height: 100%;
                position: fixed;
                overflow: hidden;
            }

            main {
                width: 100%;
                height: 100%;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Page Content (No Navigation) -->
        <main>
            {{ $slot }}
        </main>

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
                            console.log('[NativeApp] 🔴 Logout detected, notifying native app...');
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
