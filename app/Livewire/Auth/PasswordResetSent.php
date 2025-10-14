<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class PasswordResetSent extends Component
{
    public $email;

    public function mount($email = null)
    {
        $this->email = $email;
    }

    /**
     * Detect if the request is from a mobile device or native app
     */
    private function isMobileDevice()
    {
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

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
        if ($this->isMobileDevice()) {
            return view('livewire.auth.password-reset-sent')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.auth.password-reset-sent')
            ->layout('layouts.app');
    }
}
