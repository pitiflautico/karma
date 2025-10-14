<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step5 extends Component
{
    public $moodLevel = 3; // Default: Normal (1-5 scale)

    public function mount()
    {
        // Load from session if exists
        $this->moodLevel = session('onboarding.moodLevel', 3);
    }

    public function updateMood($level)
    {
        $this->moodLevel = max(1, min(5, $level));
    }

    public function saveAndContinue()
    {
        // Validate
        $this->validate([
            'moodLevel' => 'required|integer|min:1|max:5',
        ], [
            'moodLevel.required' => 'Por favor selecciona tu estado de ánimo.',
            'moodLevel.min' => 'El valor debe estar entre 1 y 5.',
            'moodLevel.max' => 'El valor debe estar entre 1 y 5.',
        ]);

        // Save to session
        session(['onboarding.moodLevel' => $this->moodLevel]);

        // Redirect to next step (when created)
        return redirect()->route('onboarding.step6');
    }

    public function skip()
    {
        session(['onboarding.moodLevel' => null]);
        return redirect()->route('onboarding.step6');
    }

    public function back()
    {
        return redirect()->route('onboarding.step4');
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
            return view('livewire.onboarding.step5-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step5')->layout('layouts.app');
    }
}
