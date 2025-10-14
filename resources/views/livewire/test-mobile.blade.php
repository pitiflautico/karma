<div>
    <div class="fixed inset-0 w-full h-full overflow-hidden bg-[#F5F1EB]">
        <!-- Content -->
        <div class="relative z-10 flex flex-col h-full overflow-y-auto">

            <!-- Header -->
            <div class="pt-16 px-6">
                <h1 class="text-gray-900 text-2xl font-bold mb-2">Test Screen - Date Picker</h1>
                <p class="text-gray-600 text-sm mb-4">Debugging iOS-style date selector</p>
                <div class="text-gray-500 text-xs">
                    Selected: <span x-text="formattedDate || 'Not set'"></span>
                </div>
            </div>

            <!-- Date Picker Test -->
            <div class="flex-1 flex items-center justify-center px-6">
                <div class="w-full max-w-md">
                    <div class="text-center mb-8">
                        <h2 class="text-gray-900 text-3xl font-semibold mb-12">¿Cuándo naciste?</h2>
                    </div>

                    <!-- iOS-style Date Picker -->
                    <div class="relative mb-8" x-data="datePicker()" x-init="init()">
                        <!-- Container with selection indicator -->
                        <div class="relative w-full max-w-sm mx-auto">
                            <!-- Selection indicator (green border oval) -->
                            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-12 mx-4 border-2 border-[#8BC34A] rounded-full pointer-events-none z-10"></div>

                            <!-- 3 Column Pickers -->
                            <div class="flex justify-center items-center gap-2 py-4">
                                <!-- Month Picker -->
                                <div class="flex-1 h-60 overflow-y-auto scrollbar-hide picker-column"
                                     x-ref="monthScroll"
                                     @scroll="updateMonth()"
                                     @touchstart="onTouchStart($event, 'month')"
                                     @touchend="onTouchEnd('month')">
                                    <div style="height: 96px;"></div>
                                    <template x-for="(month, index) in months" :key="index">
                                        <div class="h-12 flex items-center justify-center text-base transition-all duration-200"
                                             :class="selectedMonth === index ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400'"
                                             x-text="month"></div>
                                    </template>
                                    <div style="height: 96px;"></div>
                                </div>

                                <!-- Day Picker -->
                                <div class="flex-1 h-60 overflow-y-auto scrollbar-hide picker-column"
                                     x-ref="dayScroll"
                                     @scroll="updateDay()"
                                     @touchstart="onTouchStart($event, 'day')"
                                     @touchend="onTouchEnd('day')">
                                    <div style="height: 96px;"></div>
                                    <template x-for="day in days" :key="day">
                                        <div class="h-12 flex items-center justify-center text-base transition-all duration-200"
                                             :class="selectedDay === day ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400'"
                                             x-text="String(day).padStart(2, '0')"></div>
                                    </template>
                                    <div style="height: 96px;"></div>
                                </div>

                                <!-- Year Picker -->
                                <div class="flex-1 h-60 overflow-y-auto scrollbar-hide picker-column"
                                     x-ref="yearScroll"
                                     @scroll="updateYear()"
                                     @touchstart="onTouchStart($event, 'year')"
                                     @touchend="onTouchEnd('year')">
                                    <div style="height: 96px;"></div>
                                    <template x-for="year in years" :key="year">
                                        <div class="h-12 flex items-center justify-center text-base transition-all duration-200"
                                             :class="selectedYear === year ? 'text-[#8BC34A] font-semibold scale-110' : 'text-gray-400'"
                                             x-text="year"></div>
                                    </template>
                                    <div style="height: 96px;"></div>
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

                        <!-- Debug info -->
                        <div class="mt-8 p-4 bg-white rounded-lg text-xs text-gray-600">
                            <div><strong>Selected Month Index:</strong> <span x-text="selectedMonth"></span></div>
                            <div><strong>Selected Day:</strong> <span x-text="selectedDay"></span></div>
                            <div><strong>Selected Year:</strong> <span x-text="selectedYear"></span></div>
                            <div><strong>Formatted Date:</strong> <span x-text="formattedDate"></span></div>
                            <div><strong>Age:</strong> <span x-text="age"></span></div>
                        </div>

                        <!-- Hidden input for Livewire -->
                        <input type="hidden" wire:model="birthDate" x-model="formattedDate">
                    </div>
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

        /* Picker column improvements */
        .picker-column {
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: y mandatory;
            overscroll-behavior: contain;
            touch-action: pan-y;
        }

        .picker-column > div {
            scroll-snap-align: center;
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initialized, registering datePicker component');

            Alpine.data('datePicker', () => ({
                months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                days: Array.from({ length: 31 }, (_, i) => i + 1),
                years: Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - i),
                selectedMonth: 8, // September (index 8)
                selectedDay: 8,
                selectedYear: 2001,
                age: 23,
                formattedDate: '',
                monthScrollTimeout: null,
                dayScrollTimeout: null,
                yearScrollTimeout: null,
                touchStartY: 0,
                isScrolling: false,

                init() {
                    console.log('Date picker initialized');
                    setTimeout(() => {
                        console.log('Setting initial scroll positions...');
                        // Scroll to default values
                        this.scrollToMonth(this.selectedMonth, false);
                        this.scrollToDay(this.selectedDay - 1, false);
                        this.scrollToYear(this.years.indexOf(this.selectedYear), false);
                        this.calculateAge();
                        this.updateFormattedDate();
                        console.log('Initial state:', {
                            month: this.selectedMonth,
                            day: this.selectedDay,
                            year: this.selectedYear
                        });
                    }, 100);
                },

                onTouchStart(event, type) {
                    this.touchStartY = event.touches[0].clientY;
                    this.isScrolling = true;
                    console.log(`Touch start on ${type}:`, this.touchStartY);
                },

                onTouchEnd(type) {
                    console.log(`Touch end on ${type}`);
                    this.isScrolling = false;
                    // Trigger snap after touch ends with a short delay
                    setTimeout(() => {
                        if (type === 'month') this.updateMonth();
                        if (type === 'day') this.updateDay();
                        if (type === 'year') this.updateYear();
                    }, 50);
                },

                scrollToMonth(index, smooth = true) {
                    const scrollElement = this.$refs.monthScroll;
                    if (scrollElement) {
                        console.log('Scrolling month to index:', index);
                        scrollElement.scrollTo({
                            top: index * 48,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    }
                },

                scrollToDay(index, smooth = true) {
                    const scrollElement = this.$refs.dayScroll;
                    if (scrollElement) {
                        console.log('Scrolling day to index:', index);
                        scrollElement.scrollTo({
                            top: index * 48,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    }
                },

                scrollToYear(index, smooth = true) {
                    const scrollElement = this.$refs.yearScroll;
                    if (scrollElement) {
                        console.log('Scrolling year to index:', index);
                        scrollElement.scrollTo({
                            top: index * 48,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    }
                },

                updateMonth() {
                    clearTimeout(this.monthScrollTimeout);
                    this.monthScrollTimeout = setTimeout(() => {
                        const scrollElement = this.$refs.monthScroll;
                        const scrollTop = scrollElement.scrollTop;
                        const index = Math.round(scrollTop / 48);
                        this.selectedMonth = Math.max(0, Math.min(11, index));
                        console.log('Month updated to:', this.selectedMonth);
                        this.scrollToMonth(index, true);
                        this.calculateAge();
                        this.updateFormattedDate();
                    }, 150);
                },

                updateDay() {
                    clearTimeout(this.dayScrollTimeout);
                    this.dayScrollTimeout = setTimeout(() => {
                        const scrollElement = this.$refs.dayScroll;
                        const scrollTop = scrollElement.scrollTop;
                        const index = Math.round(scrollTop / 48);
                        this.selectedDay = Math.max(1, Math.min(31, index + 1));
                        console.log('Day updated to:', this.selectedDay);
                        this.scrollToDay(index, true);
                        this.calculateAge();
                        this.updateFormattedDate();
                    }, 150);
                },

                updateYear() {
                    clearTimeout(this.yearScrollTimeout);
                    this.yearScrollTimeout = setTimeout(() => {
                        const scrollElement = this.$refs.yearScroll;
                        const scrollTop = scrollElement.scrollTop;
                        const index = Math.round(scrollTop / 48);
                        const clampedIndex = Math.max(0, Math.min(this.years.length - 1, index));
                        this.selectedYear = this.years[clampedIndex];
                        console.log('Year updated to:', this.selectedYear);
                        this.scrollToYear(clampedIndex, true);
                        this.calculateAge();
                        this.updateFormattedDate();
                    }, 150);
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
                    console.log('Formatted date:', this.formattedDate);
                }
            }));
        });
    </script>
    @endpush
</div>
