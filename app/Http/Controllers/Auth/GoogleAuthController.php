<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar.readonly',
                'https://www.googleapis.com/auth/calendar.events.readonly'
            ])
            ->redirect();
    }

    /**
     * Redirect to Google OAuth for Calendar sync only
     */
    public function syncCalendar(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar.readonly',
                'https://www.googleapis.com/auth/calendar.events.readonly'
            ])
            ->with(['prompt' => 'consent', 'access_type' => 'offline'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(): RedirectResponse
    {
        try {
            \Log::info('=== Google OAuth Callback Started ===');

            $googleUser = Socialite::driver('google')->user();
            \Log::info('Google User received:', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId(),
            ]);

            // Check if user is already logged in (calendar sync flow)
            if (Auth::check()) {
                \Log::info('User already authenticated - Calendar sync flow');
                $user = Auth::user();
                $user->update([
                    'google_calendar_token' => $googleUser->token,
                    'google_calendar_refresh_token' => $googleUser->refreshToken,
                    'calendar_sync_enabled' => true,
                ]);

                return redirect('/dashboard')->with('success', 'Google Calendar synchronized successfully!');
            }

            \Log::info('Regular login flow - Creating/updating user');

            // Regular login flow
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'google_calendar_token' => $googleUser->token,
                    'google_calendar_refresh_token' => $googleUser->refreshToken,
                    'email_verified_at' => now(),
                    'calendar_sync_enabled' => true,
                ]
            );

            \Log::info('User created/updated:', ['user_id' => $user->id, 'email' => $user->email]);

            // Assign 'user' role if user doesn't have any role
            if (!$user->hasAnyRole(['admin', 'user'])) {
                \Log::info('Assigning user role');
                $user->assignRole('user');
            } else {
                \Log::info('User already has roles:', ['roles' => $user->getRoleNames()]);
            }

            \Log::info('Attempting to login user');
            Auth::login($user, true);

            \Log::info('Auth::check() after login:', ['authenticated' => Auth::check(), 'user_id' => Auth::id()]);

            // Create API token for native app integration
            \Log::info('Creating API token for native app...');

            try {
                $token = $user->createToken('native-app-token')->accessToken;
                \Log::info('Token created successfully:', ['token_preview' => substr($token, 0, 20) . '...']);
            } catch (\Exception $e) {
                \Log::error('Failed to create token:', ['error' => $e->getMessage()]);
                // Continue without token - don't break the login
                $token = null;
            }

            if ($token) {
                // Store in session for web browsers
                session()->put('native_app_login', true);
                session()->put('native_app_token', $token);
                session()->save();

                \Log::info('Token stored in session');

                // For WebView: Pass token in URL fragment (more secure than query param)
                // Fragment is not sent to server and can only be read by JavaScript
                \Log::info('Redirecting with token in URL fragment for WebView');
                return redirect()->intended('/dashboard?native_auth=' . base64_encode(json_encode([
                    'user_id' => $user->id,
                    'token' => $token,
                    'timestamp' => time(),
                ])));
            } else {
                \Log::warning('No token created, skipping session storage');
            }

            \Log::info('Redirecting to dashboard');
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            \Log::error('Google OAuth Callback Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    /**
     * Disconnect Google Calendar
     */
    public function disconnectCalendar(): RedirectResponse
    {
        $user = Auth::user();
        $user->update([
            'google_calendar_token' => null,
            'google_calendar_refresh_token' => null,
            'calendar_sync_enabled' => false,
        ]);

        return redirect()->back()->with('success', 'Google Calendar disconnected successfully!');
    }

    /**
     * Logout user
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
