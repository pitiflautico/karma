<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step4 extends Component
{
    public $gender;

    public function mount()
    {
        // Load from session if exists
        $this->gender = session('onboarding.gender', '');
    }

    public function selectGender($value)
    {
        $this->gender = $value;
    }

    public function saveAndContinue()
    {
        // Validate
        $this->validate([
            'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
        ], [
            'gender.required' => 'Por favor selecciona una opción.',
        ]);

        // Save to session
        session(['onboarding.gender' => $this->gender]);

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
        // Detect if mobile device
        $userAgent = request()->header('User-Agent');
        $isMobile = false;

        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    $isMobile = true;
                    break;
                }
            }
        }

        if ($isMobile || request()->has('mobile')) {
            return view('livewire.onboarding.step4-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step4')->layout('layouts.app');
    }
}
