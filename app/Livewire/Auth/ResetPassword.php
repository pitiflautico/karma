<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ResetPassword extends Component
{
    public $token;
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    /**
     * Reset the user's password
     */
    public function resetPassword()
    {
        try {
            $this->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8|confirmed',
                'passwordConfirmation' => 'required',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.exists' => 'We couldn\'t find an account with this email.',
                'password.required' => 'The password field is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Passwords do not match.',
            ]);

            // Reset password
            $status = Password::reset(
                [
                    'email' => $this->email,
                    'password' => $this->password,
                    'password_confirmation' => $this->passwordConfirmation,
                    'token' => $this->token,
                ],
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                session()->flash('success', 'Your password has been reset successfully!');
                return $this->redirectRoute('sign-in-mail');
            }

            session()->flash('error', 'This password reset link is invalid or has expired.');
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
            return view('livewire.auth.reset-password')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.auth.reset-password')
            ->layout('layouts.app');
    }
}
