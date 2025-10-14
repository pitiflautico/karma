<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Step1 extends Component
{
    public $name;

    public function mount()
    {
        \Log::info('[Step1] Mounting Step1 component');

        // Load from session if exists, or from user
        $this->name = session('onboarding.name', Auth::user()->name ?? '');
    }

    public function saveAndContinue()
    {
        \Log::info('[Step1] saveAndContinue called', [
            'name' => $this->name,
        ]);

        // Validate
        $this->validate([
            'name' => 'required|string|min:2|max:255',
        ]);

        \Log::info('[Step1] Validation passed, saving to session');

        // Save to session
        session(['onboarding.name' => $this->name]);

        \Log::info('[Step1] Redirecting to step2');

        // Redirect to next step using absolute URL
        return redirect(url('/onboarding/step-2'));
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
            return view('livewire.onboarding.step1-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step1')->layout('layouts.app');
    }
}
