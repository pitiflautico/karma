<div>
    <!-- Flash Notifications -->
    @if (session()->has('error'))
        <x-flash-notification
            type="error"
            message="ERROR: {{ session('error') }}"
            :autoHide="false"
        />
    @endif

    <div class="fixed inset-0 w-full h-full overflow-hidden">
        <!-- Background Gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-purple-400 to-blue-600"></div>

        <!-- Radial gradient overlay for the glow effect -->
        <div class="absolute inset-0" style="background: radial-gradient(circle at center top, rgba(255, 192, 203, 0.6) 0%, rgba(138, 196, 255, 0.4) 30%, transparent 60%);"></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            @if($currentStep === 0)
                <!-- INTRO SCREEN -->
                <div class="flex-1 flex items-center justify-center px-6">
                    <div class="text-center">
                        <h1 class="text-white text-6xl font-serif mb-8">Feelith</h1>
                        <p class="text-white text-xl mb-12">Welcome! We need to get to know you a bit better.</p>
                        <button
                            wire:click="nextStep"
                            class="bg-white text-purple-600 font-semibold py-4 px-8 rounded-full text-lg shadow-lg hover:bg-gray-100 transition-all">
                            Let's Start
                        </button>
                    </div>
                </div>

            @elseif($currentStep === 8)
                <!-- WELCOME SCREEN -->
                <div class="flex-1 flex items-center justify-center px-6">
                    <div class="text-center">
                        <h1 class="text-white text-5xl font-serif mb-4">Welcome, {{ $name }}!</h1>
                        <p class="text-white text-lg mb-12">You're all set. Let's start your journey to better emotional wellness.</p>
                        <button
                            wire:click="finishOnboarding"
                            class="bg-white text-purple-600 font-semibold py-4 px-8 rounded-full text-lg shadow-lg hover:bg-gray-100 transition-all">
                            Go to Dashboard
                        </button>
                    </div>
                </div>

            @else
                <!-- STEP WIZARD -->
                <div class="flex-1 flex flex-col">
                    <!-- Progress Bar -->
                    <div class="pt-8 px-6">
                        <div class="flex items-center justify-between mb-4">
                            <button wire:click="previousStep" class="text-white text-2xl">←</button>
                            <span class="text-white text-sm font-medium">Step {{ $currentStep }} of 7</span>
                            <div class="w-8"></div>
                        </div>
                        <div class="w-full bg-white/30 rounded-full h-2">
                            <div class="bg-white rounded-full h-2 transition-all duration-300" style="width: {{ ($currentStep / 7) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 flex items-center justify-center px-6 py-8">
                        <div class="w-full max-w-md">

                            @if($currentStep === 1)
                                <!-- STEP 1: NAME -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">What's your name?</h2>
                                    <p class="text-white/80 text-base">We'd love to know what to call you.</p>
                                </div>
                                <input
                                    type="text"
                                    wire:model="name"
                                    placeholder="Enter your name..."
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-white/50 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white">

                            @elseif($currentStep === 2)
                                <!-- STEP 2: HELP REASONS -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">How can we help you?</h2>
                                    <p class="text-white/80 text-base">Select all that apply</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach($availableReasons as $reason)
                                        <button
                                            type="button"
                                            wire:click="toggleReason('{{ $reason }}')"
                                            class="w-full px-6 py-4 rounded-full text-left transition-all
                                                {{ in_array($reason, $helpReasons) ? 'bg-white text-purple-600 font-semibold' : 'bg-white/20 text-white border-2 border-white/50' }}">
                                            {{ $reason }}
                                        </button>
                                    @endforeach
                                </div>

                            @elseif($currentStep === 3)
                                <!-- STEP 3: BIRTH DATE -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">When were you born?</h2>
                                    <p class="text-white/80 text-base">This helps us personalize your experience</p>
                                </div>
                                <input
                                    type="date"
                                    wire:model="birthDate"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-white/50 bg-white/20 text-white focus:outline-none focus:border-white">

                            @elseif($currentStep === 4)
                                <!-- STEP 4: GENDER -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">What's your gender?</h2>
                                    <p class="text-white/80 text-base">This helps us provide better insights</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('gender', '{{ $value }}')"
                                            class="w-full px-6 py-4 rounded-full text-center transition-all
                                                {{ $gender === $value ? 'bg-white text-purple-600 font-semibold' : 'bg-white/20 text-white border-2 border-white/50' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>

                            @elseif($currentStep === 5)
                                <!-- STEP 5: INITIAL MOOD -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">How are you feeling right now?</h2>
                                    <p class="text-white/80 text-base">Rate your mood from 1 (lowest) to 10 (highest)</p>
                                </div>
                                <div class="text-center mb-6">
                                    <span class="text-white text-6xl font-bold">{{ $initialMood }}</span>
                                </div>
                                <input
                                    type="range"
                                    wire:model.live="initialMood"
                                    min="1"
                                    max="10"
                                    class="w-full h-3 bg-white/30 rounded-full appearance-none cursor-pointer">

                            @elseif($currentStep === 6)
                                <!-- STEP 6: WEIGHT (OPTIONAL) -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">What's your weight?</h2>
                                    <p class="text-white/80 text-base">Optional - helps track health patterns (in kg)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="weight"
                                    placeholder="Enter weight in kg..."
                                    step="0.1"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-white/50 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white">

                            @elseif($currentStep === 7)
                                <!-- STEP 7: HEIGHT (OPTIONAL) -->
                                <div class="text-center mb-8">
                                    <h2 class="text-white text-3xl font-semibold mb-4">What's your height?</h2>
                                    <p class="text-white/80 text-base">Optional - helps track health patterns (in cm)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="height"
                                    placeholder="Enter height in cm..."
                                    step="0.1"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-white/50 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white">
                            @endif

                        </div>
                    </div>

                    <!-- Next Button -->
                    <div class="p-6">
                        <button
                            wire:click="{{ $currentStep === 7 ? 'completeOnboarding' : 'nextStep' }}"
                            class="w-full bg-white text-purple-600 font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-gray-100 transition-all">
                            {{ $currentStep === 7 ? 'Complete' : 'Next' }}
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <style>
        /* Custom range slider styling */
        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        input[type="range"]::-moz-range-thumb {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Date input styling for iOS */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</div>
