<?php

namespace App\Livewire;

use App\Models\MoodEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Onboarding extends Component
{
    // Wizard state
    public $currentStep = 0; // 0 = intro, 1-7 = steps, 8 = welcome

    // Step 1: Name
    public $name = '';

    // Step 2: Help reasons (multiple choice)
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

    // Step 3: Birth date
    public $birthDate = '';

    // Step 4: Gender
    public $gender = '';

    // Step 5: Initial mood (first mood entry)
    public $initialMood = 5;

    // Step 6: Weight
    public $weight = '';

    // Step 7: Height
    public $height = '';

    /**
     * Initialize component and check if user already completed onboarding
     */
    public function mount()
    {
        // Redirect if user already completed onboarding
        if (Auth::user()->onboarding_completed) {
            return $this->redirectRoute('dashboard');
        }

        // Pre-fill name if available
        $this->name = Auth::user()->name ?? '';
    }

    /**
     * Toggle help reason selection
     */
    public function toggleReason($reason)
    {
        if (in_array($reason, $this->helpReasons)) {
            $this->helpReasons = array_values(array_diff($this->helpReasons, [$reason]));
        } else {
            $this->helpReasons[] = $reason;
        }
    }

    /**
     * Go to next step
     */
    public function nextStep()
    {
        // Validate current step before proceeding
        try {
            $this->validateCurrentStep();
            $this->currentStep++;
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            if (!empty($errors)) {
                session()->flash('error', $errors[0]);
            }
        }
    }

    /**
     * Go to previous step
     */
    public function previousStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    /**
     * Skip current step (for optional steps)
     */
    public function skipStep()
    {
        // Only allow skipping optional steps (6: weight, 7: height)
        if (in_array($this->currentStep, [6, 7])) {
            $this->currentStep++;
        }
    }

    /**
     * Validate current step
     */
    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1: // Name
                $this->validate([
                    'name' => 'required|string|min:2|max:255',
                ], [
                    'name.required' => 'Please enter your name.',
                    'name.min' => 'Name must be at least 2 characters.',
                ]);
                break;

            case 2: // Help reasons
                $this->validate([
                    'helpReasons' => 'required|array|min:1',
                ], [
                    'helpReasons.required' => 'Por favor selecciona al menos una opción.',
                    'helpReasons.min' => 'Por favor selecciona al menos una opción.',
                ]);
                break;

            case 3: // Birth date
                $this->validate([
                    'birthDate' => 'required|date|before:today',
                ], [
                    'birthDate.required' => 'Please enter your birth date.',
                    'birthDate.date' => 'Please enter a valid date.',
                    'birthDate.before' => 'Birth date must be in the past.',
                ]);
                break;

            case 4: // Gender
                $this->validate([
                    'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
                ], [
                    'gender.required' => 'Please select your gender.',
                ]);
                break;

            case 5: // Initial mood
                $this->validate([
                    'initialMood' => 'required|integer|min:1|max:10',
                ], [
                    'initialMood.required' => 'Please select your current mood.',
                    'initialMood.min' => 'Mood must be between 1 and 10.',
                    'initialMood.max' => 'Mood must be between 1 and 10.',
                ]);
                break;

            case 6: // Weight (optional)
                if ($this->weight) {
                    $this->validate([
                        'weight' => 'nullable|numeric|min:20|max:500',
                    ], [
                        'weight.numeric' => 'Please enter a valid weight.',
                        'weight.min' => 'Weight must be at least 20 kg.',
                        'weight.max' => 'Weight must be less than 500 kg.',
                    ]);
                }
                break;

            case 7: // Height (optional)
                if ($this->height) {
                    $this->validate([
                        'height' => 'nullable|numeric|min:50|max:300',
                    ], [
                        'height.numeric' => 'Please enter a valid height.',
                        'height.min' => 'Height must be at least 50 cm.',
                        'height.max' => 'Height must be less than 300 cm.',
                    ]);
                }
                break;
        }
    }

    /**
     * Complete onboarding and save all data
     */
    public function completeOnboarding()
    {
        // Validate all steps one more time
        try {
            $this->validate([
                'name' => 'required|string|min:2|max:255',
                'helpReasons' => 'required|array|min:1',
                'birthDate' => 'required|date|before:today',
                'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
                'initialMood' => 'required|integer|min:1|max:10',
                'weight' => 'nullable|numeric|min:20|max:500',
                'height' => 'nullable|numeric|min:50|max:300',
            ]);

            $user = Auth::user();

            // Update user profile
            $user->update([
                'name' => $this->name,
                'help_reason' => $this->helpReasons,
                'birth_date' => $this->birthDate,
                'gender' => $this->gender,
                'weight' => $this->weight ?: null,
                'height' => $this->height ?: null,
                'onboarding_completed' => true,
            ]);

            // Create first mood entry
            MoodEntry::create([
                'user_id' => $user->id,
                'mood_score' => $this->initialMood,
                'note' => 'Initial mood during onboarding',
                'is_manual' => true,
            ]);

            // Go to welcome screen
            $this->currentStep = 8;

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            if (!empty($errors)) {
                session()->flash('error', $errors[0]);
            }
        }
    }

    /**
     * Finish onboarding and redirect to dashboard
     */
    public function finishOnboarding()
    {
        return $this->redirectRoute('dashboard');
    }

    /**
     * Detect if the request is from a mobile device or native app
     */
    private function isMobileDevice()
    {
        if (session()->has('is_mobile_app') || session()->has('native_app_login')) {
            return true;
        }

        if (request()->has('mobile') && request()->input('mobile') == '1') {
            session()->put('is_mobile_app', true);
            return true;
        }

        $userAgent = request()->header('User-Agent');
        if ($userAgent) {
            $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
            foreach ($mobileKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render()
    {
        if ($this->isMobileDevice()) {
            return view('livewire.onboarding-mobile')
                ->layout('layouts.app-mobile');
        }

        return view('livewire.onboarding')
            ->layout('layouts.app');
    }
}
