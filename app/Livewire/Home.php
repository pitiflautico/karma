<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Home extends Component
{
    // Login properties
    public $email = '';
    public $password = '';

    // Register properties
    public $name = '';
    public $registerEmail = '';
    public $registerPassword = '';

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

        session()->flash('error', 'Invalid credentials');
    }

    /**
     * Handle user registration
     */
    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'registerEmail' => 'required|email|unique:users,email',
            'registerPassword' => 'required|min:6',
        ], [
            'registerEmail.required' => 'The email field is required.',
            'registerEmail.email' => 'Please enter a valid email address.',
            'registerEmail.unique' => 'This email is already registered.',
            'registerPassword.required' => 'The password field is required.',
            'registerPassword.min' => 'Password must be at least 6 characters.',
        ]);

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->registerEmail,
            'password' => Hash::make($this->registerPassword),
        ]);

        // Log the user in
        Auth::login($user);
        session()->regenerate();

        // Notify native app if running in WebView
        session()->flash('native_app_login', true);

        return redirect()->intended('/dashboard');
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
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        // Detect if mobile device and render appropriate view
        if ($this->isMobileDevice()) {
            return view('livewire.home-mobile')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.home')
            ->layout('layouts.app');
    }
}
