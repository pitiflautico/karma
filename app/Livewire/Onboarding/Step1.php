<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Step1 extends Component
{
    public $name;

    public function mount()
    {
        // Load from session if exists, or from user
        $this->name = session('onboarding.name', Auth::user()->name ?? '');
    }

    public function saveAndContinue()
    {
        // Validate
        $this->validate([
            'name' => 'required|string|min:2|max:255',
        ]);

        // Save to session
        session(['onboarding.name' => $this->name]);

        // Redirect to next step using absolute URL
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
            return view('livewire.onboarding.step1-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step1')->layout('layouts.app');
    }
}
