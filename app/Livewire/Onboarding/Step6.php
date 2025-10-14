<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step6 extends Component
{
    public $weight = 70; // Default weight in kg
    public $unit = 'kg'; // 'kg' or 'lbs'

    public function mount()
    {
        \Log::info('[Step6] Mounting Step6 component');

        // Load from session if exists
        $savedWeight = session('onboarding.weight');
        $savedUnit = session('onboarding.unit', 'kg');

        if ($savedWeight) {
            $this->weight = $savedWeight;
            $this->unit = $savedUnit;
        }
    }

    public function saveAndContinue()
    {
        \Log::info('[Step6] saveAndContinue called', [
            'weight' => $this->weight,
            'unit' => $this->unit,
        ]);

        // Validate
        $this->validate([
            'weight' => 'required|numeric|min:20|max:500',
            'unit' => 'required|in:kg,lbs',
        ], [
            'weight.required' => 'Por favor selecciona tu peso.',
            'weight.numeric' => 'El peso debe ser un número.',
            'weight.min' => 'El peso mínimo es 20.',
            'weight.max' => 'El peso máximo es 500.',
        ]);

        \Log::info('[Step6] Validation passed, saving to session');

        // Save to session
        session([
            'onboarding.weight' => $this->weight,
            'onboarding.unit' => $this->unit,
        ]);

        \Log::info('[Step6] Redirecting to step7');

        // Redirect to next step
        return redirect(url('/onboarding/step-7'));
    }

    public function skip()
    {
        session([
            'onboarding.weight' => null,
            'onboarding.unit' => 'kg',
        ]);
        return redirect(url('/onboarding/step-7'));
    }

    public function back()
    {
        return redirect(url('/onboarding/step-5'));
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
            return view('livewire.onboarding.step6-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step6')->layout('layouts.app');
    }
}
