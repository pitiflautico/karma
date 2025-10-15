<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- STEP WIZARD -->
            <div class="flex-1 flex flex-col">
                <!-- Header with back, step counter, and skip -->
                <div class="pt-16 px-6">
                    <div class="flex items-center justify-between mb-4">
                        <button wire:click="back" class="text-gray-700 text-2xl">←</button>
                        <span class="text-gray-700 text-sm font-medium">Step 3 of 7</span>
                        <button wire:click="skip" class="text-gray-600 text-base font-medium hover:text-gray-800">
                            Skip
                        </button>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (3 / 7) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex items-center justify-center px-6 py-8">
                    <div class="w-full max-w-md">
                        <!-- STEP 3: BIRTH DATE -->
                        <div class="text-center mb-8">
                            <h2 class="text-gray-900 text-3xl font-semibold mb-12">¿Cuándo naciste?</h2>
                        </div>

                        <!-- iOS-style Date Picker -->
                        <div class="relative mb-8"
                            wire:ignore
                            x-data="{
                            months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            days: Array.from({ length: 31 }, (_, i) => i + 1),
                            years: Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - i),
                            selectedMonth: 8,
                            selectedDay: 8,
                            selectedYear: 2001,
                            age: 23,
                            formattedDate: '{{ $birthDate }}',

                            init() {
                                if (this.formattedDate && this.formattedDate !== '') {
                                    const parts = this.formattedDate.split('-');
                                    this.selectedYear = parseInt(parts[0]);
                                    this.selectedMonth = parseInt(parts[1]) - 1;
                                    this.selectedDay = parseInt(parts[2]);
                                }

                                // Sync initial value with Livewire
                                this.updateFormattedDate();

                                setTimeout(() => {
                                    this.scrollToMonth(this.selectedMonth, false);
                                    this.scrollToDay(this.selectedDay - 1, false);
                                    this.scrollToYear(this.years.indexOf(this.selectedYear), false);
                                    this.calculateAge();
                                }, 100);
                            },

                            scrollToMonth(index, smooth = true) {
                                const scrollElement = this.$refs.monthScroll;
                                if (scrollElement) {
                                    scrollElement.scrollTo({
                                        top: index * 36,
                                        behavior: smooth ? 'smooth' : 'auto'
                                    });
                                }
                            },

                            scrollToDay(index, smooth = true) {
                                const scrollElement = this.$refs.dayScroll;
                                if (scrollElement) {
                                    scrollElement.scrollTo({
                                        top: index * 36,
                                        behavior: smooth ? 'smooth' : 'auto'
                                    });
                                }
                            },

                            scrollToYear(index, smooth = true) {
                                const scrollElement = this.$refs.yearScroll;
                                if (scrollElement) {
                                    scrollElement.scrollTo({
                                        top: index * 36,
                                        behavior: smooth ? 'smooth' : 'auto'
                                    });
                                }
                            },

                            updateMonth() {
                                const scrollElement = this.$refs.monthScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                this.selectedMonth = Math.max(0, Math.min(11, index));
                            },

                            updateDay() {
                                const scrollElement = this.$refs.dayScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                this.selectedDay = Math.max(1, Math.min(31, index + 1));
                            },

                            updateYear() {
                                const scrollElement = this.$refs.yearScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                const clampedIndex = Math.max(0, Math.min(this.years.length - 1, index));
                                this.selectedYear = this.years[clampedIndex];
                            },

                            snapMonth() {
                                const scrollElement = this.$refs.monthScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                this.selectedMonth = Math.max(0, Math.min(11, index));
                                this.scrollToMonth(index, true);
                                this.calculateAge();
                                this.updateFormattedDate();
                            },

                            snapDay() {
                                const scrollElement = this.$refs.dayScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                this.selectedDay = Math.max(1, Math.min(31, index + 1));
                                this.scrollToDay(index, true);
                                this.calculateAge();
                                this.updateFormattedDate();
                            },

                            snapYear() {
                                const scrollElement = this.$refs.yearScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 36);
                                const clampedIndex = Math.max(0, Math.min(this.years.length - 1, index));
                                this.selectedYear = this.years[clampedIndex];
                                this.scrollToYear(clampedIndex, true);
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
                                const month = String(this.selectedMonth + 1).padStart(2, '0');
                                const day = String(this.selectedDay).padStart(2, '0');
                                this.formattedDate = `${this.selectedYear}-${month}-${day}`;

                                // Sync with Livewire
                                window.Livewire.find(this.$el.closest('[wire\\:id]').getAttribute('wire:id')).set('birthDate', this.formattedDate);
                            }
                        }" x-init="init()">

                            <!-- Container with selection indicator -->
                            <div class="relative w-full max-w-sm mx-auto">
                                <!-- Selection indicator (green border oval) -->
                                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-9 mx-4 border-2 border-[#8BC34A] rounded-full pointer-events-none z-10"></div>

                                <!-- 3 Column Pickers -->
                                <div class="flex justify-center items-center gap-4 py-4">
                                    <!-- Month Picker -->
                                    <div class="w-20 h-60 overflow-y-auto scrollbar-hide picker-column"
                                         x-ref="monthScroll"
                                         @scroll.passive="updateMonth()"
                                         @scrollend="snapMonth()">
                                        <div style="height: 108px;"></div>
                                        <template x-for="(month, index) in months" :key="index">
                                            <div class="h-9 flex items-center justify-center text-sm transition-all duration-150"
                                                 :class="selectedMonth === index ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400 text-xs'"
                                                 x-text="month"></div>
                                        </template>
                                        <div style="height: 108px;"></div>
                                    </div>

                                    <!-- Day Picker -->
                                    <div class="w-16 h-60 overflow-y-auto scrollbar-hide picker-column"
                                         x-ref="dayScroll"
                                         @scroll.passive="updateDay()"
                                         @scrollend="snapDay()">
                                        <div style="height: 108px;"></div>
                                        <template x-for="day in days" :key="day">
                                            <div class="h-9 flex items-center justify-center text-sm transition-all duration-150"
                                                 :class="selectedDay === day ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400 text-xs'"
                                                 x-text="String(day).padStart(2, '0')"></div>
                                        </template>
                                        <div style="height: 108px;"></div>
                                    </div>

                                    <!-- Year Picker -->
                                    <div class="w-20 h-60 overflow-y-auto scrollbar-hide picker-column"
                                         x-ref="yearScroll"
                                         @scroll.passive="updateYear()"
                                         @scrollend="snapYear()">
                                        <div style="height: 108px;"></div>
                                        <template x-for="year in years" :key="year">
                                            <div class="h-9 flex items-center justify-center text-sm transition-all duration-150"
                                                 :class="selectedYear === year ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400 text-xs'"
                                                 x-text="year"></div>
                                        </template>
                                        <div style="height: 108px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Age display -->
                            <div class="flex items-center justify-center gap-2 text-gray-600 mt-6">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="'Tengo ' + age + ' años'"></span>
                            </div>
                        </div>

                        @error('birthDate')
                            <p class="text-red-500 text-sm mt-4 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Next Button -->
                <div class="p-6">
                    <button
                        wire:click="saveAndContinue"
                        class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                        Siguiente
                    </button>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Picker column improvements - iOS-like momentum scrolling */
        .picker-column {
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: none;
            overscroll-behavior: contain;
            touch-action: pan-y;
            scroll-behavior: auto;
        }
    </style>
</div>
