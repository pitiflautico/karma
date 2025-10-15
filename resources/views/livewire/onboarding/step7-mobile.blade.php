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
                        <span class="text-gray-700 text-sm font-medium">Step 7 of 7</span>
                        <button wire:click="skip" class="text-gray-600 text-base font-medium hover:text-gray-800">
                            Skip
                        </button>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex items-center justify-center px-6 py-8">
                    <div class="w-full max-w-md">
                        <!-- STEP 7: HEIGHT SELECTOR -->
                        <div class="text-center mb-8">
                            <h2 class="text-gray-900 text-3xl font-semibold mb-8">What is your height?</h2>
                        </div>

                        <!-- iOS-style Height Picker -->
                        <div class="relative mb-8" x-data="{
                            heights: [],
                            selectedHeight: {{ $height ?? 160 }},
                            unit: '{{ $unit }}',
                            minHeight: 100,
                            maxHeight: 250,
                            formattedHeight: '{{ $height }}',

                            init() {
                                // Generate height array based on unit
                                this.generateHeights();

                                // Ensure selectedHeight is set to 160 if not already set
                                if (!this.formattedHeight || this.formattedHeight === '') {
                                    this.selectedHeight = 160;
                                }

                                // Sync initial value with Livewire
                                this.updateFormattedHeight();

                                // Scroll to selected height with longer delay to ensure DOM is ready
                                setTimeout(() => {
                                    const index = this.heights.indexOf(this.selectedHeight);
                                    if (index >= 0) {
                                        this.scrollToHeight(index, false);
                                    }
                                }, 300);
                            },

                            generateHeights() {
                                if (this.unit === 'cm') {
                                    this.minHeight = 100;
                                    this.maxHeight = 250;
                                    this.heights = Array.from({ length: this.maxHeight - this.minHeight + 1 }, (_, i) => this.minHeight + i);
                                } else {
                                    this.minHeight = 39;
                                    this.maxHeight = 98;
                                    this.heights = Array.from({ length: this.maxHeight - this.minHeight + 1 }, (_, i) => this.minHeight + i);
                                }
                            },

                            setUnit(newUnit) {
                                if (this.unit === 'cm' && newUnit === 'inch') {
                                    this.selectedHeight = Math.round(this.selectedHeight / 2.54);
                                } else if (this.unit === 'inch' && newUnit === 'cm') {
                                    this.selectedHeight = Math.round(this.selectedHeight * 2.54);
                                }
                                this.unit = newUnit;
                                this.generateHeights();

                                setTimeout(() => {
                                    const index = this.heights.indexOf(this.selectedHeight);
                                    this.scrollToHeight(index, false);
                                }, 50);

                                $wire.set('unit', this.unit);
                                $wire.set('height', this.selectedHeight);
                            },

                            scrollToHeight(index, smooth = true) {
                                const scrollElement = this.$refs.heightScroll;
                                if (scrollElement) {
                                    scrollElement.scrollTo({
                                        top: index * 80,
                                        behavior: smooth ? 'smooth' : 'auto'
                                    });
                                }
                            },

                            updateHeight() {
                                const scrollElement = this.$refs.heightScroll;
                                const scrollTop = scrollElement.scrollTop;
                                const index = Math.round(scrollTop / 80);
                                const clampedIndex = Math.max(0, Math.min(this.heights.length - 1, index));
                                this.selectedHeight = this.heights[clampedIndex];
                                this.scrollToHeight(clampedIndex, true);
                                this.updateFormattedHeight();
                            },

                            updateFormattedHeight() {
                                this.formattedHeight = this.selectedHeight;
                                $wire.set('height', this.selectedHeight);
                            }
                        }" x-init="init()">

                            <!-- Unit Toggle (cm / inch) - Full width -->
                            <div class="mb-12">
                                <div class="flex bg-gray-200 rounded-full p-1">
                                    <button
                                        type="button"
                                        @click="setUnit('cm')"
                                        class="flex-1 py-3 rounded-full text-lg font-medium transition-all"
                                        :class="unit === 'cm' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        cm
                                    </button>
                                    <button
                                        type="button"
                                        @click="setUnit('inch')"
                                        class="flex-1 py-3 rounded-full text-lg font-medium transition-all"
                                        :class="unit === 'inch' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        inch
                                    </button>
                                </div>
                            </div>

                            <!-- Container with selection indicator -->
                            <div class="relative w-full max-w-sm mx-auto">
                                <!-- Selection indicator (green border oval) -->
                                <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 w-64 h-20 border-2 border-[#8BC34A] rounded-full pointer-events-none z-10"></div>

                                <!-- Height Picker -->
                                <div class="w-full h-[400px] overflow-y-auto scrollbar-hide picker-column"
                                     x-ref="heightScroll"
                                     @scroll.debounce.150ms="updateHeight()"
                                     @touchend="updateHeight()">
                                    <div style="height: 160px;"></div>
                                    <template x-for="height in heights" :key="height">
                                        <div class="h-20 flex items-center justify-center text-6xl font-bold transition-all duration-200"
                                             :class="selectedHeight === height ? 'text-[#8BC34A] scale-100' : 'text-gray-400 scale-75 opacity-50'"
                                             x-text="height"></div>
                                    </template>
                                    <div style="height: 160px;"></div>
                                </div>
                            </div>
                        </div>

                        @error('height')
                            <p class="text-red-500 text-sm mt-4 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Continue Button -->
                <div class="p-6">
                    <button
                        wire:click="saveAndContinue"
                        class="w-full bg-[#7C4DFF] text-white font-semibold py-4 px-6 rounded-full text-lg shadow-lg hover:bg-[#6A3DE8] transition-all">
                        Continue →
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
</div>
