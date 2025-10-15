<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuthHome extends Component
{
    /**
     * Initialize component and check if user is already logged in
     */
    public function mount()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            $this->redirectRoute('dashboard');
        }
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
            return view('livewire.auth.auth-home')
                ->layout('layouts.app-mobile');
        }

        // For desktop, render the same view (it will show desktop layout)
        return view('livewire.auth.auth-home')
            ->layout('layouts.app');
    }
}
