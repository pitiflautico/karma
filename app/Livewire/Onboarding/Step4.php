<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step4 extends Component
{
    public $gender;

    public function mount()
    {
        \Log::info('[Step4] Mounting Step4 component');

        // Load from session if exists
        $this->gender = session('onboarding.gender', '');
    }

    public function selectGender($value)
    {
        $this->gender = $value;
    }

    public function saveAndContinue()
    {
        \Log::info('[Step4] saveAndContinue called', [
            'gender' => $this->gender,
        ]);

        // Validate
        $this->validate([
            'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
        ], [
            'gender.required' => 'Por favor selecciona una opción.',
        ]);

        \Log::info('[Step4] Validation passed, saving to session');

        // Save to session
        session(['onboarding.gender' => $this->gender]);

        \Log::info('[Step4] Redirecting to step5');

        // Redirect to next step (when created)
        return redirect()->route('onboarding.step5');
    }

    public function skip()
    {
        session(['onboarding.gender' => null]);
        return redirect()->route('onboarding.step5');
    }

    public function back()
    {
        return redirect()->route('onboarding.step3');
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
            return view('livewire.onboarding.step4-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step4')->layout('layouts.app');
    }
}
