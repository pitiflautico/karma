<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'user_id' => $user->id, // Explicitly include user_id for easy access
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Establish web session using API token (API endpoint)
     * This is used by the mobile app to establish a web session when reopening
     */
    public function establishSession(Request $request)
    {
        // User is already authenticated via API token (auth:api middleware)
        $user = $request->user();

        // Log the user into the web session
        Auth::guard('web')->login($user, true);

        \Log::info('[Auth] Web session established for user', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Web session established successfully',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Establish web session from token parameter and redirect to dashboard
     * This is used by the mobile app on startup
     */
    public function sessionFromToken(Request $request)
    {
        $token = $request->query('token');

        \Log::info('[Auth] sessionFromToken called', [
            'has_token' => !empty($token),
            'token_length' => $token ? strlen($token) : 0
        ]);

        if (!$token) {
            \Log::warning('[Auth] Session from token: No token provided');
            return redirect('/')->with('error', 'Invalid session token');
        }

        try {
            // Create a new request with the token in the Authorization header
            $apiRequest = Request::create('/api/auth/user', 'GET', [], [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'HTTP_ACCEPT' => 'application/json',
            ]);

            \Log::info('[Auth] Created API request with Bearer token');

            // Use the API guard to authenticate the token
            $user = Auth::guard('api')->user();

            \Log::info('[Auth] First attempt with guard(api)->user()', ['user' => $user ? $user->id : null]);

            // If guard doesn't work, try manually authenticating the request
            if (!$user) {
                \Log::info('[Auth] Trying with setRequest');
                // Get the API guard and authenticate the request
                $guard = Auth::guard('api');
                $user = $guard->setRequest($apiRequest)->user();
                \Log::info('[Auth] Second attempt result', ['user' => $user ? $user->id : null]);
            }

            if (!$user) {
                \Log::warning('[Auth] Session from token: Invalid or expired token after all attempts');
                return redirect('/')->with('error', 'Invalid or expired token');
            }

            // Log the user into the web session
            Auth::guard('web')->login($user, true);

            \Log::info('[Auth] ✅ Web session established from token', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Redirect to dashboard
            return redirect('/dashboard');

        } catch (\Exception $e) {
            \Log::error('[Auth] Error establishing session from token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')->with('error', 'Failed to establish session');
        }
    }
}
