<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step7 extends Component
{
    public $height = 170; // Default height in cm
    public $unit = 'cm'; // 'cm' or 'inch'

    public function mount()
    {
        \Log::info('[Step7] Mounting Step7 component');

        // Load from session if exists
        $savedHeight = session('onboarding.height');
        $savedUnit = session('onboarding.unit_height', 'cm');

        if ($savedHeight) {
            $this->height = $savedHeight;
            $this->unit = $savedUnit;
        }
    }

    public function saveAndContinue()
    {
        \Log::info('[Step7] saveAndContinue called', [
            'height' => $this->height,
            'unit' => $this->unit,
        ]);

        // Validate
        $this->validate([
            'height' => 'required|numeric|min:50|max:300',
            'unit' => 'required|in:cm,inch',
        ], [
            'height.required' => 'Por favor selecciona tu altura.',
            'height.numeric' => 'La altura debe ser un número.',
            'height.min' => 'La altura mínima es 50.',
            'height.max' => 'La altura máxima es 300.',
        ]);

        \Log::info('[Step7] Validation passed, saving to session');

        // Save to session
        session([
            'onboarding.height' => $this->height,
            'onboarding.unit_height' => $this->unit,
        ]);

        \Log::info('[Step7] Redirecting to complete page');

        // Redirect to complete page
        return redirect(url('/onboarding/complete'));
    }

    public function skip()
    {
        session([
            'onboarding.height' => null,
            'onboarding.unit_height' => 'cm',
        ]);

        return redirect(url('/onboarding/complete'));
    }

    public function back()
    {
        return redirect(url('/onboarding/step-6'));
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
            return view('livewire.onboarding.step7-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step7')->layout('layouts.app');
    }
}
