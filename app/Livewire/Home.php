<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Home extends Component
{
    // View state
    public $showSignUp = false;

    // Login properties
    public $email = '';
    public $password = '';

    // Register properties
    public $name = '';
    public $registerEmail = '';
    public $registerPassword = '';
    public $registerPasswordConfirmation = '';
    public $passwordStrength = 0;

    /**
     * Initialize component and check if user is already logged in
     */
    public function mount()
    {
        \Log::info('[Home] mount() called');
        \Log::info('[Home] Auth::check() = ' . (Auth::check() ? 'true' : 'false'));

        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            \Log::info('[Home] User is logged in, redirecting to dashboard');
            \Log::info('[Home] User ID: ' . Auth::id());
            $this->redirectRoute('dashboard');
        } else {
            \Log::info('[Home] User is NOT logged in, showing login page');
        }
    }

    /**
     * Handle email/password login
     */
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();

            // Notify native app if running in WebView
            session()->flash('native_app_login', true);

            return redirect()->intended('/dashboard');
        }

        session()->flash('error', 'Incorrect email or password!');
    }

    /**
     * Handle user registration
     */
    public function register()
    {
        $this->validate([
            'registerEmail' => 'required|email|unique:users,email',
            'registerPassword' => 'required|min:8|confirmed',
            'registerPasswordConfirmation' => 'required',
        ], [
            'registerEmail.required' => 'The email field is required.',
            'registerEmail.email' => 'Please enter a valid email address.',
            'registerEmail.unique' => 'This email is already registered.',
            'registerPassword.required' => 'The password field is required.',
            'registerPassword.min' => 'Password must be at least 8 characters.',
            'registerPassword.confirmed' => 'Passwords do not match.',
        ]);

        // Create user with a default name (email prefix)
        $user = User::create([
            'name' => explode('@', $this->registerEmail)[0],
            'email' => $this->registerEmail,
            'password' => Hash::make($this->registerPassword),
        ]);

        // Assign user role
        if (!$user->hasAnyRole(['admin', 'user'])) {
            $user->assignRole('user');
        }

        // Log the user in
        Auth::login($user);
        session()->regenerate();

        // Notify native app if running in WebView
        session()->flash('native_app_login', true);

        return redirect()->intended('/dashboard');
    }

    /**
     * Toggle to sign up view
     */
    public function showSignUp()
    {
        $this->showSignUp = true;
    }

    /**
     * Toggle to login view
     */
    public function showLogin()
    {
        $this->showSignUp = false;
    }

    /**
     * Calculate password strength (called when password changes)
     */
    public function updatedRegisterPassword($value)
    {
        $this->passwordStrength = $this->calculatePasswordStrength($value);
    }

    /**
     * Calculate password strength (0-4)
     */
    private function calculatePasswordStrength($password)
    {
        $strength = 0;

        if (strlen($password) >= 8) $strength++;
        if (strlen($password) >= 12) $strength++;
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) $strength++;
        if (preg_match('/[0-9]/', $password)) $strength++;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength++;

        return min($strength, 4);
    }

    /**
     * Detect if the request is from a mobile device or native app
     */
    private function isMobileDevice()
    {
        // Check if there's a session variable indicating mobile/native app
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        // Check for mobile query parameter (can be set by native app on first load)
        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        // Check user agent for mobile devices
        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render()
    {
        // Detect if mobile device and render appropriate view
        if ($this->isMobileDevice()) {
            return view('livewire.home-mobile')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.home')
            ->layout('layouts.app');
    }
}
