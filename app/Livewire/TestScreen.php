<?php

namespace App\Livewire;

use Livewire\Component;

class TestScreen extends Component
{
    public $birthDate = '';

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
            return view('livewire.test-mobile')->layout('layouts.app-mobile');
        }

        return view('livewire.test')->layout('layouts.app');
    }
}
