<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step3 extends Component
{
    public $birthDate;

    public function mount()
    {
        \Log::info('[Step3] Mounting Step3 component', [
            'url' => request()->url(),
            'path' => request()->path(),
            'fullUrl' => request()->fullUrl(),
        ]);

        // Load from session if exists
        $this->birthDate = session('onboarding.birthDate', '');
    }

    public function saveAndContinue()
    {
        \Log::info('[Step3] saveAndContinue called', [
            'birthDate' => $this->birthDate,
            'birthDate_length' => strlen($this->birthDate ?? ''),
        ]);

        // Validate
        $this->validate([
            'birthDate' => 'required|date|before:today',
        ], [
            'birthDate.required' => 'Por favor selecciona tu fecha de nacimiento.',
            'birthDate.date' => 'La fecha no es válida.',
            'birthDate.before' => 'La fecha debe ser anterior a hoy.',
        ]);

        \Log::info('[Step3] Validation passed, saving to session');

        // Save to session
        session(['onboarding.birthDate' => $this->birthDate]);

        \Log::info('[Step3] Redirecting to step4', [
            'url' => url('/onboarding/step-4')
        ]);

        // Redirect to next step using absolute URL
        return redirect(url('/onboarding/step-4'));
    }

    public function skip()
    {
        session(['onboarding.birthDate' => null]);
        return redirect(url('/onboarding/step-4'));
    }

    public function back()
    {
        return redirect(url('/onboarding/step-2'));
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
            return view('livewire.onboarding.step3-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step3')->layout('layouts.app');
    }
}
