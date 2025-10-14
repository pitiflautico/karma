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
                        <span class="text-gray-700 text-sm font-medium">Step 6 of 7</span>
                        <button wire:click="skip" class="text-gray-600 text-base font-medium hover:text-gray-800">
                            Skip
                        </button>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-[#7C4DFF] rounded-full h-2 transition-all duration-300" style="width: {{ (6 / 7) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 flex flex-col justify-center py-8">
                    <div class="w-full">
                        <!-- STEP 6: WEIGHT SELECTOR -->
                        <div class="text-center mb-12 px-6">
                            <h2 class="text-gray-900 text-4xl font-bold">What is your weight?</h2>
                        </div>

                        <!-- Weight Selector Widget -->
                        <div x-data="{
                            weight: {{ $weight }},
                            unit: '{{ $unit }}',
                            minWeight: 20,
                            maxWeight: 200,
                            offset: 0,
                            isDragging: false,
                            startX: 0,
                            startOffset: 0,
                            pixelsPerUnit: 10,

                            get displayWeight() {
                                return Math.round(this.weight);
                            },

                            init() {
                                // Initialize offset based on weight
                                // Offset is negative: we move the ruler to the left to show higher weights
                                this.offset = -(this.weight - this.minWeight) * this.pixelsPerUnit;
                                $wire.set('weight', this.weight);
                                $wire.set('unit', this.unit);
                            },

                            setUnit(newUnit) {
                                if (this.unit === 'kg' && newUnit === 'lbs') {
                                    this.weight = this.weight * 2.20462;
                                    this.maxWeight = 440;
                                } else if (this.unit === 'lbs' && newUnit === 'kg') {
                                    this.weight = this.weight / 2.20462;
                                    this.maxWeight = 200;
                                }
                                this.unit = newUnit;
                                this.offset = -(this.weight - this.minWeight) * this.pixelsPerUnit;
                                $wire.set('unit', this.unit);
                                $wire.set('weight', this.weight);
                            },

                            handleStart(e) {
                                this.isDragging = true;
                                const touch = e.touches ? e.touches[0] : e;
                                this.startX = touch.clientX;
                                this.startOffset = this.offset;
                            },

                            handleMove(e) {
                                if (!this.isDragging) return;
                                const touch = e.touches ? e.touches[0] : e;
                                const deltaX = touch.clientX - this.startX;
                                this.offset = this.startOffset + deltaX;

                                // Calculate weight from offset - update in real-time
                                const calculatedWeight = this.minWeight - (this.offset / this.pixelsPerUnit);
                                this.weight = Math.max(this.minWeight, Math.min(this.maxWeight, calculatedWeight));
                            },

                            handleEnd() {
                                this.isDragging = false;
                                // Snap to nearest integer and sync
                                this.weight = Math.round(this.weight);
                                this.offset = -(this.weight - this.minWeight) * this.pixelsPerUnit;
                                $wire.set('weight', this.weight);
                            }
                        }" x-init="init()">

                            <!-- Unit Toggle (lbs / kg) - Full width -->
                            <div class="px-6 mb-12">
                                <div class="flex bg-gray-200 rounded-full p-1">
                                    <button
                                        type="button"
                                        @click="setUnit('lbs')"
                                        class="flex-1 py-3 rounded-full text-lg font-medium transition-all"
                                        :class="unit === 'lbs' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        lbs
                                    </button>
                                    <button
                                        type="button"
                                        @click="setUnit('kg')"
                                        class="flex-1 py-3 rounded-full text-lg font-medium transition-all"
                                        :class="unit === 'kg' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'">
                                        kg
                                    </button>
                                </div>
                            </div>

                            <!-- Weight Display -->
                            <div class="text-center mb-16 px-6">
                                <span class="text-8xl font-bold text-[#5D4037]" x-text="displayWeight"></span>
                                <span class="text-5xl font-normal text-gray-600" x-text="unit"></span>
                            </div>

                            <!-- Scale Slider - draggable ruler -->
                            <div class="relative overflow-hidden" style="height: 120px;">
                                <!-- Fixed center indicator (green line) -->
                                <div class="absolute left-1/2 top-0 w-1 h-20 bg-[#8BC34A] z-10" style="transform: translateX(-50%);"></div>

                                <!-- Draggable ruler -->
                                <div
                                    class="absolute top-0 h-full select-none cursor-grab active:cursor-grabbing flex items-start"
                                    :style="`left: 50%; transform: translateX(${offset}px);`"
                                    @mousedown="handleStart($event)"
                                    @mousemove="handleMove($event)"
                                    @mouseup="handleEnd()"
                                    @mouseleave="handleEnd()"
                                    @touchstart.prevent="handleStart($event)"
                                    @touchmove.prevent="handleMove($event)"
                                    @touchend="handleEnd()">

                                    <!-- Scale marks -->
                                    <template x-for="i in (maxWeight - minWeight + 1)" :key="i">
                                        <div class="flex flex-col items-center" style="width: 10px;">
                                            <!-- Tick mark -->
                                            <div
                                                class="bg-gray-400"
                                                :class="(minWeight + i - 1) % 10 === 0 ? 'h-16 w-0.5' : 'h-8 w-px'">
                                            </div>
                                            <!-- Label for every 10th mark -->
                                            <span
                                                x-show="(minWeight + i - 1) % 10 === 0"
                                                class="text-xs text-gray-600 mt-1"
                                                x-text="minWeight + i - 1">
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            @error('weight')
                                <p class="text-red-500 text-sm mt-4 text-center">{{ $message }}</p>
                            @enderror
                        </div>
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
</div>
