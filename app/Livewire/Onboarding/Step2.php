<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class Step2 extends Component
{
    public $helpReasons = [];
    public $availableReasons = [
        'Manejar estrés y ansiedad',
        'Mejorar la calidad del sueño',
        'Rastrear mis patrones emocionales',
        'Aumentar mi autoconciencia',
        'Construir mejores hábitos',
        'Entender mis desencadenantes',
        'Mejorar mis relaciones',
        'Apoyo en salud mental',
    ];

    public function mount()
    {
        \Log::info('[Step2] Mounting Step2 component');

        // Load from session if exists
        $this->helpReasons = session('onboarding.helpReasons', []);
    }

    public function toggleReason($reason)
    {
        if (in_array($reason, $this->helpReasons)) {
            $this->helpReasons = array_values(array_diff($this->helpReasons, [$reason]));
        } else {
            $this->helpReasons[] = $reason;
        }
    }

    public function saveAndContinue()
    {
        \Log::info('[Step2] saveAndContinue called', [
            'helpReasons' => $this->helpReasons,
            'count' => count($this->helpReasons),
        ]);

        // Validate
        $this->validate([
            'helpReasons' => 'required|array|min:1',
        ], [
            'helpReasons.required' => 'Por favor selecciona al menos una opción.',
            'helpReasons.min' => 'Por favor selecciona al menos una opción.',
        ]);

        \Log::info('[Step2] Validation passed, saving to session');

        // Save to session
        session(['onboarding.helpReasons' => $this->helpReasons]);

        // Use absolute URL for redirect to ensure WebView compatibility
        $redirectUrl = url('/onboarding/step-3');
        \Log::info('[Step2] Redirecting to step3', [
            'url' => $redirectUrl,
            'currentUrl' => request()->url(),
            'currentPath' => request()->path(),
        ]);

        return redirect($redirectUrl);
    }

    public function back()
    {
        return redirect(url('/onboarding/step-1'));
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
            return view('livewire.onboarding.step2-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding.step2')->layout('layouts.app');
    }
}
