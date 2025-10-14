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
        \Log::info('[Complete] Starting - marking onboarding as complete');

        // Mark onboarding as complete
        auth()->user()->update(['onboarding_completed_at' => now()]);

        \Log::info('[Complete] Onboarding completed, redirecting to dashboard');

        // Redirect to dashboard
        return redirect()->route('dashboard');
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
            return view('livewire.onboarding.complete-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.complete')->layout('layouts.app');
    }
}
