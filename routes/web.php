<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Livewire\Calendar;
use App\Livewire\CalendarEvents;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\MoodPrompts;
use App\Livewire\Reports;
use App\Livewire\SharingSettings;
use App\Livewire\SharedWithMe;
use App\Livewire\AcceptInvite;
use App\Livewire\AIInsights;
use App\Livewire\Settings;

// Public pages
Route::get('/', Home::class)->name('home');

// Sharing invite acceptance (public route)
Route::get('/accept-invite/{token}', AcceptInvite::class)->name('sharing.accept-invite');

// Google OAuth routes
Route::prefix('auth/google')->group(function () {
    Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('sync-calendar', [GoogleAuthController::class, 'syncCalendar'])->name('auth.google.sync');
    Route::post('disconnect-calendar', [GoogleAuthController::class, 'disconnectCalendar'])->middleware('auth')->name('auth.google.disconnect');
});

// Protected user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/calendar', Calendar::class)->name('calendar');
    Route::get('/calendar-events', CalendarEvents::class)->name('calendar.events');
    Route::get('/calendar-settings', \App\Livewire\CalendarSettings::class)->name('calendar.settings');
    Route::get('/mood-prompts', MoodPrompts::class)->name('mood.prompts');
    Route::get('/mood-history', \App\Livewire\MoodHistory::class)->name('mood.history');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/ai-insights', AIInsights::class)->name('ai.insights');
    Route::get('/sharing-settings', SharingSettings::class)->name('sharing.settings');
    Route::get('/shared-with-me', SharedWithMe::class)->name('shared.with.me');
    Route::get('/settings', Settings::class)->name('settings');
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
});

// Debug routes - REMOVE IN PRODUCTION AFTER TESTING

// Simular WebView de React Native en local
Route::get('/debug/simulate-webview', function () {
    if (!config('app.debug') && request('key') !== 'debug123') {
        abort(404);
    }

    return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Simulador WebView React Native</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .webview { border: 2px solid #333; height: 600px; margin: 20px 0; }
        .console { background: #000; color: #0f0; padding: 10px; font-family: monospace; height: 300px; overflow-y: auto; }
        .console-entry { margin: 5px 0; }
        h1 { color: #333; }
        .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Simulador de WebView React Native</h1>

        <div class="info">
            <strong>Instrucciones:</strong>
            <ol>
                <li>El iframe simula un WebView de React Native</li>
                <li>Haz login con Google en el iframe</li>
                <li>Los mensajes aparecerán en la consola abajo</li>
                <li>También haz logout para probar</li>
            </ol>
        </div>

        <iframe
            id="webview"
            class="webview"
            src="http://localhost:8000"
            style="width: 100%;"
        ></iframe>

        <h2>📱 Consola de Mensajes (simula React Native)</h2>
        <div class="console" id="console">
            <div class="console-entry">Esperando mensajes del WebView...</div>
        </div>
    </div>

    <script>
        // Simular window.ReactNativeWebView.postMessage
        const consoleEl = document.getElementById('console');

        window.addEventListener('message', function(event) {
            try {
                const message = JSON.parse(event.data);
                const timestamp = new Date().toLocaleTimeString();

                let emoji = '📩';
                let color = '#0f0';

                if (message.action === 'loginSuccess') {
                    emoji = '✅';
                    color = '#0f0';
                } else if (message.action === 'logout') {
                    emoji = '👋';
                    color = '#ff0';
                }

                const entry = document.createElement('div');
                entry.className = 'console-entry';
                entry.style.color = color;
                entry.innerHTML = `
                    [${timestamp}] ${emoji} <strong>${message.action}</strong>
                    <br>
                    ${JSON.stringify(message, null, 2).replace(/\n/g, '<br>').replace(/ /g, '&nbsp;')}
                `;

                consoleEl.appendChild(entry);
                consoleEl.scrollTop = consoleEl.scrollHeight;
            } catch (e) {
                // Ignorar mensajes que no son JSON
            }
        });

        // Inyectar window.ReactNativeWebView en el iframe
        const iframe = document.getElementById('webview');
        iframe.onload = function() {
            try {
                iframe.contentWindow.ReactNativeWebView = {
                    postMessage: function(data) {
                        console.log('📤 postMessage llamado:', data);
                        // Enviar el mensaje de vuelta a esta ventana
                        window.postMessage(data, '*');
                    }
                };

                const entry = document.createElement('div');
                entry.className = 'console-entry';
                entry.style.color = '#0ff';
                entry.innerHTML = '[INFO] ReactNativeWebView inyectado en iframe ✓';
                consoleEl.appendChild(entry);
            } catch (e) {
                console.error('Error inyectando ReactNativeWebView:', e);
            }
        };
    </script>
</body>
</html>
HTML;
})->name('debug.simulate-webview');

Route::get('/debug/session-info', function () {
    // Solo accesible si APP_DEBUG=true o con parámetro secreto
    if (!config('app.debug') && request('key') !== 'debug123') {
        abort(404);
    }

    $sessionConfig = [
        'driver' => config('session.driver'),
        'domain' => config('session.domain'),
        'secure' => config('session.secure'),
        'http_only' => config('session.http_only'),
        'same_site' => config('session.same_site'),
        'lifetime' => config('session.lifetime'),
        'cookie_name' => config('session.cookie'),
    ];

    $authInfo = [
        'is_authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'user_email' => auth()->user()?->email ?? 'Not logged in',
    ];

    $envInfo = [
        'app_env' => config('app.env'),
        'app_url' => config('app.url'),
        'app_debug' => config('app.debug'),
    ];

    $googleOAuth = [
        'redirect_uri' => config('services.google.redirect'),
        'client_id_configured' => !empty(config('services.google.client_id')),
    ];

    return response()->json([
        'session_config' => $sessionConfig,
        'auth_info' => $authInfo,
        'env_info' => $envInfo,
        'google_oauth' => $googleOAuth,
        'server_info' => [
            'php_version' => PHP_VERSION,
            'https' => request()->secure() ? 'Yes' : 'No',
            'server_name' => request()->server('SERVER_NAME'),
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.session');

Route::get('/debug/test-session', function () {
    if (!config('app.debug') && request('key') !== 'debug123') {
        abort(404);
    }

    session(['test_time' => now()->toString()]);
    return 'Session value set! <a href="/debug/check-session?key=debug123">Click here to check</a>';
})->name('debug.test-session');

Route::get('/debug/check-session', function () {
    if (!config('app.debug') && request('key') !== 'debug123') {
        abort(404);
    }

    $testValue = session('test_time', 'Session NOT working!');
    $sessionId = session()->getId();

    return response()->json([
        'test_value' => $testValue,
        'session_id' => $sessionId,
        'message' => $testValue === 'Session NOT working!'
            ? 'Session is NOT persisting! Check configuration.'
            : 'Session is working correctly!'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.check-session');

Route::post('/debug/clear-native-token', function () {
    session()->forget('native_app_login');
    session()->forget('native_app_token');
    session()->save();

    return response()->json(['success' => true, 'message' => 'Token cleared']);
})->name('debug.clear-native-token');

// Admin redirect - redirects to login if not authenticated, or to admin dashboard if authenticated
Route::get('/admin', function () {
    return redirect('/admin/login');
})->name('admin');

// Include route files
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
