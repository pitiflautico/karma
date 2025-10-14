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
                <!-- Skip button (only on steps 1-7) -->
                @if($currentStep >= 1 && $currentStep <= 7)
                    <div class="absolute top-16 right-6 z-20">
                        <button wire:click="skipStep" class="text-gray-600 text-base font-medium hover:text-gray-800">
                            Skip
                        </button>
                    </div>
                @endif

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
                        <h1 class="text-gray-900 text-4xl font-semibold mb-4">¡Bienvenido, {{ $name }}!</h1>
                        <p class="text-gray-600 text-lg mb-12">Ya está todo listo. Comencemos tu viaje hacia un mejor bienestar emocional.</p>
                        <button
                            wire:click="finishOnboarding"
                            class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-8 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                            Ir al Dashboard
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
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cómo te llamas?</h2>
                                    <p class="text-gray-600 text-base">Nos encantaría saber cómo llamarte.</p>
                                </div>
                                <input
                                    type="text"
                                    wire:model="name"
                                    placeholder="Escribe tu nombre..."
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">

                            @elseif($currentStep === 2)
                                <!-- STEP 2: HELP REASONS -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿En qué podemos ayudarte?</h2>
                                    <p class="text-gray-600 text-base">Selecciona todas las que apliquen</p>
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
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-12">¿Cuándo naciste?</h2>
                                </div>

                                <!-- iOS-style Date Picker -->
                                <div class="relative mb-8" x-data="datePicker()">
                                    <div class="relative flex justify-center gap-4 mb-8">
                                        <!-- Selection indicator (green border) - positioned behind -->
                                        <div class="absolute left-1/2 top-20 -translate-x-1/2 w-72 h-12 border-2 border-[#8BC34A] rounded-full pointer-events-none z-10"></div>

                                        <!-- Month Picker -->
                                        <div class="flex-1 max-w-[100px] h-48 overflow-y-scroll snap-y snap-mandatory scrollbar-hide relative z-20"
                                             @scroll="updateMonth($event)"
                                             x-ref="monthScroll">
                                            <div class="h-20"></div>
                                            <template x-for="(month, index) in months" :key="index">
                                                <div class="h-12 flex items-center justify-center snap-center text-lg"
                                                     :class="selectedMonth === index ? 'text-[#8BC34A] font-semibold' : 'text-gray-400'"
                                                     x-text="month"></div>
                                            </template>
                                            <div class="h-20"></div>
                                        </div>

                                        <!-- Day Picker -->
                                        <div class="flex-1 max-w-[80px] h-48 overflow-y-scroll snap-y snap-mandatory scrollbar-hide relative z-20"
                                             @scroll="updateDay($event)"
                                             x-ref="dayScroll">
                                            <div class="h-20"></div>
                                            <template x-for="day in days" :key="day">
                                                <div class="h-12 flex items-center justify-center snap-center text-lg"
                                                     :class="selectedDay === day ? 'text-[#8BC34A] font-semibold' : 'text-gray-400'"
                                                     x-text="String(day).padStart(2, '0')"></div>
                                            </template>
                                            <div class="h-20"></div>
                                        </div>

                                        <!-- Year Picker -->
                                        <div class="flex-1 max-w-[100px] h-48 overflow-y-scroll snap-y snap-mandatory scrollbar-hide relative z-20"
                                             @scroll="updateYear($event)"
                                             x-ref="yearScroll">
                                            <div class="h-20"></div>
                                            <template x-for="year in years" :key="year">
                                                <div class="h-12 flex items-center justify-center snap-center text-lg"
                                                     :class="selectedYear === year ? 'text-[#8BC34A] font-semibold' : 'text-gray-400'"
                                                     x-text="year"></div>
                                            </template>
                                            <div class="h-20"></div>
                                        </div>
                                    </div>

                                    <!-- Age display -->
                                    <div class="flex items-center justify-center gap-2 text-gray-600 mt-8">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span x-text="'Tengo ' + age + ' años'"></span>
                                    </div>

                                    <!-- Hidden input for Livewire -->
                                    <input type="hidden" wire:model="birthDate" x-model="formattedDate">
                                </div>

                            @elseif($currentStep === 4)
                                <!-- STEP 4: GENDER -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cuál es tu género?</h2>
                                    <p class="text-gray-600 text-base">Esto nos ayuda a brindarte mejores insights</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach(['male' => 'Masculino', 'female' => 'Femenino', 'other' => 'Otro', 'prefer_not_to_say' => 'Prefiero no decir'] as $value => $label)
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
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cómo te sientes ahora?</h2>
                                    <p class="text-gray-600 text-base">Califica tu estado de ánimo del 1 (más bajo) al 10 (más alto)</p>
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
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cuál es tu peso?</h2>
                                    <p class="text-gray-600 text-base">Opcional - ayuda a rastrear patrones de salud (en kg)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="weight"
                                    placeholder="Ingresa tu peso en kg..."
                                    step="0.1"
                                    class="w-full px-6 py-4 rounded-full text-lg text-center border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#7C4DFF]">

                            @elseif($currentStep === 7)
                                <!-- STEP 7: HEIGHT (OPTIONAL) -->
                                <div class="text-center mb-8">
                                    <h2 class="text-gray-900 text-3xl font-semibold mb-4">¿Cuál es tu altura?</h2>
                                    <p class="text-gray-600 text-base">Opcional - ayuda a rastrear patrones de salud (en cm)</p>
                                </div>
                                <input
                                    type="number"
                                    wire:model="height"
                                    placeholder="Ingresa tu altura en cm..."
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
                            {{ $currentStep === 7 ? 'Completar' : 'Siguiente' }}
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

        /* Hide scrollbar but keep functionality */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <script>
        function datePicker() {
            return {
                months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                days: Array.from({ length: 31 }, (_, i) => i + 1),
                years: Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - i),
                selectedMonth: 8, // September
                selectedDay: 8,
                selectedYear: 2001,
                age: 23,
                formattedDate: '',

                init() {
                    this.$nextTick(() => {
                        // Scroll to default values
                        this.scrollToMonth(this.selectedMonth);
                        this.scrollToDay(this.selectedDay);
                        this.scrollToYear(this.selectedYear);
                        this.calculateAge();
                        this.updateFormattedDate();
                    });
                },

                scrollToMonth(index) {
                    const scrollElement = this.$refs.monthScroll;
                    scrollElement.scrollTop = index * 48; // 48px per item (h-12)
                },

                scrollToDay(day) {
                    const scrollElement = this.$refs.dayScroll;
                    scrollElement.scrollTop = (day - 1) * 48;
                },

                scrollToYear(year) {
                    const scrollElement = this.$refs.yearScroll;
                    const yearIndex = this.years.indexOf(year);
                    scrollElement.scrollTop = yearIndex * 48;
                },

                updateMonth(event) {
                    const scrollTop = event.target.scrollTop;
                    const index = Math.round(scrollTop / 48);
                    this.selectedMonth = Math.max(0, Math.min(11, index));
                    this.calculateAge();
                    this.updateFormattedDate();
                },

                updateDay(event) {
                    const scrollTop = event.target.scrollTop;
                    const index = Math.round(scrollTop / 48);
                    this.selectedDay = Math.max(1, Math.min(31, index + 1));
                    this.calculateAge();
                    this.updateFormattedDate();
                },

                updateYear(event) {
                    const scrollTop = event.target.scrollTop;
                    const index = Math.round(scrollTop / 48);
                    this.selectedYear = this.years[Math.max(0, Math.min(this.years.length - 1, index))];
                    this.calculateAge();
                    this.updateFormattedDate();
                },

                calculateAge() {
                    const today = new Date();
                    const birthDate = new Date(this.selectedYear, this.selectedMonth, this.selectedDay);
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();

                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }

                    this.age = age;
                },

                updateFormattedDate() {
                    // Format: YYYY-MM-DD
                    const month = String(this.selectedMonth + 1).padStart(2, '0');
                    const day = String(this.selectedDay).padStart(2, '0');
                    this.formattedDate = `${this.selectedYear}-${month}-${day}`;
                }
            }
        }
    </script>
</div>
