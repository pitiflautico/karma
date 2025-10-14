<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email = '';

    /**
     * Send password reset link to the provided email
     */
    public function sendResetLink()
    {
        try {
            $this->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.exists' => 'We couldn\'t find an account with this email.',
            ]);

            // Send password reset link
            $status = Password::sendResetLink(
                ['email' => $this->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                // Redirect to password reset sent page
                return $this->redirectRoute('password.sent', ['email' => $this->email]);
            }

            session()->flash('error', 'Unable to send password reset link. Please try again.');
        } catch (ValidationException $e) {
            // Get the first validation error message
            $errors = $e->validator->errors()->all();
            if (!empty($errors)) {
                session()->flash('error', $errors[0]);
            }
        }
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
            return view('livewire.auth.forgot-password')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.auth.forgot-password')
            ->layout('layouts.app');
    }
}
