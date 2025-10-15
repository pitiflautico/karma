<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Complete extends Component
{
    public function mount()
    {
        \Log::info('[Complete] Mounting Complete component');
    }

    public function start()
    {
        \Log::info('[Complete] Starting - saving onboarding data and marking as complete');

        // Collect all onboarding data from session
        $onboardingData = [
            'name' => session('onboarding.name'),
            'birth_date' => session('onboarding.birthDate'),
            'gender' => session('onboarding.gender'),
            'weight' => session('onboarding.weight'),
            'weight_unit' => session('onboarding.unit', 'kg'),
            'height' => session('onboarding.height'),
            'height_unit' => session('onboarding.unit_height', 'cm'),
            'help_reason' => session('onboarding.helpReasons'),
            'mood_level' => session('onboarding.moodLevel'),
            'onboarding_completed' => true,
        ];

        // Remove null values
        $onboardingData = array_filter($onboardingData, fn($value) => !is_null($value));

        \Log::info('[Complete] Onboarding data to save', $onboardingData);

        // Update user with all onboarding data
        auth()->user()->update($onboardingData);

        // Clear onboarding session data
        session()->forget([
            'onboarding.name',
            'onboarding.birthDate',
            'onboarding.gender',
            'onboarding.weight',
            'onboarding.unit',
            'onboarding.height',
            'onboarding.unit_height',
            'onboarding.helpReasons',
            'onboarding.moodLevel',
        ]);

        \Log::info('[Complete] Onboarding completed, redirecting to dashboard');

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }

    public function render()
    {
        // Detect if mobile device or native app
        $isNativeApp = request()->header('X-Native-App') === 'true';
        $isMobile = request()->has('mobile');

        if (!$isMobile && !$isNativeApp) {
            $userAgent = request()->header('User-Agent');
            if ($userAgent) {
                $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod'];
                foreach ($mobileKeywords as $keyword) {
                    if (stripos($userAgent, $keyword) !== false) {
                        $isMobile = true;
                        break;
                    }
                }
            }
        }

        if ($isMobile || $isNativeApp) {
            return view('livewire.onboarding.complete-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.complete')->layout('layouts.app');
    }
}
