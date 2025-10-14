<div>
    <!-- Flash Notifications -->
    @if (session()->has('error'))
        <x-flash-notification
            type="error"
            message="ERROR: {{ session('error') }}"
            :autoHide="false"
        />
    @endif

    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- 3-Stage Progress Bar (Always visible) - Respecting Dynamic Island -->
            <div class="pt-16 px-6">
                <div class="flex items-center justify-center gap-2">
                    <!-- Welcome Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 {{ $currentStep === 0 ? 'bg-[#8BC34A] border-[#8BC34A]' : 'bg-white border-gray-300' }} transition-all">
                                <div class="w-3 h-3 rounded-full {{ $currentStep === 0 ? 'bg-white' : 'bg-gray-300' }}"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Welcome</span>
                        </div>
                    </div>

                    <!-- Connector Line -->
                    <div class="w-16 h-0.5 {{ $currentStep >= 1 ? 'bg-[#8BC34A]' : 'bg-gray-300' }} transition-all mb-6"></div>

                    <!-- Info Personal Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 {{ $currentStep >= 1 && $currentStep <= 7 ? 'bg-[#8BC34A] border-[#8BC34A]' : 'bg-white border-gray-300' }} transition-all">
                                <div class="w-3 h-3 rounded-full {{ $currentStep >= 1 && $currentStep <= 7 ? 'bg-white' : 'bg-gray-300' }}"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Info Personal</span>
                        </div>
                    </div>

                    <!-- Connector Line -->
                    <div class="w-16 h-0.5 {{ $currentStep === 8 ? 'bg-[#8BC34A]' : 'bg-gray-300' }} transition-all mb-6"></div>

                    <!-- Empezar Stage -->
                    <div class="flex items-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center border-2 {{ $currentStep === 8 ? 'bg-[#8BC34A] border-[#8BC34A]' : 'bg-white border-gray-300' }} transition-all">
                                <div class="w-3 h-3 rounded-full {{ $currentStep === 8 ? 'bg-white' : 'bg-gray-300' }}"></div>
                            </div>
                            <span class="text-gray-700 text-xs font-medium">Empezar</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($currentStep === 0)
                <!-- INTRO SCREEN -->
                <div class="flex-1 flex items-center justify-center px-6">
                    <div class="text-center max-w-sm">
                        <img src="{{ asset('images/iso_feel.png') }}" alt="Feelith" class="w-40 h-40 mx-auto mb-12">

                        <h1 class="text-gray-900 text-3xl font-semibold mb-4">Vamos a completar tu perfil</h1>

                        <p class="text-gray-600 text-base mb-12 leading-relaxed">
                            Vamos a hacerte unas pequeño cuestionario que nos ayudara a conocerte mejor
                        </p>

                        <button
                            wire:click="nextStep"
                            class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-8 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all flex items-center justify-center gap-2">
                            Estoy List@
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>

                        <button class="mt-6 text-[#7C4DFF] font-medium text-base flex items-center justify-center gap-2 mx-auto hover:underline">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Necesito ayuda
                        </button>
                    </div>
                </div>

            @elseif($currentStep === 8)
                <!-- WELCOME SCREEN -->
                <div class="flex-1 flex items-center justify-center px-6">
                    <div class="text-center max-w-sm">
                        <h1 class="text-gray-900 text-4xl font-semibold mb-4">Welcome, {{ $name }}!</h1>
                        <p class="text-gray-600 text-lg mb-12">You're all set. Let's start your journey to better emotional wellness.</p>
                        <button
                            wire:click="finishOnboarding"
                            class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-8 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                            Go to Dashboard
                        </button>
                    </div>
                </div>

            @else
                <!-- STEP WIZARD -->
                <div class="flex-1 flex flex-col">
                    <!-- 7-Step Progress Bar (Only during Info persona stage) -->
                    <div class="pt-6 px-6">
                        <div class="flex items-center justify-between mb-4">
                            <button wire:click="previousStep" class="text-gray-700 text-2xl">←</button>
                            <span class="text-gray-700 text-sm font-medium">Step {{ $currentStep }} of 7</span>
                            <div class="w-8"></div>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-2">
                            <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ ($currentStep / 7) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 flex items-center justify-center px-6 py-8">
                        <div class="w-full max-w-md">

                            @if($currentStep === 1)
                                <!-- STEP 1: NAME -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">What's your name?</h2>
                                    <p class="text-gray-600 text-base">We'd love to know what to call you.</p>
                                </div>
                                <input
                                    type="text"
                                    wire:model="name"
                                    placeholder="Enter your name..."
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">

                            @elseif($currentStep === 2)
                                <!-- STEP 2: HELP REASONS -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">How can we help you?</h2>
                                    <p class="text-gray-600 text-base">Select all that apply</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach($availableReasons as $reason)
                                        <button
                                            type="button"
                                            wire:click="toggleReason('{{ $reason }}')"
                                            class="w-full px-6 py-4 rounded-full text-left transition-all
                                                {{ in_array($reason, $helpReasons) ? 'bg-[#7C4DFF] text-white font-semibold' : 'bg-white text-gray-700 border-2 border-gray-300' }}">
                                            {{ $reason }}
                                        </button>
                                    @endforeach
                                </div>

                            @elseif($currentStep === 3)
                                <!-- STEP 3: BIRTH DATE -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">When were you born?</h2>
                                    <p class="text-gray-600 text-base">This helps us personalize your experience</p>
                                </div>
                                <input
                                    type="date"
                                    wire:model="birthDate"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 focus:outline-none focus:border-[#7C4DFF]">

                            @elseif($currentStep === 4)
                                <!-- STEP 4: GENDER -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">What's your gender?</h2>
                                    <p class="text-gray-600 text-base">This helps us provide better insights</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('gender', '{{ $value }}')"
                                            class="w-full px-6 py-4 rounded-full text-center transition-all
                                                {{ $gender === $value ? 'bg-[#7C4DFF] text-white font-semibold' : 'bg-white text-gray-700 border-2 border-gray-300' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>

                            @elseif($currentStep === 5)
                                <!-- STEP 5: INITIAL MOOD -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">How are you feeling right now?</h2>
                                    <p class="text-gray-600 text-base">Rate your mood from 1 (lowest) to 10 (highest)</p>
                                </div>
                                <div class="text-center mb-6">
                                    <span class="text-gray-900 text-6xl font-bold">{{ $initialMood }}</span>
                                </div>
                                <input
                                    type="range"
                                    wire:model.live="initialMood"
                                    min="1"
                                    max="10"
                                    class="w-full h-3 bg-gray-300 rounded-full appearance-none cursor-pointer">

                            @elseif($currentStep === 6)
                                <!-- STEP 6: WEIGHT (OPTIONAL) -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">What's your weight?</h2>
                                    <p class="text-gray-600 text-base">Optional - helps track health patterns (in kg)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="weight"
                                    placeholder="Enter weight in kg..."
                                    step="0.1"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">

                            @elseif($currentStep === 7)
                                <!-- STEP 7: HEIGHT (OPTIONAL) -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">What's your height?</h2>
                                    <p class="text-gray-600 text-base">Optional - helps track health patterns (in cm)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="height"
                                    placeholder="Enter height in cm..."
                                    step="0.1"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">
                            @endif

                        </div>
                    </div>

                    <!-- Next Button -->
                    <div class="p-6">
                        <button
                            wire:click="{{ $currentStep === 7 ? 'completeOnboarding' : 'nextStep' }}"
                            class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
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
            background: #7C4DFF;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(124, 77, 255, 0.4);
        }

        input[type="range"]::-moz-range-thumb {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #7C4DFF;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 10px rgba(124, 77, 255, 0.4);
        }

        /* Date input styling for iOS */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }
    </style>
</div>
